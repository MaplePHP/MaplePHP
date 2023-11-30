<?php

/**
 * Find all, string number: [^/]+
 * IF match or else: (?:match|elseMatch)
 *
 * Can be used as bellow, this will first search for "sibling param" if not just use "/":
 *
 * {page:(?:.+/|/)vara-bilar}
 *
 * OK: "/PageParam1/PageParam2/vara-bilar"
 * OK: "/vara-bilar"
 *
 * Add a dynamic route from pages
 * /{page:.+}/{id:\d+}/{permalink:bil-[^/]+}
 *
 * @var object $routes
 */

$routes->group(function ($routes) {
    // Will handle all HTTP request errors
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\HttpRequestError', "handleError"]);

    // Regular static example pages
    $routes->get("/", ['Http\Controllers\Examples\Pages', "start"]);
    $routes->get("/{page:about}", ['Http\Controllers\Examples\Pages', "about"]);

    // Contact page with form
    $routes->get("/{page:contact}", ['Http\Controllers\Examples\ExampleForm', "contactFrom"]);
    $routes->get("/{page:contact}/{model:modal}", ['Http\Controllers\Examples\ExampleForm', "contactFormModal"]);
    $routes->post("/{page:contact}", ['Http\Controllers\Examples\ExampleForm', "post"]);
    
    
    // Open up a SESSION
    $routes->group(function ($routes) {

        // With session now open we can handle the Login form and it's requests

        // Public login area
        $routes->group(function ($routes) {
            // Regular page with form
            $routes->get("/{page:login}", ['Http\Controllers\Private\Login', "form"]);
            // Open form in a modal with ajax call
            $routes->get("/{page:login}/{model:model}", ['Http\Controllers\Private\Login', "formModel"]);
            // Login request
            $routes->post("/{page:login}", ['Http\Controllers\Private\Login', "login"]);

            $routes->get("/{page:login}/{type:forgot}", ['Http\Controllers\Private\Login', "forgotPasswordForm"]);
            $routes->post("/{page:login}/{type:forgot}", ['Http\Controllers\Private\Login', "forgotPasswordPost"]);

            // Change password
            $routes->get("/{page:login}/{type:reset}/{token:[^/]+}", [
                'Http\Controllers\Private\Login',
                "resetPasswordForm"
            ]);
            $routes->post("/{page:login}/{type:reset}/{token:[^/]+}", [
                'Http\Controllers\Private\Login',
                "resetPasswordPost"
            ]);
        }, [
            [Http\Middlewares\LoggedIn::class, "publicZone"],
        ]);


        // Private area (The user is logged in)
        $routes->group(function ($routes) {
            // Logout the user
            $routes->get("/{page:logout}", ['Http\Controllers\Private\Pages', "logout"]);

            // Profile page
            $routes->get("/{profile:profile}", ['Http\Controllers\Private\Pages', "profile"]);
        }, [
            [Http\Middlewares\LoggedIn::class, "privateZone"]
        ]);
    }, [
        Http\Middlewares\SessionStart::class
    ]);
}, [
    //Http\Middlewares\Profiling::class,
    Http\Middlewares\LastModifiedHandler::class, // Wont wotk with SESSION
    Http\Middlewares\Navigation::class,
    Http\Middlewares\DomManipulation::class
]);
