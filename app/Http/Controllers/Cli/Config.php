<?php

namespace Http\Controllers\Cli;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Http\Interfaces\DirInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Http\Env;
use Http\Controllers\Cli\CliInterface;
use Services\Stream\Cli as Stream;
use MaplePHP\Container\Reflection;

class Config implements CliInterface
{
    protected $container;
    protected $args;
    protected $dir;
    protected $cli;

    public function __construct(ContainerInterface $container, RequestInterface $request, DirInterface $dir, Stream $cli)
    {
        $this->container = $container;
        $this->args = $request->getCliArgs();
        $this->dir = $dir;
        $this->cli = $cli;
    }

    public function install()
    {
        $type = ($this->args['type'] ?? null);
        $envConfig = $this->cli->getConfig();

        $allowedConfigs = array_keys($envConfig);
        if (isset($envConfig[$type]) && is_array($envConfig[$type])) {
            $data = $envConfig[$type];
            $file = $this->dir->getRoot() . ".env";
            $env = new Env($file);
            foreach ($data as $name => $v) {
                if (!is_array($v)) {
                    $key = "{$this->args['type']}_{$name}";
                    $value = $this->cli->step(ucfirst($name), ($v ? $v : ""));
                    if (!is_array($value)) {
                        $env->set($key, (string)$value);
                    }
                }
            }
            $this->cli->createFile($env->generateOutput(["fileData", "set"]), $file);
            $this->cli->write("installation completed");
        } else {
            $this->cli->write("Expecting the argumnet --type=%s, with a valid installation.\nAllowed types: " .
                implode(", ", $allowedConfigs));
        }
        
        return $this->cli->getResponse();
    }

    public function read()
    {
        if (isset($this->args['strict'])) {
            ob_start();
            print_r($_ENV);
            $out = ob_get_clean();
            $this->cli->write($out);
        } else {
            $file = $this->dir->getRoot() . ".env";
            $env = new Env($file);
            $this->cli->write($env->generateOutput(["fileData", "set"]));
        }
        return $this->cli->getResponse();
    }

    public function create()
    {

        $file = $this->dir->getRoot() . ".env";
        $env = new Env($file);

        if (isset($this->args['key']) && isset($this->args['value'])) {
            $change = $env->set($this->args['key'], $this->args['value']);
            $flag = ($env->hasEnv($this->args['key'])) ? "edit" : "add";

            $this->cli->confirm("Are you sure you want to \"{$flag}\" row to config environments?\n...\n" .
                $change . "\n...", function ($stream) use ($env, $file) {
                    $this->cli->createFile($env->generateOutput(["fileData", "set"]), $file);
                    $stream->write("Success!");
                });
        } else {
            $this->cli->write('The types --key=%s and --value=%s is required');
        }

        return $this->cli->getResponse();
    }

    public function drop()
    {

        $file = $this->dir->getRoot() . ".env";
        $env = new Env($file);

        if (isset($this->args['key'])) {
            if (!($env->hasEnv($this->args['key']))) {
                $this->cli->write('The config environment does not exist!');
            } else {
                $this->cli->confirm(
                    "Are you sure you want to drop the config environment \"{$this->args['key']}\"?",
                    function ($stream) use ($env, $file) {
                        // Drop Key
                        $env->drop($this->args['key']);
                        $this->cli->createFile($env->generateOutput(), $file);
                        $stream->write("Success!");
                    }
                );
            }
        } else {
            $this->cli->write('The types --key=%s required');
        }

        return $this->cli->getResponse();
    }

    public function help()
    {
        $this->cli->write('$ config [type] [--values, --values, ...]');
        $this->cli->write('Type: install, read, create, drop or help');
        $this->cli->write('Values: --key=%s, --value=%s --strict');
        $this->cli->write('--key: The env config key (type: create, drop)');
        $this->cli->write('--value: The env config value (type: create)');
        $this->cli->write('--strict: Will also show hidden configs, (type: read)');
        return $this->cli->getResponse();
    }
}
