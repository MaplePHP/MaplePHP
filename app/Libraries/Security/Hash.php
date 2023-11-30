<?php

namespace MaplePHP\Security;

class Hash
{
    private $value;

    /**
     * Create a secure Blowfish hash (Used mostly for passwords)
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Verify if construct value matches the argument hashed value
     * @param  string $hashedValue
     * @return bool
     */
    public function verify(string $hashedValue): bool
    {
        if (password_verify($this->value, $hashedValue)) {
            return true;
        }
        return false;
    }

    // Same as above (use the one you rember)
    public function passwordVerify(string $hashedValue): bool
    {
        return $this->verify($hashedValue);
    }

    /**
     * Hash and return the value hashed value
     * @param  int $cost The algorithmic cost that should be used.
     * @return string
     */
    public function hash(int $cost = 8): string
    {
        return password_hash($this->value, PASSWORD_BCRYPT, array("cost" => $cost));
    }

    // Same as above (use the one you rember)
    public function passwordHash(int $cost = 8): string
    {
        return $this->hash($cost);
    }
}
