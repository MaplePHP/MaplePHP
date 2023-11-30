<?php
declare(strict_types=1);

namespace MaplePHP\Security;

class Session
{
    public const LOGIN_KEY = "user_id";

    private $sessionID;
    private $sessionName;
    private $length;
    private $dir;
    private $host;
    private $ssl;
    private $flag;
    private $key;
    private $samesite = "Strict";

    public function __construct(
        $name = false,
        $length = 360,
        $dir = "/",
        $host = false,
        $ssl = false,
        $flag = true
    ) {
        $this->sessionName = $name;
        $this->length = $length;
        $this->dir = $dir;
        $this->host = $host;
        $this->ssl = $ssl;
        $this->flag = $flag;
    }

    /**
     * Start session
     * @return self
     */
    public function start(): self
    {
        $this->startSession($this->sessionName, $this->length, $this->dir, $this->host, $this->ssl, $this->flag);
        return $this;
    }

    /**
     * Add to session
     * @param string $key
     * @param mixed  $value
     */
    public function add(string $key, mixed $value): self
    {
        if (!is_null($this->key)) {
            $_SESSION[$this->key][$key] = $value;
        } else {
            $_SESSION[$key] = $value;
        }
        return $this;
    }

    /**
     * Get session item
     * @param  string   $key item key
     * @param  string    $default  Set default value
     * @return mixed
     */
    public function get(string $key, string $default = ""): mixed
    {
        if (!is_null($this->key)) {
            return isset($_SESSION[$this->key][$key]) ? $_SESSION[$this->key][$key] : $default;
        }
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Delete session item
     * @param  string $key item key
     * @return void
     */
    public function delete(string $key): void
    {
        if (!is_null($this->key)) {
            if (isset($_SESSION[$this->key][$key])) {
                unset($_SESSION[$this->key][$key]);
            }
        } else {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }
    }

    /**
     * Create a login session
     * @param string $value
     * @return self
     */
    public function setLogin(string $value): self
    {
        $this->add($this::LOGIN_KEY, $value);
        $this->regenerate();
        return $this;
    }

    /**
     * Check if logged in
     * @return string|false
     */
    public function loggedIn(): string|false
    {
        $getKey = $this->get($this::LOGIN_KEY);
        return (!is_null($getKey) && $this->get("session_id") === $this->idHash()) ? $getKey : false;
    }

    /**
     * Get the main session ID - Could be a "user ID"
     * @return string|null
     */
    public function getByKey(): ?string
    {
        return $this->get($this::LOGIN_KEY);
    }

    /**
     * Regenerate session
     * @param  bool $destroyOld remove old session (FALSE will logout the user)
     * @return self
     */
    public function regenerate(bool $destroyOld = true): self
    {
        // Sometime you want to keep old session if you forexample want to save cart data to the sesssion id in DB.
        session_regenerate_id($destroyOld);
        $this->sessionID = session_id();
        if ($destroyOld) {
            $this->setCert();
        }
        return $this;
    }

    /**
     * Get session ID
     * @return string
     */
    public function getID(): string
    {
        return $this->sessionID;
    }

    /**
     * Change the session name
     * @param string $name
     * @return void
     */
    protected function setName(string $name): void
    {
        //$this->clear();
        $this->setCert();
        session_name($name);
    }

    /**
     * Clear session
     * @return self
     */
    public function clear(): self
    {
        unset($_SESSION);
        session_destroy();
        return $this;
    }

    /**
     * Nested key session
     * @param string $key key
     * @return self
     */
    public function key(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Same site key
     * @param bool $enable key (false=Lax, true=Strict)
     * @return self
     */
    public function samesite(bool $enable): self
    {
        $this->samesite = ($enable) ? "Strict" : "Lax";
        return $this;
    }


    /**
     * Create a ssession ID
     * @return string
     */
    public function createID(): string
    {
        $this->sessionID = session_create_id();
        return $this->sessionID;
    }

    /**
     * Start session
     * @param  string|null $name
     * @param  int|integer $length
     * @param  string      $dir
     * @param  string|null $host
     * @param  bool|null   $ssl
     * @param  bool|null   $flag
     * @return void
     */
    private function startSession(
        ?string $name = null,
        int $length = 360,
        ?string $dir = "/",
        ?string $host = null,
        ?bool $ssl = null,
        ?bool $flag = null
    ): void {
        $length = $length * 60;

        // This should be called on every request and in exacly this order
        if (is_null($this->sessionID)) {
            /*
            if (!is_null($this->sessionID)) {
                session_write_close();
            }
             */
            if (!is_null($name)) {
                $this->setName($name);
            }

            if (PHP_VERSION_ID < 70300) {
                session_set_cookie_params($length, $dir.'; samesite='.$this->samesite, $host, $ssl, $flag);
            } else {
                session_set_cookie_params([
                    'lifetime' => $length,
                    'path' => $dir,
                    'domain' => $host,
                    'secure' => $ssl,
                    'httponly' => $flag,
                    'samesite' => $this->samesite
                ]);
            }
            session_start();
            $this->sessionID = session_id();
            // Using a different technique
            //if(!$this->get("uuid")) $this->add("uuid", uniqid(mt_rand(1000,9999)));
        }
    }

    /**
     * Set certificate hash, that will later prevent all session injections
     * @return void
     */
    private function setCert(): void
    {
        $this->add("session_time", time());
        $this->add("session_id", $this->idHash());
    }

    /**
     * Certificate hash, will prevent all session injections
     * @return string hash
     */
    private function idHash(): string
    {
        $key1 = $this->get($this::LOGIN_KEY);
        $key2 = $this->get("session_time");
        $ipAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        return sha1($this->sessionID . $this->sessionName . $key1 . $key2 . $ipAddr);
    }

    /*
    private function _sane($name) {
        if(!isset($_COOKIE[$name])) $_COOKIE[$name] = session_create_id();
        return $_COOKIE[$name];
    }
     */
}
