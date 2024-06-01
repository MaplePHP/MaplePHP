<?php

namespace database\migrations;

use MaplePHP\Query\AbstractMigrate;

class Test extends AbstractMigrate
{
    public function __construct()
    {
        // Table name
        parent::__construct("test");
    }

    protected function migrateTable(): void
    {
        //$this->mig->drop();

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

        ])->column("test", [
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
