<?php

namespace database\migrations;

use Exception;
use MaplePHP\Query\AbstractMigrate;

class Test extends AbstractMigrate
{
    /**
     * @throws Exception
     */
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

        ])->column("name", [
            "type" => "varchar",
            "length" => 160,
            "collate" => true

        ])->column("content", [
            "type" => "text",
            "collate" => true

        ])->column("status", [
            "type" => "int",
            "length" => 11,
            "index" => "index",
            "default" => 0

        ])->column("parent", [
            "type" => "int",
            "length" => 11,
            "index" => "index",
            "default" => 0

        ])->column("create_date", [
            "type" => "datetime",
            "default" => "2000-01-01 00:00:00"
        ]);
    }
}
