<?php

/**
 * @Package:    MaplePHP - Error handler framework
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Blunder;


class Create
{

    const TYPES = [
        "INT", "VARCHAR", "TEXT", "DATE" // ???
    ];

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    static public function setValue(string $value): self
    {
        return new self($value);
    }

    /**
     * Validate type
     * @return bool
     */
    public function validateType(): bool
    {
        return in_array($this->value, self::TYPES);
    }

}
