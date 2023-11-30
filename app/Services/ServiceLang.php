<?php

namespace Services;

use MaplePHP\Http\Interfaces\UrlInterface;

class ServiceLang
{
    private $url;
    private $prefix;

    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
        $this->prefix = getenv("APP_LANG");
        if ($p = $this->url->withType("lang")->current()) {
            $this->prefix = $p;
        }
    }

    public function prefix()
    {
        return $this->prefix;
    }
}
