<?php

namespace Models\Migrate;

use MaplePHP\Query\AbstractMigrate;

class UsersToken extends AbstractMigrate
{
    public function __construct()
    {
        parent::__construct("users_token");
    }

    protected function buildTable(): void
    {

        // If you want to add multiple primary keys
        //->primary(["id1", "id2"])

        // Or if you need just one primary key, like most cases:
        $this->mig->column("user_id", [
            "type" => "int",
            "length" => 11,
            "index" => "primary"

        ])->column("token_type", [
            "type" => "tinyint",
            "length" => 3,
            "index" => "primary"

        ])->column("token", [
            "type" => "char",
            "length" => 36,
            "index" => "index"

        ])->column("scopes", [
            "type" => "varchar",
            "length" => 80,
            "collate" => true,
            "default" => ""

        ])->column("expires_date", [
            "type" => "datetime",
            "index" => "index",
            "default" => "2000-01-01 00:00:00"

        ])->column("generate_date", [
            "type" => "datetime",
            "default" => "2000-01-01 00:00:00"
        ]);

        /*
        // You can also rename a column multiple time over a longer period
        // But... Even tho it works this should only be used if a table "already" exists,
        // This becouse you want to keep the migration file as clear as possible!

        ->column("loremname_1", [
            // Rename: IF old column name is "loremname_1" or "loremname_2" to "permalink"
            "rename" => ["loremname_2", "permalink"],
            "type" => "varchar",
            "index" => "index",
            "length" => 200,
            "collate" => true
        ]);
         */
    }
}
