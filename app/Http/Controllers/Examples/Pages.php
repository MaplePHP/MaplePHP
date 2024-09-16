<?php

namespace Http\Controllers\Examples;

//use MaplePHP\DTO\Collection;
//use MaplePHP\DTO\Format\Arr;
//use MaplePHP\DTO\Traverse;
use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Http\Interfaces\RequestInterface;
use MaplePHP\Foundation\Http\Provider;
use Http\Controllers\BaseController;
use MaplePHP\Query\Connect;
use MaplePHP\Query\DB;
use MaplePHP\Query\Handlers\PostgreSQLHandler;
use MaplePHP\Query\Handlers\SQLiteHandler;

class Pages extends BaseController
{

    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * The start page see router
     * @return void
     */
    public function start(): void
    {

        $SQLiteHandler = new SQLiteHandler($this->provider->dir()->getDatabase("database.sqlite"));
        $SQLiteHandler->setPrefix("mp_");
        $connect = Connect::setHandler($SQLiteHandler, "sqlite");
        $connect->execute();

        $selectLite = Connect::getInstance("sqlite")::select("id,name", ["test", "a"]);
        $selectLite->limit(4);


        $PostgreSQLHandler = new PostgreSQLHandler("127.0.0.1", "postgres", "", "maplephp");
        $PostgreSQLHandler->setPrefix("maple_");
        $connect = Connect::setHandler($PostgreSQLHandler, "psg");
        $connect->execute();

        $selectPSG = Connect::getInstance("psg")::select("id,name", ["test", "a"]);


        $select = $this->provider->DB()::select("id,name", ["test", "a"]);





        echo "<pre>";
        print_r($selectLite->fetch());
        echo "<br>";
        print_r($selectPSG->pluck("a.name")->get());
        echo "<br>";
        print_r($select->pluck("a.name")->get());




        die;

        /*
          $test = Traverse::value([
            "test" => [
                "test2" => ["loremB", "loremA"]
            ],
            "create_date" => "2023-02-21 12:22:11",
            "www" => [1,2,3,4],
            "content" => "loremd dqw qwdwq dqw qdwqdw qdw qdwqdwq dwq dw q.",
            "ingress" => [
                "content" => "loremd dqw qwdwq dqw qdwqdw qdw qdwqdwq dwq dw q.",
            ]
        ]);

        $this->view()->setPartial("ingress", [
            "tagline" => "Ingress view partial",
            "name" => "Welcome to MaplePHP",
            "content" => "Get ready to build you first application."
        ]);

        $select = DB::select("*", new \database\migrations\Test);
        $select->join(new \database\migrations\TestCat);
        print_r($select->fetch());
        die;
        $traverse = Traverse::value([
            "test" => [
                "test2" => "2000-01-01 12:33:00",
            ],
            "test2" => [
                "test2" => "tetet"
            ],
        ]);

        die;
         */

        //

        $this->view()->setPartial("main.ingress", [
            "tagline" => "Ingress view partial",
            "name" => "Welcome to MaplePHP",
            "content" => "Get ready to build you first application."
        ], 3600);

        $this->view()->setPartial("main.text", [
            "tagline" => "Text view partial A",
            "name" => "Lorem ipsum dolor",
            "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id sapien dui. Nullam gravida bibendum finibus. Pellentesque a elementum augue. Aliquam malesuada et neque ac varius. Nam id eros eros. Ut ut mattis ex. Aliquam molestie tortor quis ultrices euismod. Quisque blandit pellentesque purus, in posuere ex mollis ac."
        ]);

        $this->view()->setPartial("main.text.textB", [
            "tagline" => "Text view partial B",
            "name" => "Lorem ipsum dolor",
            "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id sapien dui. Nullam gravida bibendum finibus. Pellentesque a elementum augue. Aliquam malesuada et neque ac varius. Nam id eros eros. Ut ut mattis ex. Aliquam molestie tortor quis ultrices euismod. Quisque blandit pellentesque purus, in posuere ex mollis ac."
        ]);
    }

    /**
     * The about page (see router)
     * @return void
     */
    public function about(): void
    {

        // Overwrite the default meta value
        //$this->head()->getElement("title")->setValue("Welcome to my awesome app");
        //$this->head()->getElement("description")->attr("content", "Some text about my awesome app");

        // $this->view() is the same as $this->provider when extending to the BaseController!;
        $this->view()->setPartial("main.ingress", [
            "tagline" => "Layered structure MVC framework",
            "name" => "MaplePHP",
        ]);

        $this->view()->setPartial("main.text", [
            "tagline" => "Layered structure MVC framework",
            "name" => "MaplePHP",
            "content" => "MaplePHP is a layered structure PHP framework that has been meticulously crafted to " .
            "provide developers with an intuitive, user-friendly experience that doesn't compromise on performance " .
            "or scalability. By leveraging a modular architecture with full PSR interface support, the framework " .
            "allows for easy customization and flexibility, enabling developers to pick and choose the specific " .
            "components they need to build their applications."
        ]);

    }


    /**
     * The about page (see router)
     * @return void
     */
    public function policy(): void
    {
        $this->view()->setPartial("main.text.integrity", [
            "name" => "Integrity policy",
            "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id sapien dui. Nullam gravida bibendum finibus. Pellentesque a elementum augue. Aliquam malesuada et neque ac varius. Nam id eros eros. Ut ut mattis ex. Aliquam molestie tortor quis ultrices euismod. Quisque blandit pellentesque purus, in posuere ex mollis ac."
        ]);

        $this->view()->setPartial("main.text.cookie", [
            "name" => "Cookies",
            "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id sapien dui. Nullam gravida bibendum finibus. Pellentesque a elementum augue. Aliquam malesuada et neque ac varius. Nam id eros eros. Ut ut mattis ex. Aliquam molestie tortor quis ultrices euismod. Quisque blandit pellentesque purus, in posuere ex mollis ac."
        ]);
        
    }

    /**
     * Will be invoked if method in router is missing
     * @param  ResponseInterface $response
     * @param  RequestInterface  $request
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response, RequestInterface $request): object
    {
        $_response = $response->withHeader("Content-type", "application/json; charset=UTF-8");

        // Repaint the whole HTML document with:
        // @response->getBody()->write("New content...")
        // @responder->build(), will do as above but read current responder json data
        return $this->responder()->build();
    }
}


class TEST {

    public $id;
    public $name;

    public function __construct($prefix = "", $id = 0)
    {
        $this->id = $prefix.$id;
    }
}