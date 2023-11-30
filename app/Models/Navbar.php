<?php

namespace Models;

use InvalidArgumentException;
use MaplePHP\DTO\Traverse;
use MaplePHP\Nest\Builder;
use Services\ServiceProvider;

class Navbar
{
    public const ITEMS = [
        [
            "name" => "Start",
            "slug" => "",
            "parent" => 0,
            "title" => false,
            "description" => "Lorem ipsum dolor"
        ],
        [
            "name" => "About",
            "slug" => "about",
            "parent" => 0,
            "title" => "About us",
            "description" => "Lorem ipsum dolor"
        ],
        [
            "name" => "Contact",
            "slug" => "contact",
            "parent" => 0,
            "title" => "Contact us",
            "description" => "Lorem ipsum dolor"
        ],
        /*
        [
            "name" => "Login",
            "slug" => "login",
            "parent" => 0,
            "title" => "Login",
            "description" => "Lorem ipsum dolor"
        ]
         */
    ];

    private $builder;
    private $items = array();
    private $protocol;
    private $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Add item to nav
     * @param array $arr
     */
    public function addItem(array $arr): void
    {
        if (empty($arr['name'])) {
            throw new InvalidArgumentException("Error Navbar::addItem array item name is missing!", 1);
        }
        if (empty($arr['slug'])) {
            throw new InvalidArgumentException("Error Navbar::addItem array item slug is missing!", 1);
        }
        $this->items[] = $arr;
    }

    /**
     * Create array items
     * @return array
     */
    private function items(): array
    {
        $items = array();
        $arr = array_merge($this->items, $this::ITEMS);
        foreach ($arr as $key => $item) {
            //$pos = ($item['position'] ?? 0);
            $key = (int)$key;
            $items[($item['parent'] ?? 0)][($key + 1)] = Traverse::value($item);
        }
        return $items;
    }

    /**
     * Build nav template
     * @return void
     */
    protected function template(): void
    {
        $this->builder->setClass("items gap-x-10")->html(
            "nav",
            "ul",
            "li",
            function ($obj, $li, $active, $level, $id, $parent) {
                //$uri = $obj->uri;

                //$activeParent = $obj->activeParent($uriArr);
                $hasChild = ($obj->hasChild ? " has-child" : "");
                $topItem = ($parent === 0) ? " top-item" : "";
                $li->attr("class", "item{$hasChild}{$topItem}{$active}");

                // Create link
                $li->create("a", $obj->name)
                ->attr("title", "Besök sidan")
                ->attr("href", $this->provider->url()->getRoot($obj->uri))
                ->attr("class", "item item-{$id}{$active}");
            }
        );
    }

    /**
     * Get nav builder
     * @return Builder
     */
    public function get(): Builder
    {
        if (is_null($this->builder)) {
            // Build the navigation structure
            $this->builder = new Builder($this->items());
            $this->builder->build(function ($obj) {
                return $obj->slug;
            });

            // The navigation template
            $this->template();

            // Protocol will validate request (works great with dynmic routes) and will
            // pass on active nav item to the template.
            $this->protocol = $this->validate($this->provider->url()->getVars());

            // Pass the protocol to the ServiceProvider and container
            // You can use it on the controller to validate request in conjunction with a dynamic
            // PATTERN in the router
            // E.g. ($this->protocol->status() === 200 || $this->protocol->status() === 404 ||
            // $this->protocol->status() === 301)
            $this->provider->set("protocol", $this->protocol);
        }
        return $this->builder;
    }

    /**
     * Validate navigation item request
     * @param  array  $vars
     * @return object
     */
    public function validate(array $vars): object
    {
        $protocol = $this->builder->protocol();
        $protocol->load($vars);
        return $protocol;
    }
}
