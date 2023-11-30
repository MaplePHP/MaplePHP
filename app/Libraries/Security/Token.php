<?php

namespace MaplePHP\Security;

use DateTimeInterface;
use MaplePHP\Http\Interfaces\CookiesInterface;
use MaplePHP\Query\Interfaces\DBInterface;
use MaplePHP\Security\GenerateKey;

class Token
{
    public const TABLE = "users_token";
    public const COOKIE_PREFIX = "maple_token_";
    public const EXPIRES = 2592000; // 30 days

    public const REMEMBER = 1;
    public const FORGOT = 2;
    public const API = 3;

    private $tokenType;
    private $name;
    private $cookies;
    private $database;
    private $token;
    private $date;
    private $expires;

    /**
     * Create and validate a remember me token
     * @param int               $tokenType    Unique cookie name
     * @param CookiesInterface  $cookies (PSR)
     * @param DateTimeInterface $date    (DateTime)
     */
    public function __construct(int $tokenType, CookiesInterface $cookies, DBInterface $database, DateTimeInterface $date)
    {
        $this->tokenType = $tokenType;
        $this->name = static::COOKIE_PREFIX . $tokenType;
        $this->cookies = $cookies;
        $this->database = $database;
        $this->date = $date;
        $this->expires = static::EXPIRES;
    }

    /**
     * Change the default experation date
     * @param int $expires TTL (Seconds)
     */
    public function setExpires(int $expires): void
    {
        $this->expires = $expires;
    }

    /**
     * Insert a token to TB column
     * @param  int    $userId     User id
     * @return bool
     */
    public function generate(int $userId, bool $setCookie = true): bool
    {
        $token = $this->uuid();
        $date = date("Y-m-d H:i:s", $this->date->getTimestamp());
        $expires = date("Y-m-d H:i:s", ($this->date->getTimestamp() + $this->expires));
        $insert = $this->database::insert(static::TABLE)->set(["token" => $token, "user_id" => $userId,
            "token_type" => $this->tokenType, "expires_date" => $expires, "generate_date" => $date]);
        $insert->limit(1)->onDupKey();


        if ($insert->execute()) {
            $this->setToken($token, $setCookie);
            return true;
        }
        return false;
    }

    /**
     * Disable token in TB column
     * @param  int    $userId     User id
     * @return void
     */
    public function disable(int $userId): void
    {
        $expires = date("Y-m-d H:i:s", $this->date->getTimestamp());
        $update = $this->database::update(static::TABLE)->set(["user_id" => $userId, "token_type" => $this->tokenType,
            "expires_date" => $expires]);
        $update->limit(1);
        $this->deleteToken();
    }

    /**
     * Get token objects
     * @return null|string
     */
    public function getToken(): ?string
    {
        if (is_null($this->token)) {
            $this->token = $this->cookies->get($this->name);
        }
        return $this->token;
    }

    /**
     * Get token type
     * @return int
     */
    public function getTokenType(): int
    {
        return $this->tokenType;
    }

    /**
     * Set token objects
     * @param string $token
     */
    public function setToken(string $token, bool $setCookie = true): void
    {
        $this->token = $token;
        if ($setCookie) {
            $this->cookies->set($this->name, $token, ($this->date->getTimestamp() + $this->expires));
        }
    }

    /**
     * Delete token objects
     * @return void
     */
    public function deleteToken(): void
    {
        $this->token = null;
        $this->cookies->delete($this->name);
    }

    /**
     * Validate token.
     * NOTIS: If more than 1 user has the same token then clear it. It is possible but it is higly improbable
     * for duplicate tokens. It is only possible if users login in within 1 second of each other + a long set
     * of rand keys randomly gets the same. So like I said it is higly unlikly
     * @return int|bool  (user id or false)
     */
    public function validate(): int|bool
    {
        if ($token = $this->getToken()) {
            $date = date("Y-m-d H:i:s", $this->date->getTimestamp());
            $select = $this->database::select("user_id", static::TABLE)
            ->where("token", $token)
            ->where("expires_date", $date, ">=")
            ->limit(1);
            $result = $select->execute();
            if ($result && $result->num_rows > 0) {
                // In case of a token is NOT unique the remove it. (It is higly unlikly!)
                if ($result->num_rows > 1) {
                    $this->deleteToken();
                    return false;
                }

                $obj = $result->fetch_object();
                return (int)$obj->user_id;
            }
        }
        return false;
    }

    /**
     * Generate a UUID token
     * @return string
     */
    final protected function uuid(): string
    {
        return $this->database::getUUID();
    }
}
