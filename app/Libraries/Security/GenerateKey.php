<?php

namespace MaplePHP\Security;

class GenerateKey
{
    public const CHARSET = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

    /**
     * Create a random generate stings
     */
    public function __construct()
    {
    }

    /**
     * Generate a char 32 random token string
     * @return string
     */
    public function char32(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Generate a char 32 random token string
     * @return string
     */
    public function char64(): string
    {
        return bin2hex(random_bytes(32));
    }


    /**
     * Generate UUID char(36)
     * NOTIS: It is possible but highly unlikly that if users login in within 1 second of each other and a long set
     * of rand keys randomly gets the same for duplicate UUID to be generated. But like I said it is higly unlikly.
     * @return string
     */
    public function uuid(): string
    {
        return sprintf(
            '%s-%s-%04x-%04x-%s',
            substr(md5(uniqid($this->randStrScalarArg(), true)), 0, 8),
            substr(md5(uniqid($this->randStrScalarArg(), true)), 8, 4),
            $this->randStrScalarArg(0, 65535),
            $this->randStrScalarArg(0, 65535),
            substr(md5(uniqid($this->randStrScalarArg(), true)), 12, 8)
        );
    }

    /**
     * Return right scalar (preparing for a scalar class)
     * @param  int|null $min
     * @param  int|null $max
     * @return string
     */
    private function randStrScalarArg(?int $min = null, ?int $max = null): string
    {
        if (!is_null($min) && !is_null($max)) {
            return (string)mt_rand($min, $max);
        }
        return (string)mt_rand();
    }

    /**
     * Get random string from a set of random charcters
     * @example  GenerateKey->fromSet(GenerateKey::CHARSET, 10);
     * @param  string $set    Set of random characters
     * @param  int    $length Leng of generated key
     * @return string
     */
    public function fromSet(string $set, int $length): string
    {
        $key = "";
        for ($i = 0; $i < $length; $i++) {
            $key .= $this->getRandChar($set);
        }
        return $key;
    }

    /**
     * Returns one random character from a set of characters
     * @param  string $set set of characters
     * @return string
     */
    private function getRandChar(string $set): string
    {
        $rand = (mt_rand(0, (strlen($set) - 1)));
        return $set[$rand];
    }
}
