<?php

namespace Services;

use MaplePHP\Http\Interfaces\UrlInterface;
use MaplePHP\Http\Interfaces\UrlHandlerInterface;

class ServiceUrl implements UrlHandlerInterface
{

    private $url;
    private $publicDirPath;

    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
        $envDir = getenv("APP_PUBLIC_DIR");
        if (is_string($envDir) && $this->validateDir($envDir)) {
            $this->publicDirPath = ltrim(rtrim($envDir, "/"), "/");
        }
    }

    /**
     * Get the public dir path
     * @return string|null
     */
    public function getPublicDirPath(): ?string
    {
        return $this->publicDirPath;
    }

     /**
     * Get URL to public directory
     * @param  string $path  add to URI
     * @return string
     */
    public function getPublic(string $path = ""): string
    {
        return $this->url->getRoot("/{$path}");
    }

    /**
     * Get URL to resources directory
     * @param  string $path  add to URI
     * @return string
     */
    public function getResource(string $path = ""): string
    {
        return $this->url->getRootDir("/resources/{$path}");
    }

    /**
     * Get URL to js directory
     * @param  string $path  add to URI
     * @return string
     */
    public function getJs(string $path, bool $isProd = false): string
    {
        if ($isProd) {
            return $this->getPublic("js/{$path}");
        }
        return $this->getResource("js/{$path}");
    }

    /**
     * Get URL to css directory
     * @param  string $path  add to URI
     * @return string
     */
    public function getCss(string $path): string
    {
        return $this->getPublic("css/{$path}");
    }

    /**
     * Validate the dir path
     * @param  string $path
     * @return bool
     */
    public function validateDir(string $path): bool
    {
        $fullPath = realpath($_ENV['APP_DIR'].$path);
        return (is_string($fullPath) && strpos($fullPath, $_ENV['APP_DIR']) === 0);
    }
}
