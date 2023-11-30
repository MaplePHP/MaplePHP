<?php

namespace Models\Migrate;

use MaplePHP\Query\AbstractMigrate;

class Organizations extends AbstractMigrate
{
    public function __construct()
    {
        parent::__construct("organizations");
    }

    protected function buildTable(): void
    {
        $this->mig->column("org_id", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "primary",
            "ai" => true

        ])->column("org_name", [
            "type" => "varchar",
            "length" => 80,
            "collate" => true,
            "default" => ""

        ])->column("org_create_date", [
            "type" => "datetime",
            "default" => "2000-01-01 00:00:00"
        ]);
    }
}
