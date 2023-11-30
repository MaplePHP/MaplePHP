<?php

namespace Services\Stream;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\DTO\Format\Str;
use MaplePHP\Validate\Inp;
use Exception;

class Packaging
{
    public const REQUIRED = ["package", "description", "version", "architecture", "maintainer", "files"];
    public const CONTROL_COLUMNS = ["package", "description", "version", "architecture", "maintainer"];

    private $data = array();

    public function setPackage(string $package): void
    {
        $this->data['package'] = $package;
    }

    public function setDescription(string $description): void
    {
        $this->data['description'] = $description;
    }

    public function setVersion(string $version): void
    {
        $this->data['version'] = $version;
    }

    public function setArchitecture(string $architecture): void
    {
        $this->data['architecture'] = $architecture;
    }

    public function setMaintainer(string $maintainer): void
    {
        $this->data['maintainer'] = $maintainer;
    }

    public function setFiles(string $files): void
    {
        $this->data['files'] = $files;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function formatFileName(string $fileName): string
    {
        $str = new Str($fileName);
        return $str->clearBreaks()->trim()->replaceSpecialChar()->replaceSpaces("")->get();
    }

    public function getControlFile(): string
    {
        $out = "";
        foreach ($this->data as $key => $value) {
            // Files wont be used in control file
            // I will instead generate temp files with @fileCommand
            if (in_array($key, self::CONTROL_COLUMNS)) {
                $key = ucfirst($key);
                $out .= "{$key}: {$value}\n";
            }
        }
        return $out;
    }

    public function buildPackage(string $rootDir, string $packageFile): string
    {
        $this->validate();
        $data = "mkdir -p " . $packageFile . "/DEBIAN && cat <<EOF >" . $packageFile . "/DEBIAN/control\n";
        $data .= $this->getControlFile();
        $data .= "EOF\n";
        $data .= $this->fileCommand($rootDir, $packageFile) . " && dpkg-deb --build " . $packageFile .
        " && rm -Rf {$packageFile}";
        return $data;
    }

    public function selectPackage(array $data, string $name, ?string &$version = null, bool $strict = false)
    {
        if (!isset($data[$name])) {
            return null;
        }

        $latest = end($data[$name]);
        if (is_null($version)) {
            $version = key($data[$name]);
        }

        if ($strict && !isset($data[$name][$version])) {
            return null;
        }
        return isset($data[$name][$version]) ? $data[$name][$version] : $latest;
    }

    /**
     * Package config file merge used with @Services\Stream\cli:jsonToFile()
     * @param  string $name package name
     * @return callable
     */
    public function packageFileInsert($name): callable
    {
        return function ($data, $insert) use ($name) {
            if (isset($data[$name])) {
                $data[$name] = array_merge($data[$name], $insert[$name]);
                return $data;
            }
            return $data + $insert;
        };
    }


    public function validate()
    {
        if (!Inp::value($this->data["version"] ?? "")->validVersion(true)) {
            throw new Exception("The version number is not in a valid format. Expects 0.0.0 format.", 1);
        }

        foreach (self::REQUIRED as $key) {
            $value = trim(($this->data[$key] ?? ""));
            if (!isset($value) || !strlen($value)) {
                throw new Exception("You need to fill in the argumnet \"{$key}\"!", 1);
            }
        }
    }

    private function fileCommand(string $rootDir, string $packageFile): string
    {
        $fileError = $fileCommand = array();
        $files = explode(",", $this->data['files']);
        foreach ($files as $file) {
            $file = str_replace("\\", "/", $file);
            $path = $rootDir . trim($file);
            $mkdir = "mkdir -p {$packageFile}/" . dirname(trim($file)) . "/";
            $fileCommand[] = "{$mkdir} && cp -r {$path} {$packageFile}/" . dirname(trim($file)) . "/";
            if (!is_file($path) && !is_dir($path)) {
                $fileError[] = $path;
            }
        }
        if (count($fileError) > 0) {
            throw new Exception("Files do not exist: " . implode(", ", $fileError), 1);
        }
        return implode(" && ", $fileCommand);
    }
}
