<?php

namespace Models\Migrate;

use MaplePHP\Query\AbstractMigrate;

class Login extends AbstractMigrate
{
    public function __construct()
    {
        parent::__construct("login");
    }

    protected function buildTable(): void
    {

        $this->mig->column("login_id", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "primary",
            "ai" => true

        ])->column("user_id", [
            "type" => "int",
            "length" => 11,
            "index" => "index",
            "attr" => "unsigned",
            "fk" => [
                [
                    "table" => "users",
                    "column" => "id",
                    "update" => "cascade",
                    "delete" => "cascade"
                ]
            ]

        ])->column("email", [
            "type" => "varchar",
            "length" => 160,
            "index" => "index",
            "collate" => true

        ])->column("hashed_password", [
            "type" => "varchar",
            "length" => 60,
            "collate" => true

        ])->column("status", [
            "type" => "int",
            "length" => 1,
            "default" => 0

        ])->column("logged_in", [
            "type" => "datetime",
            "default" => "2000-01-01 00:00:00"
        ]);
    }
}
