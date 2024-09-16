<?php

/**
 * @Package:    MaplePHP - Error handler framework
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Blunder;

use Exception;

class Builder
{
    const VALID_ARGS = ['type'];
    private array $args = [];
    private array $row = [];

    private string $sql = "";

    public function __construct()
    {
    }

    /**
     * Add column to row
     * @param string $key
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function add(string $key, array $data): self
    {
        // Validate ARGS
        foreach($data as $argName => $_value) {
            if(!in_array($argName, static::VALID_ARGS)) {
                throw new Exception("The argument '{$argName}' is not supported.");
            }
        }

        if(empty($data['type'])) {
            throw new Exception("The argument 'type' is missing.");
        }

        $this->row[$key] = $data;
        return $this;
    }

    /**
     * Get a column in row
     * @param string $key
     * @return array|null
     */
    public function getColumn(string $key): ?array
    {
        return $this->row[$key] ?? null;
    }

    /**
     * Get whole row
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * Build row data
     * @return void
     */
    public function build(): void
    {

        $this->sql = "";

        $this->sql .= "START TRANSACTION;\n\n";
        $this->sql .= "SET FOREIGN_KEY_CHECKS=0;\n";


        // HMM Should be 1 even when
        $this->build .= "SET FOREIGN_KEY_CHECKS=1;\n\n";
        $this->build .= "COMMIT;";

    }

}
