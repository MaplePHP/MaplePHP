<?php
/**
 * Find all, string number: [^/]+
 * IF match or else: (?:match|elseMatch)
 *
 * Can be used as bellow, this will first search for "sibling param" if not just use "/":
 *
 * {page:(?:.+/|/)our-cars}
 *
 * OK: "/PageParam1/PageParam2/our-cars"
 * OK: "/our-cars"
 *
 * Add a dynamic route from pages
 * /{page:.+}/{id:\d+}/{permalink:car-[^/]+}
 *
 * @var object $routes
 */

$routes->group(function ($routes) {

    // Will handle all HTTP request errors
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\HttpRequestError', "handleError"]);

    // Regular static example pages
    $routes->get("/", ['Http\Controllers\Examples\Pages', "start"]);
    $routes->get("/{page:about}", ['Http\Controllers\Examples\Pages', "about"]);
    $routes->get("/{page:policy}", ['Http\Controllers\Examples\Pages', "policy"]);

    // Contact page with form
    $routes->get("/{page:contact}", ['Http\Controllers\Examples\ExampleForm', "contactFrom"]);
    $routes->get("/{page:contact}/{model:modal}", ['Http\Controllers\Examples\ExampleForm', "contactFormModal"]);
    $routes->post("/{page:contact}", ['Http\Controllers\Examples\ExampleForm', "post"]);

    $routes->group(function ($routes) {
        // Public login area
        // Regular page with form
        $routes->get("/{page:login}", ['Http\Controllers\Private\Login', "form"]);

        // Open form in a modal with ajax call
        $routes->get("/{page:login}/{model:model}", ['Http\Controllers\Private\Login', "formModel"]);

        // Login request
        $routes->post("/{page:login}", ['Http\Controllers\Private\Login', "login"]);

        // Forgot password
        $routes->get("/{page:login}/{type:forgot}", ['Http\Controllers\Private\Login', 'forgotPasswordForm']);
        $routes->post("/{page:login}/{type:forgot}", ['Http\Controllers\Private\Login', 'forgotPasswordPost']);

        // Change password
        $routes->get("/{page:login}/{type:reset}/{token:[^/]+}", ['Http\Controllers\Private\Login', "resetPasswordForm"]);
        $routes->post("/{page:login}/{type:reset}/{token:[^/]+}", ['Http\Controllers\Private\Login', "resetPasswordPost"]);

    }, [
        [MaplePHP\Foundation\Auth\Middleware\LoggedIn::class, "publicZone"],
    ]);

    $routes->group(function ($routes) {
        // Private area (The user is logged in)
        // Profile page
        $routes->get("/{profile:profile}", ['Http\Controllers\Private\Pages', "profile"]);

        // Logout the user
        $routes->get("/{page:logout}", ['Http\Controllers\Private\Pages', "logout"]);

    }, [
        [MaplePHP\Foundation\Auth\Middleware\LoggedIn::class, "privateZone"]
    ]);

}, [
    [Http\Middlewares\Document::class, ["before" => ["head", "navigation", "footer"]]],
    //MaplePHP\Foundation\Cache\Middleware\LastModified::class,
    MaplePHP\Foundation\Dom\Middleware\Meta::class,
    MaplePHP\Foundation\Auth\Middleware\SessionStart::class
]);
