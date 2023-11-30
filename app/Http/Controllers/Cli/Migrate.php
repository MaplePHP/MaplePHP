<?php

namespace Http\Controllers\Cli;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Query\Exceptions\QueryCreateException;
use Http\Controllers\Cli\CliInterface;
use Services\Stream\Cli as Stream;
use Services\Migration;

class Migrate implements CliInterface
{
    protected $container;
    protected $args;
    protected $table;
    protected $cli;
    protected $migrate;
    protected $mig;
    protected $migName;

    public function __construct(ContainerInterface $container, RequestInterface $request, Stream $cli, Migration $mig)
    {
        $this->container = $container;
        $this->args = $request->getCliArgs();
        $this->cli = $cli;
        $this->migrate = $mig;
    }

    public function create()
    {
        if (!$this->migrate->hasMigration()) {
            return $this->missingMigrate();
        }

        $this->cli->confirm("Are you sure you want to migrate the {$this->table} table?", function ($stream) {

            try {
                $msg = $this->migrate->getBuild()->create();
                $stream->write($this->migrate->getBuild()->getMessage($msg));
            } catch (QueryCreateException $e) {
                $this->cli->write($e->getMessage());
            }
        });

        return $this->cli->getResponse();
    }

    public function read()
    {
        if (!$this->migrate->hasMigration()) {
            return $this->missingMigrate();
        }

        try {
            $this->cli->write($this->migrate->getBuild()->read());
        } catch (QueryCreateException $e) {
            $this->cli->write($e->getMessage());
        }


       // $this->cli->write($this->migrate->getBuild()->read());
        return $this->cli->getResponse();
    }

    public function drop()
    {
        if (!$this->migrate->hasMigration()) {
            return $this->missingMigrate();
        }
        $this->cli->confirm("Are you sure you want to drop the the {$this->table} table?", function ($stream) {
            $msg = $this->migrate->getBuild()->drop();
            $stream->write($this->migrate->getBuild()->getMessage($msg));
        });
        return $this->cli->getResponse();
    }

    public function help()
    {
        $this->cli->write('$ migrate [type] [--values, --values, ...]');
        $this->cli->write('Type: read, create, drop or help');
        $this->cli->write('Values: --table=%s, --help');
        return $this->cli->getResponse();
    }

    public function missingMigrate()
    {
        $this->cli->write('The migrate "' . $this->migrate->getName() . '" is missing! Read help form more info. ' .
            '($ migrate help)');
        return $this->cli->getResponse();
    }
}
