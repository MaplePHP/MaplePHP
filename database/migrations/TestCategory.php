<?php

namespace database\migrations;

use Exception;
use MaplePHP\Query\AbstractMigrate;

class TestCategory extends AbstractMigrate
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        // Table name
        parent::__construct("test_category");
    }

    protected function migrateTable(): void
    {
        //$this->mig->drop();
        $this->mig->column("cat_id", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "primary",
            "ai" => true

        ])->column("tid", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "index",
            "default" => 0,
            "fk" => [
                [
                    "table" => "test",
                    "column" => "id",
                    "update" => "cascade",
                    "delete" => "cascade",
                    "enabled" => false
                ]
            ]

        ])->column("name", [
            "type" => "varchar",
            "length" => 30,
            "collate" => true
        ]);
    }
}
