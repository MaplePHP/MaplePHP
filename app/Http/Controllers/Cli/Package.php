<?php

namespace Http\Controllers\Cli;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Container\Interfaces\ContainerInterface;
use MaplePHP\Http\Interfaces\DirInterface;
use MaplePHP\Http\Env;
use Http\Controllers\Cli\CliInterface;
use Services\Stream\Cli as Stream;
use Services\Stream\Packaging;
use MaplePHP\Container\Reflection;

/**
 * Is used to install dkim packages to extend MaplePHP functionallity
 * @psalm-suppress ForbiddenCode
 */
class Package implements CliInterface
{
    protected $container;
    protected $args;
    protected $dir;
    protected $cli;
    protected $pack;
    private $rootDir;
    private $packageDir;
    private $configFile;

    public function __construct(
        ContainerInterface $container,
        RequestInterface $request,
        DirInterface $dir,
        Stream $cli,
        Packaging $packaging
    ) {
        $this->container = $container;
        $this->args = $request->getCliArgs();
        $this->dir = $dir;
        $this->cli = $cli;
        $this->pack = $packaging;
        $this->rootDir = $this->dir->getRoot();
        $this->packageDir = "{$this->rootDir}storage/bin/packages/";
        $this->configFile = "{$this->packageDir}packaging.json";
    }

    /**
     * Get all the depended classes to specified class
     * @cliArgs name namespace::class
     * @cliArgs filtered If has flag then filter out libraries
     * @psalm-suppress InvalidArgument
     * @return ResponseInterface
     */
    public function get(): ResponseInterface
    {
        if (isset($this->args['name']) && strlen($this->args['name'])) {
            $this->args['name'] = str_replace("/", "\\", $this->args['name']);

            /*
            try {
                $ref = new Reflection($this->args['name']);
                //$ref->allowInterfaces(false);
                $instances = $ref->dependencyInjector();
            } catch (\Exception $e) {
            }
             */

            $list = Reflection::getClassList();

            if (is_array($list) && count($list) > 0) {
                $keys = array_keys($list);
                if (isset($this->args['filtered'])) {
                    $keys = array_filter($keys, function ($val) {
                        return strpos($val, "MaplePHP") === false;
                    });
                }
                $keys[] = $this->args['name'];
                $seperator = "\n";

                $this->cli->write("Found " . count($keys) . " services:");
                $this->cli->write(implode($seperator, $keys));
            } else {
                $this->cli->write("Could not find any services!");
            }
        } else {
            $this->cli->write("--name argument is required!");
        }

        return $this->cli->getResponse();
    }

    /**
     * List all installable packages
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        $files = glob("{$this->packageDir}*.deb");
        if (count($files) > 0) {
            //$out = "";
            foreach ($files as &$file) {
                $file = basename($file);
                $exp = explode(".", $file);
                array_pop($exp);
                $file = implode(".", $exp);
            }

            $this->cli->write("Available packages:");
            $this->cli->write(implode("\n", $files));
        } else {
            $this->cli->write("There exist no package at the moment.");
        }

        return $this->cli->getResponse();
    }

    /**
     * Inspects the package file and shows all files that will be installed
     * @return ResponseInterface
     */
    public function inspect(): ResponseInterface
    {
        $data = $this->cli->setJsonFileStream($this->configFile);
        $name = ($this->args['name'] ?? "");
        $version = ($this->args['version'] ?? null);

        $data = $this->pack->selectPackage($data, $name, $version, true);
        if (!$data) {
            $data = ["package" => $name, "version" => $version];
        }

        $file = "{$data['package']}-{$data['version']}.deb";
        $packageFile = "{$this->packageDir}{$file}";

        if (is_file($packageFile)) {
            $resonse = shell_exec("dpkg-deb -c {$packageFile} 2>&1");
            $this->cli->write("Inspecting: $resonse");
        } else {
            $this->cli->write("Error: The package (--name={$name} --version={$version}) do not exist!");
        }

        return $this->cli->getResponse();
    }

    public function updateBuild(): void
    {

        $data = $this->cli->setJsonFileStream($this->configFile);
        $name = ($this->args['name'] ?? "");
        $version = ($this->args['version'] ?? null);

        if (($data = $this->pack->selectPackage($data, $name, $version))) {
            $data['description'] = $this->cli->step("Description", $data['description']);
            $data['version'] = $this->cli->step("Version", $version);
            $data['maintainer'] = $this->cli->step("Maintainer", $data['maintainer']);
            $data['files'] = $this->cli->step("Files", $data['files']);
            $this->pack->setData($data);

            $file = "{$data['package']}-{$data['version']}";
            $packageFile = "{$this->packageDir}{$file}";

            try {
                $shellData = $this->pack->buildPackage($this->rootDir, $packageFile);
                $resonse = (string)shell_exec("{$shellData} 2>&1");
                if (strpos($resonse, "{$file}.deb") !== false) {
                    $insert = [$name => [$data['version'] => $this->pack->getData()]];
                    $this->cli->jsonToFile($insert, $this->pack->packageFileInsert($name));
                    $this->cli->write("Package has been re-built!");
                } else {
                    $this->cli->write("Error: {$resonse}");
                }
            } catch (\Exception $e) {
                $this->cli->write($e->getMessage());
            }
        } else {
            $this->cli->write("Error: The package (--name={$name}) do not exist in package.json file! " .
                "Either add correct lines to package.json file or build a new packages.");
        }
    }

