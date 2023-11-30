<?php

namespace Models\Migrate;

use MaplePHP\Query\AbstractMigrate;

class ExampleTable extends AbstractMigrate
{
    public function __construct()
    {
        parent::__construct("example_table");
    }

    protected function buildTable(): void
    {

        // If you want to add multiple primary keys
        //->primary(["id1", "id2"])

        // Or if you need just one primary key, like most cases:
        $this->mig->column("id1", [
            "type" => "int",
            "length" => 11,
            "attr" => "unsigned",
            "index" => "primary",
            "ai" => true

        ])->column("user_id", [
            "type" => "int",
            "length" => 11,
            "index" => "index"

        ])->column("name", [
            "type" => "varchar",
            "length" => 160,
            "index" => "index",
            "collate" => true // Auto add UTF8

        ])->column("content", [
            "type" => "text",
            "collate" => true,
            "null" => true

        ])->column("json_data", [ // IF json data is here
            "type" => "text",
            "collate" => true,
            "null" => true

        ])->column("sku", [
            // This will extract json column data (articleNumber, colorCode, sizeCode) from the column "json_data"
            // And then combine it with string, the end result will be: SKU665425-0001-46
            "type" => "varchar",
            "collate" => true,
            "generated" => [
                "json_columns" => "json_data", // from then auto json extract
                "columns" => "SKU{{articleNumber}}-{{colorCode}}-{{sizeCode}}",
            ],
            "index" => "index",
            "default" => "",

        ])->column("token", [
            "type" => "char",
            "length" => 36,
            "null" => true

        ])->column("status", [
            "type" => "tinyint",
            "length" => 1,
            "index" => "index",
            "default" => 0

        ])->column("position", [
            "type" => "int",
            "length" => 1,
            "default" => 0,
            "drop" => true // Will be dropped if exists

        ])->column("logged_in", [
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
