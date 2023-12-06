
# MaplePHP
**MaplePHP is a layered structure PHP framework** that has been meticulously crafted to provide developers with an intuitive, user-friendly experience that doesn't compromise on performance or scalability. By leveraging a modular architecture with **full PSR support**, the framework allows for easy customization and flexibility, enabling developers to pick and choose the specific components they need to build their applications. Overall, the PHP Fuse framework is an excellent choice for developers looking to build high-quality web applications quickly and efficiently.

MaplePHP's commitment to **agnosticism** ensures that it remains **free of dependencies**, allowing developers the flexibility to choose dependencies based on their specific project needs rather than being dictated by the framework. The framework's streamlined approach guarantees it avoids bloat by incorporating only essential libraries, each serving a distinct purpose without redundancy.

Moreover, MaplePHP sets itself apart through its seamless communication between the backend and the frontend, facilitated by Stratoxjs—a purpose-built tool tailored specifically for Maple. This integration enhances the framework's unique capabilities, fostering efficient and effective development of high-quality web applications.

## Much more to be done
In recent developments, MaplePHP has reached a significant milestone with the release of **version 2.0.0+**. This signifies the conclusion of its beta phase, marking the completion of every structural change. While substantial progress has been achieved, there is still much on the horizon. Ongoing tasks include **rigorous quality testing** and **comprehensive documentation updates**, all aimed at ensuring an even more user-friendly experience for developers.

## Installation
The primary installation.
```
composer create-project maplephp/maplephp myApp
```
## Update MaplePHP
As of version 2.0.0 and beyond MaplePHP you will only need to execute the command bellow to update Maple to its latest version.
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

### Install database
Execute the following command and follow the instructions:
```
php cli config install --type=mysql
```
The database is now installed and ready.

### Install mail
Execute the following command and follow the instructions. I do recommended using a SMTP but is not required:
```
php cli config install --type=mail
```
Mail has now been installed.

### Install Auth and login form
*Requires at least MaplePHP framework 1.0.4*
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
use MaplePHP\Foundation\Http\Provider;
use Http\Controllers\BaseController;

class YourController extends BaseController  {

    function __construct(Provider $provider) {
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
