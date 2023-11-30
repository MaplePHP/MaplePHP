

# MaplePHP
**MaplePHP is a layered structure PHP framework** that has been meticulously crafted to provide developers with an intuitive, user-friendly experience that doesn't compromise on performance or scalability. By leveraging a modular architecture with **full PSR support**, the framework allows for easy customization and flexibility, enabling developers to pick and choose the specific components they need to build their applications. Overall, the PHP Fuse framework is an excellent choice for developers looking to build high-quality web applications quickly and efficiently.

## Installation
Right now installation will only be available through **git** because of I have made every library as an **submodule**. This might change soon.
```
git clone --recurse-submodules git@github.com:MaplePHP/MaplePHP.git myAppDir
```

## Guides
The guide is not complete. There is much more to come.
 - [Cache](https://github.com/MaplePHP/Cache)
 - [Container](https://github.com/MaplePHP/Container)
 - [DTO](https://github.com/MaplePHP/DTO)
 - [Form](https://github.com/MaplePHP/Form)
 - [Handler](https://github.com/MaplePHP/Handler)
 - [Http](https://github.com/MaplePHP/Http)
 - [Log](https://github.com/MaplePHP/Log)
 - [Output](https://github.com/MaplePHP/SwiftRender)
 - [Query](https://github.com/MaplePHP/Query)
 - [Roles](https://github.com/MaplePHP/Roles)
 - [Validate](https://github.com/MaplePHP/Validate)
 - Clock (Guide not complete)
 - Cookies (Guide not complete)
 - Auth (Guide not complete)
 
### More functions
- Pen-tested
- Quality code tested
- Dependency injector 
- Will have full PSR support
- Service provider
- Multiple languages
- Built in protection againt XSS, CSRF, session injection, MySql injection
- Emitter, CSP, Strict transport-security
- Seamless communication between frontend and backend
- Lightweight frontend code yet powerfull.

### Preview
Guide will come
```php

namespace Http\Controllers;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use Services\ServiceProvider;
use Http\Controllers\BaseController;

class YourController extends BaseController  {

    function __construct(ServiceProvider $provider) {
    }

    function about(ResponseInterface $response, RequestInterface $request) {

        // Meta (DOM)
        $this->head()->getElement("title")->setValue("About us");
        $this->head()->getElement("description")->attr("content", "Lorem ipum dolor");

        // Template
        $this->view()->setPartial("breadcrumb", [
            "tagline" => getenv("APP_NAME"),
            "name" => "Welcome to MaplePHP",
            "content" => "Get ready to build you first application."
        ]);
        
        //$this->local("auth")->get("wrong-credentials", "Wrong credentials"); // Static translate
        //$this->lang()
        //$this->cookies()
        //$this->mail()
        //$this->responder()
        //...

        return $response;
    }

}
```