    /**
     * Create a package
     * @return ResponseInterface
     */
    public function build(): ResponseInterface
    {

        $data = $this->cli->setJsonFileStream($this->configFile);
        $name = (string)$this->cli->step("Name", ($this->args['name'] ?? null));
        $name = $this->pack->formatFileName($name);
        if (!isset($data[$name])) {
            $version = (string)$this->cli->step("Version", "1.0.0");
            $desc = (string)$this->cli->step("Description");
            $maintainer = (string)$this->cli->step("Maintainer", getenv("APP_MAINTAINER"));
            $files = (string)$this->cli->step("Files");

            $file = "{$name}-{$version}";
            $packageFile = "{$this->packageDir}{$file}";

            $this->pack->setPackage($name);
            $this->pack->setVersion($version);
            $this->pack->setArchitecture("all");
            $this->pack->setMaintainer($maintainer);
            $this->pack->setDescription($desc);
            $this->pack->setFiles($files);

            try {
                $shellData = $this->pack->buildPackage($this->rootDir, $packageFile);
                $resonse = (string)shell_exec("{$shellData} 2>&1");
                if (strpos($resonse, "{$file}.deb") !== false) {
                    $insert = [$name => [$version => $this->pack->getData()]];
                    $this->cli->jsonToFile($insert, $this->pack->packageFileInsert($name));
                    $this->cli->write("Package has been built!");
                } else {
                    $this->cli->write("Error: {$resonse}");
                }
            } catch (\Exception $e) {
                $this->cli->write($e->getMessage());
            }
        } else {
            $this->cli->write("Error: The package \"{$name}\" already exists. " .
                "Use another name/delete or updateBuild.");
        }

        return $this->cli->getResponse();
    }

    /**
     * Install a package
     * @cliArgs name package name
     * @return ResponseInterface
     */
    public function install(): ResponseInterface
    {

        $_data = $this->cli->setJsonFileStream($this->configFile);
        $name = ($this->args['name'] ?? "");
        $version = ($this->args['version'] ?? null);

        $_data = $this->pack->selectPackage($_data, $name, $version, true);
        if (!$_data) {
            $_data = ["package" => $name, "version" => $version];
        }

        $file = "{$name}-{$version}";
        $packageFile = "{$this->packageDir}{$file}.deb";

        if (is_file($packageFile)) {
            $msg = (string)shell_exec("sudo dpkg --install --instdir={$this->rootDir} {$packageFile} 2>&1");
            if (strpos($msg, $packageFile) !== false) {
                $this->cli->write("Installing error: {$msg}");
                $this->cli->write("There probobly is an conflict and you need to uninstall a package first");
                $this->cli->write("You can uninstall package with the command:\n");
                $this->cli->write("php cli config package --uninstall=[PackageName]");
            } else {
                $this->cli->write("Installing: {$msg}");
            }
        } else {
            $this->cli->write("Error: The package (--name={$name} --version={$version}) do not exist!");
        }

        return $this->cli->getResponse();
    }

    /**
     * If there is an conflict you might be needing to uninstall a package
     * @cliArgs name package name
     * @return ResponseInterface
     */
    public function uninstall(): ResponseInterface
    {
        if (isset($this->args['name']) && strlen($this->args['name'])) {
            $msg = shell_exec("sudo dpkg -r {$this->args['name']} 2>&1");
            $this->cli->write("Uninstalling: {$msg}");
        } else {
            $this->cli->write("--name argument is required!");
        }

        return $this->cli->getResponse();
    }

    /**
     * Delete package file (this does not uninstall package)
     * @cliArgs name package name
     * @return ResponseInterface
     */
    public function delete(): ResponseInterface
    {
        $files = array();
        $config = $this->cli->setJsonFileStream($this->configFile);
        $name = ($this->args['name'] ?? "");
        $lastestVersion = $version = ($this->args['version'] ?? null);

        if (($_data = $this->pack->selectPackage($config, $name, $lastestVersion, true))) {
            if (!is_null($version)) {
                $this->deletePackageFile($name, (string)$lastestVersion, $files);
            } else {
                foreach ($config[$name] as $v => $_row) {
                    $this->deletePackageFile($name, $v, $files);
                }
            }

            if (count($files) > 0) {
                $this->cli->jsonToFile($files, function ($data, $input) use ($name) {
                    foreach ($input[$name] as $version => $_file) {
                        unset($data[$name][$version]);
                    }
                    if (count($data[$name]) === 0) {
                        unset($data[$name]);
                    }
                    return $data;
                });
                $this->cli->write("The Package has been removed.");
            }
        } else {
            $packageFile = "{$this->packageDir}{$name}-{$version}.deb";
            if (is_file($packageFile)) {
                unlink($packageFile);
                $this->cli->write("The Package has been removed.");
            } else {
                $this->cli->write("Error: The package (--name={$name} --version={$version}) do not exist!");
            }
        }

        return $this->cli->getResponse();
    }

    /**
     * Help
     */
    public function help()
    {
        $this->cli->write('$ package [type] [--values, --values, ...]');
        $this->cli->write('Type: create, install, uninstall, delete, get, list, inspect or help');
        $this->cli->write('Values: --name=%s');
        $this->cli->write('--name: package or namespace::class if get)');
        return $this->cli->getResponse();
    }

    private function deletePackageFile(string $name, string $version, array &$files = array()): void
    {
        $file = "{$name}-{$version}";
        $packageFile = "{$this->packageDir}{$file}";
        if (is_file($packageFile . ".deb")) {
            unlink($packageFile . ".deb");
        }
        $files[$name][$version] = $file;
    }
}
