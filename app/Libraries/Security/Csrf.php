<?php

namespace MaplePHP\Security;

use MaplePHP\Http\Interfaces\CookiesInterface;
use MaplePHP\Security\GenerateKey;

class Csrf
{
    public const INP_FIELD_NAME = "csrfToken";
    public const EXPIRES = 60 * 15;

    private $token;
    private $cookie;

    /**
     * Start CSRF validation instance
     * @param CookiesInterface|null $cookie
     */
    public function __construct(?CookiesInterface $cookie = null)
    {
        $this->cookie = $cookie;
        if (!is_null($this->cookie) && !$this->hasSession()) {
            if (!$this->cookie->isSecure()) {
                throw new \Exception("Either make sure session is started OR that the cookie settings is more " .
                    "secure (strict) e.g. set (samesite=Strict, secure=true, httponly=true)", 1);
            }
        }
    }

    /**
     * Validates token on form post
     * @param string|null $token
     * @return bool
     */
    public function isValid(?string $token): bool
    {
        return ($this->getCookie("csrftoken") === $token);
    }

    /**
     * Validates token time on form post
     * @return bool
     */
    public function isRecent(): bool
    {
        if (is_null($this->cookie)) {
            if (isset($_SESSION['csrftoken_time'])) {
                return (($_SESSION['csrftoken_time'] + static::EXPIRES) >= time());
            }
            $this->destroytoken();
            return false;
        }
        return true;
    }

    /**
     * Get current token
     * @return string|NULL
     */
    public function getToken(): ?string
    {
        return (!is_null($this->token) ? $this->token : $this->getCookie("csrftoken"));
    }

    /**
     * Get token / or generate and get if empty
     * @return string
     */
    public function token(): string
    {
        if (is_null($this->token)) {
            $this->token = $this->createToken();
        }
        return $this->token;
    }

    /**
     * Create and set CSRF Token to SESSION
     * @return string Token string
     */
    public function createToken(): string
    {
        if (is_null($this->token)) {
            $this->token = $this->generate();
            $this->setCookie("csrftoken", $this->token);
            if ($this->hasSession()) {
                $_SESSION['csrftoken_time'] = time();
            }
        }
        return $this->token;
    }

    /**
     * Destroy the CSRF token
     * @return void
     */
    public function destroyToken(): void
    {
        $this->deleteCookie("csrftoken");
        $this->deleteCookie("csrftoken_time");
    }

    /**
     * Create CSRF token and return the hidden input field has HTML
     * @return string
     */
    public function tokenTag(): string
    {
        $token = $this->createToken();
        return "<input class=\"inp-csrf-token\" type=\"hidden\" name=\"" .
        static::INP_FIELD_NAME . "\" value=\"{$token}\">";
    }

    /**
     * Create CSRF token and return the hidden input field as Array
     * @return array
     */
    public function tokenField(): array
    {
        $token = $this->createToken();
        return [
            static::INP_FIELD_NAME => [
                "type" => "hidden",
                "attr" => [
                    "class" => "inp-csrf-token"
                ],
                "value" => $token
            ]
        ];
    }

    /**
     * Generate a random token string
     * @return string
     */
    protected function generate(): string
    {
        $generateKey = new GenerateKey();
        return $generateKey->char32();
    }

    /**
     * Get cookie
     * Will get cookie and use the best right and secure technique depending on your setup
     * @param string $key   The cookie key
     * @return string|null
     */
    protected function getCookie(string $key): ?string
    {
        if (!is_null($this->cookie)) {
            return $this->cookie->get($key);
        }
        return ($_SESSION[$key] ?? null);
    }

    /**
     * Set cookie
     * Will set cookie and use the best right and secure technique depending on your setup
     * @param string $key   The cookie key
     * @param string $value The cookie value
     * @return void
     */
    protected function setCookie(string $key, string $value): void
    {
        if (!is_null($this->cookie)) {
            $this->cookie->set($key, $value, time() + static::EXPIRES);
        } else {
            $_SESSION[$key] = $value;
        }
    }


    /**
     * Delete cookie
     * @param string $key   The cookie key
     * @return void
     */
    protected function deleteCookie(string $key): void
    {
        if (!is_null($this->cookie)) {
            $this->cookie->delete($key);
        } else {
            $_SESSION[$key] = null;
        }
    }

    /**
     * Check if session has started
     * @return bool
     */
    protected function hasSession(): bool
    {
        return (session_status() === PHP_SESSION_ACTIVE);
    }
}
