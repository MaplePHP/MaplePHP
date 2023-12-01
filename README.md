# MaplePHP
**MaplePHP is a layered structure PHP framework** that has been meticulously crafted to provide developers with an intuitive, user-friendly experience that doesn't compromise on performance or scalability. By leveraging a modular architecture with **full PSR support**, the framework allows for easy customization and flexibility, enabling developers to pick and choose the specific components they need to build their applications. Overall, the PHP Fuse framework is an excellent choice for developers looking to build high-quality web applications quickly and efficiently.

MaplePHP's commitment to **agnosticism** ensures that it remains **free of dependencies**, allowing developers the flexibility to choose dependencies based on their specific project needs rather than being dictated by the framework. The framework's streamlined approach guarantees it avoids bloat by incorporating only essential libraries, each serving a distinct purpose without redundancy.

Moreover, MaplePHP sets itself apart through its seamless communication between the backend and the frontend, facilitated by Stratoxjs—a purpose-built tool tailored specifically for Maple. This integration enhances the framework's unique capabilities, fostering efficient and effective development of high-quality web applications.

## Much more to be done
The MaplePHP framework is still a work in progress, with over 800 points on my checklist that need attention—no exaggeration. Despite its incompleteness, I'm enthusiastic about showcasing its notable features. However, I advise against utilizing the framework for actual projects at this time. It's best to wait until version 1.1.0, by which point I'll have addressed all the existing issues and provided comprehensive documentation for a more stable and reliable experience.

**With that said...** The libraries bellow is should all be stable.

## Installation
Right now installation will only be available through **git** because of I have made every library as an **submodule**. This might change soon.
```
composer create-project maplephp/maplephp myApp
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
