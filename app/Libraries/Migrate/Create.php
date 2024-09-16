<?php

/**
 * @Package:    MaplePHP - Error handler framework
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Blunder;


use MaplePHP\Query\Interfaces\HandlerInterface;

class Create
{
    private HandlerInterface $conn;

    public function __construct(HandlerInterface $conn)
    {

        $this->conn = $conn;
    }





}
