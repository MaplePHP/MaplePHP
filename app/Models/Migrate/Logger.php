<?php

namespace Models\Migrate;

use MaplePHP\Query\AbstractMigrate;

class Logger extends AbstractMigrate
{
    public function __construct()
    {
        parent::__construct("logger");
    }

    protected function buildTable(): void
    {
        $this->mig->column("id", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "primary",
            "ai" => true

        ])->column("user_id", [
            "type" => "int",
            "length" => 11,
            "index" => "index",
            "default" => 0

        ])->column("level", [
            "type" => "varchar",
            "length" => 30,
            "collate" => true

        ])->column("message", [
            "type" => "text",
            "collate" => true

        ])->column("data", [ // IF json data is here
            "type" => "text",
            "collate" => true,
            "null" => true

        ])->column("logged_in", [
            "type" => "datetime",
            "default" => "2000-01-01 00:00:00"

        ]);
    }
}
