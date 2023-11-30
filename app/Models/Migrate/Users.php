<?php

namespace Models\Migrate;

use MaplePHP\Query\AbstractMigrate;

class Users extends AbstractMigrate
{
    public function __construct()
    {
        parent::__construct("users");
    }

    protected function buildTable(): void
    {
        $this->mig->column("id", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "primary",
            "ai" => true

        ])->column("oid", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "index",
            "default" => 0,
            "fk" => [
                [
                    "table" => "organizations",
                    "column" => "org_id",
                    "update" => "cascade",
                    "delete" => "cascade",
                    "enabled" => false
                ]
            ]

        ])->column("firstname", [
            "type" => "varchar",
            "length" => 80,
            "collate" => true,
            "default" => ""

        ])->column("lastname", [
            "type" => "varchar",
            "length" => 120,
            "collate" => true,
            "default" => ""

        ])->column("phone", [
            "type" => "varchar",
            "length" => 30,
            "collate" => true,
            "default" => ""

        ])->column("extra", [
            "type" => "text",
            "collate" => true,
            "null" => true

        ])->column("visible", [
            "type" => "tinyint",
            "length" => 1,
            "index" => "index",
            "default" => 0

        ])->column("role", [
            "type" => "int",
            "length" => 2,
            "index" => "index",
            "default" => 4

        ])->column("position", [
            "type" => "int",
            "length" => 11,
            "default" => 0

        ])->column("create_date", [
            "type" => "datetime",
            "default" => "2000-01-01 00:00:00"
        ]);
    }
}
