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


$routes->cli("[help]", ['Http\Controllers\Cli\Cli', "help"]);

// Group handle is not required, but is a great way to organizing CLI packages->type calls

// Database creation/migration
$routes->group("migrate", function ($routes) {
    // It is recommended to add this handle at the begining of every grouped call
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\Cli\Cli', "handleMissingType"]);
    $routes->cli("[/help]", ['Http\Controllers\Cli\Migrate', "help"]);

    $routes->cli("/create", ['Http\Controllers\Cli\Migrate', "create"]);
    $routes->cli("/read", ['Http\Controllers\Cli\Migrate', "read"]);
    $routes->cli("/drop", ['Http\Controllers\Cli\Migrate', "drop"]);
});

$routes->group("config", function ($routes) {
    // It is recommended to add this 2 handles at the begining of every grouped call
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\Cli\Cli', "handleMissingType"]);
    $routes->cli("[/help]", ['Http\Controllers\Cli\Config', "help"]);

    $routes->cli("/install", ['Http\Controllers\Cli\Config', "install"]);
    $routes->cli("/package", ['Http\Controllers\Cli\Config', "package"]);
    $routes->cli("/create", ['Http\Controllers\Cli\Config', "create"]);
    $routes->cli("/read", ['Http\Controllers\Cli\Config', "read"]);
    $routes->cli("/drop", ['Http\Controllers\Cli\Config', "drop"]);
});


$routes->group("image", function ($routes) {
    // It is recommended to add this 2 handles at the begining of every grouped call
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\Cli\Cli', "handleMissingType"]);
    $routes->cli("[/help]", ['Http\Controllers\Cli\Image', "help"]);

    $routes->cli("/resize", ['Http\Controllers\Cli\Image', "resize"]);
});


$routes->group("package", function ($routes) {
    // It is recommended to add this 2 handles at the begining of every grouped call
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\Cli\Cli', "handleMissingType"]);
    $routes->cli("[/help]", ['Http\Controllers\Cli\Package', "help"]);

    $routes->cli("/get", ['Http\Controllers\Cli\Package', "get"]);
    $routes->cli("/list", ['Http\Controllers\Cli\Package', "list"]);
    $routes->cli("/inspect", ['Http\Controllers\Cli\Package', "inspect"]);
    $routes->cli("/install", ['Http\Controllers\Cli\Package', "install"]);
    $routes->cli("/uninstall", ['Http\Controllers\Cli\Package', "uninstall"]);
    $routes->cli("/build", ['Http\Controllers\Cli\Package', "build"]);
    $routes->cli("/updateBuild", ['Http\Controllers\Cli\Package', "updateBuild"]);
    $routes->cli("/delete", ['Http\Controllers\Cli\Package', "delete"]);
});

$routes->group("database", function ($routes) {
    // It is recommended to add this 2 handles at the begining of every grouped call
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\Cli\Cli', "handleMissingType"]);
    $routes->cli("[/help]", ['Http\Controllers\Cli\Database', "help"]);

    $routes->cli("/insertUser", ['Http\Controllers\Cli\Database', "insertUser"]);
    $routes->cli("/insertOrg", ['Http\Controllers\Cli\Database', "insertOrg"]);
    $routes->cli("/delete", ['Http\Controllers\Cli\Database', "delete"]);
});

$routes->group("mail", function ($routes) {
    // It is recommended to add this 2 handles at the begining of every grouped call
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\Cli\Cli', "handleMissingType"]);
    $routes->cli("[/help]", ['Http\Controllers\Cli\Mail', "help"]);
    $routes->cli("/send", ['Http\Controllers\Cli\Mail', "send"]);
});
