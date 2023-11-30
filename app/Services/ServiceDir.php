<?php

namespace Services;

use MaplePHP\Http\Interfaces\DirInterface;
use MaplePHP\Http\Interfaces\DirHandlerInterface;

class ServiceDir implements DirHandlerInterface
{

    private $dir;
    private $publicDirPath;

    public function __construct(DirInterface $dir)
    {
        $this->dir = $dir;
        $this->publicDirPath = "public/";
        
        $envDir = getenv("APP_PUBLIC_DIR");
        if (is_string($envDir) && $this->validateDir($envDir)) {
            $this->publicDirPath = ltrim(rtrim($envDir, "/"), "/")."/";
        }
    }

    /**
     * Get resource dir
     * @param  string $path
     * @return string
     */
    public function getResources(string $path = ""): string
    {
        return $this->dir->getDir("resources/{$path}");
    }

    /**
     * Get resource dir
     * @param  string $path
     * @return string
     */
    public function getPublic(string $path = ""): string
    {
        return $this->dir->getDir("{$this->publicDirPath}{$path}");
    }

    /**
     * Get storage dir
     * @param  string $path
     * @return string
     */
    public function getStorage(string $path = ""): string
    {
        return $this->dir->getDir("storage/{$path}");
    }

    /**
     * Get log dir
     * @param  string $path
     * @return string
     */
    public function getLogs(string $path = ""): string
    {
        return $this->getStorage("logs/{$path}");
    }

    /**
     * Get cache dir
     * @param  string $path
     * @return string
     */
    public function getCaches(string $path = ""): string
    {
        return $this->getStorage("caches/{$path}");
    }

    /**
     * Validate the dir path
     * @param  string $path
     * @return bool
     */
    final protected function validateDir(string $path): bool
    {
        $fullPath = realpath($_ENV['APP_DIR'].$path);
        return (is_string($fullPath) && strpos($fullPath, $_ENV['APP_DIR']) === 0);
    }
}
