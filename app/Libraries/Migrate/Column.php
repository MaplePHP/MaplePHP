<?php

namespace MaplePHP\Blunder;

class Column
{

    const VALID_ARGS = ['type'];

    private array $data;

    function __construct(array $data)
    {
        $this->data = $data;
    }


    function validateData() {

    }

}