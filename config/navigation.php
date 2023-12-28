<?php

/**
 * Navigation config file
 * @psalm-suppress InvalidScope
 * 
 * Modify the static navigation that comes out of the box
 *
 * E.G:
 * The id is not required, but will create it´s own id with increment, starting from 1 if not filled in. 
 * The id has to be unique and is used to select parent!
 */
return [
    'navigation' => [
        'main' => [
            [
                "id" => 1,
                "name" => "Start",
                "slug" => "",
                "parent" => 0,
                "title" => false,
                "description" => "Lorem ipsum dolor"
            ],
            [
                "id" => 2,
                "name" => "About",
                "slug" => "about",
                "parent" => 0,
                "title" => "About us",
                "description" => "Lorem ipsum dolor"
            ],
            [
                "id" => 3,
                "name" => "Contact",
                "slug" => "contact",
                "parent" => 0,
                "title" => "Contact us",
                "description" => "Lorem ipsum dolor"
            ]
        ]
    ]
];
