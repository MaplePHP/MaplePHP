



# MaplePHP - Layered structure MVC PHP framework

**MaplePHP is a layered structure MVC PHP framework** that very user-friendly and does not compromise on performance or scalability. By leveraging a modular architecture and with full PSR support, the framework allows for easy customization and flexibility, enabling developers to pick and choose the specific components they need to build their applications.

- [Why Choose MaplePHP?](#why-choose-maplephp)
- [Installation](#installation)
- [Quick guide](#quick-guide)
- [Other installations](#other-installations)
- [Library guides](#library-guides)

## Why Choose MaplePHP?
MaplePHP is designed with a commitment to **independence** and adherence to best practices, implementing **PHP Standards Recommendations (PSR)**. Within the framework, you'll find a variety of excellent libraries, including query, cache, logger, and more. However, we don't impose them on you. Feel free to utilize familiar third-party libraries or extend MaplePHP's functionality with your own. In our philosophy, dependencies should be at your discretion, not dictated by the framework.

Our library architecture is unique – each library within MaplePHP is self-contained or, in some instances, relies on another MaplePHP library. This approach not only ensures that you can initiate projects without external dependencies but also allows for an efficient use of resources. By avoiding redundancy, the framework steers clear of becoming bloated. 

Updates to MaplePHP are delivered through minor and patch versions, ensuring smooth project updates without breaking changes. This compatibility extends to PHP 8 and potentially beyond. With MaplePHP, you have the flexibility to shape the framework to meet your development needs without unnecessary constraints. Mening you will get all the latest functionality but you will never see Maple version 4 because you most likely already retired ;).

### Advantages
- **User-friendly:** MaplePHP is straightforward and intuitive. 
- **High Performance:** The framework is optimized for speed, providing efficient execution and reduced processing times. 
- **Full PSR Support:** MaplePHP fully adheres to PHP Standards Recommendations, promoting standardized and interoperable code. 
- **Modular Architecture:** With a modular structure, developers can easily customize and extend functionality based on project requirements. 
- **Efficient Library Integration:** MaplePHP libraries seamlessly work with other frameworks as well as within the MaplePHP environment.
- **Service Providers:** MaplePHP offers service providers, streamlining the integration of external services and enhancing application capabilities. 
- **Multilingual Support:** You can very easily add translations to your project.
- **Built-in Security Measures:** Built-in protection against common vulnerabilities such as XSS (Cross-Site Scripting), CSRF (Cross-Site Request Forgery), session injection, and MySQL injection. 
- **Emitter, CSP, Strict Transport-Security:** The framework includes features like Emitter for efficient HTTP response handling, Content Security Policy (CSP) for enhanced security against code injection, and Strict Transport-Security for secure communication. 
- **Dependency Flexibility:** Developers have the freedom to choose and control dependencies, ensuring that MaplePHP doesn't impose unnecessary constraints on project structures. 
- **Continuous Updates:** MaplePHP offers regular updates through minor and patch versions, allowing developers to stay current without worrying about breaking changes. 
- **Long-Term Compatibility:** MaplePHP commits to compatibility with PHP version 8 and beyond, providing a stable foundation for long-term projects.


## Much more to be done
In recent developments, MaplePHP has reached a significant milestone with the release of **version 2.0.0+**. This signifies the conclusion of its beta phase, marking the completion of every structural change. While substantial progress has been achieved, there is still much on the horizon. Ongoing tasks include **rigorous quality testing **and** comprehensive documentation updates**, all aimed at ensuring an even more user-friendly experience for developers.

## Installation
The primary installation.
```
composer create-project maplephp/maplephp myApp
```
## Updating MaplePHP
Starting from version 2.0.0 and beyond, updating MaplePHP to the latest version is as simple as running the command below.
```
composer update
```

## Install the app
From you apps root directory (where the file **cli** exists) execute the following command and follow the instructions:
```
php cli config install --type=app
```
*Every value that comes with a default value can be skipped.*

The app is installed... You can change ever value by either enter the install command again or by opening the **.env** file and manually change it.

Access your application/site in the browser by navigating to the **"public/"** directory to observe it in action.


## Quick guide
**Getting started in just 4 steps.** I am working on a more comprehensive guide in gitbook and will publish it as soon as possible.

1. Adding Controller
2. Add Controller to router
3. Add services to provider
5. Dispatch output

### 1. Adding controller
Add a controller file in the Controllers directory or you can just duplicate one of the working examples from "app/Http/Controllers/Examples/". 

```php
<?php
namespace Http\Controllers;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;
use Http\Controllers\BaseController;

class Pages extends BaseController
{
    public function __construct(Provider $provider)
    {
    }
    
    public function start(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        // Your code here -->
        return $response;
    }
}
```
*More comprehensive guide will come on controllers*

### 2. Add Controller to router
Add you Controller to router **(app/Http/Routes/web.php)** by specifing a method type (GET, POST, PUT, DELETE), full namespace path to your class and specify method if you want, else maple will try to access the __invoke method.
```php
$routes->group(function ($routes) {
    // Will handle all HTTP request errors
    $routes->map("*", '[/{any:.*}]', ['Http\Controllers\HttpRequestError', "handleError"]);

    // Your routes here
    $routes->get("/", ['Http\Controllers\Pages', "start"]);
    $routes->get("/{page:contact}", ['Http\Controllers\Pages', "contact"]);
    $routes->post("/{page:contact}", ['Http\Controllers\Pages', "submitContactForm"]);

}, [
        // Add middlewares
    MaplePHP\Foundation\Cache\Middleware\LastModified::class,
    MaplePHP\Foundation\Nav\Middleware\Navigation::class,
    MaplePHP\Foundation\Dom\Middleware\Meta::class
]);
```
*More comprehensive guide will come on router and middlewares*

### 3. Add services to provider
Utilize the Dependency Injector for seamless and efficient connection and access to services in controllers and services in services, and so on. It effortlessly resolves dependencies, preventing the creation of duplicate instances.
1. Add them to ”configs/providers.php” services here should be accessible by the whole application.
```php
return [
    'providers' => [
        'services' => [
            'logger' => '\MaplePHP\Foundation\Log\StreamLogger',
            'lang' => '\MaplePHP\Foundation\Http\Lang',
            'responder' => '\MaplePHP\Foundation\Http\Responder',
            'cookies' => '\MaplePHP\Foundation\Http\Cookie'
        ]
    ]
];
// To access a service above, e.g."logger" in your controller
// then just output:
//var_dump($this->logger()); 
/*
* Event handler - Example:
* Add to service provider and event handler
* Event handler will trigger every time "emergency, alert or critical" is triggered
* When they are triggered the service "MyMailService" will be triggered
* Resulting in that the log message will also be emailed

'logger' => [
    "handlers" => [
        '\MaplePHP\Foundation\Log\StreamLogger' => ["emergency", "alert", "critical"],
    ],
    "events" => [
        '\MyCustomService\MyMailService'
    ]
]

*/
```
2. Access services directly in your Controller and through your **constructor**. 
```php
public function __construct(Provider $provider, StreamLogger $streamLogger)
{
    $this->logger = $streamLogger;
}
```
3. Initiate the service directly with the Container/provider.
```php
public function __construct(Provider $provider, StreamLogger $streamLogger)
{
    $provider->set("logger", StreamLogger::class);
    //var_dump($this->logger()); // Access the logger 
}
```
*What is great is that if StreamLogger has it own services and those services has their own services and so on, the dependency injector will resolve it all for you, and also without creating duplicate instances!*

*More comprehensive guide will come on provider, services and event handler*

### 4. Dispatch output
Use built in template library or add your own either way output the content with PSR ResponseInterface.
```php
<?php

namespace Http\Controllers;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;
use Http\Controllers\BaseController;

class Pages extends BaseController
{
        // Use the built in template engine
    public function start(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        $this->view()->setPartial("ingress", [
            "tagline" => "My Awesome App",
            "name" => "Welcome to MaplePHP",
            "content" => "Get ready to build you first application."
        ]);

        $this->view()->setPartial("content", [
            "name" => "A second breadcrumb",
            "content" => "A second breadcrumb/ingress attached to the main partial."
        ]);

        return $response;
    }
    
        // Or attach you own to the stream
    public function about(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        $response->getBody()->write("Hello world");
        return $response;
    }
}
```
*More comprehensive guide will come on the built in template engine and how to implement third-apart template engine*

**And thats it.. Easy right?**

There is of course **many** more functions, but with that you can start building your site or app either with MaplePHP libraries or third-party libraries that you are used to work with.

## Other installations

### Install database
Execute the following command and follow the instructions:
```
php cli config install --type=mysql
```
The database is now installed and ready. 

**IF you do not want to use table prefix, you can manually remove "MYSQL_PREFIX" or add a empty string from .env**

### Install mail
Execute the following command and follow the instructions. I do recommended using a SMTP but is not required:
```
php cli config install --type=mail
```
Mail has now been installed.

### Install Auth and login form

#### Install the database:
In the correct order!
```
php cli migrate create --table=organizations
```
```
php cli migrate create --table=users
```
```
php cli migrate create --table=login
```
```
php cli migrate create --table=usersToken
```
#### Add organization
```
php cli database insertOrg
```
#### Add user
```
php cli database insertUser
```
*Now you can use the login form (take a look at the router file app/Http/Routes/web.php) and you will see that login controller is already prepared.*


## Library guides
The guide is not complete. There is much more to come.
 - [Routing](https://github.com/MaplePHP/Handler)
 - [Container](https://github.com/MaplePHP/Container)
 - [Dependency injector](https://github.com/MaplePHP/Container#dependency-injector)
 - [Event handler](https://github.com/MaplePHP/Container#event-handler)
 - [Http](https://github.com/MaplePHP/Http)
 - [Request](https://github.com/MaplePHP/Http#request)
 - [Response](https://github.com/MaplePHP/Http#response)
 - [Messaging](https://github.com/MaplePHP/Http#message)
 - [Stream](https://github.com/MaplePHP/Http#stream)
 - [Client requests](https://github.com/MaplePHP/Http#create-a-request)
 - [Cache](https://github.com/MaplePHP/Cache)
 - [DTO](https://github.com/MaplePHP/DTO)
 - [Form](https://github.com/MaplePHP/Form)
 - [Log](https://github.com/MaplePHP/Log)
 - [Output](https://github.com/MaplePHP/SwiftRender)
 - [Query](https://github.com/MaplePHP/Query)
 - [Roles](https://github.com/MaplePHP/Roles)
 - [Validate](https://github.com/MaplePHP/Validate)
 - Clock (Guide not complete)
 - Cookies (Guide not complete)
 - Auth (Guide not complete)
