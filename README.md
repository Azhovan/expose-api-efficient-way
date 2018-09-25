# Table of contents
-----

1. [Folder structure](#folder-structure)
2. [Application life cycle](#application-life-cycle)
3. [Application Architecture and Design implementation](#application-architecture-and-design-implementation)
   * [Architecture](#architecture)
   * [Design](#design)
   * [Architecture Abstractions](#architecture-abstractions)
   * [Request Abstraction](#request-abstraction)
   * [Events](#events) 
   * [Dispatch events](#dispatch-events) 
   * [Events](#events) 
4. [Run Tests](#run-tests)
5. [Endpoints](#endpoints)
   * [Api Authentication](#api-authentication)
   * [Api Description](#api-description)
6. [Persistence](#persistence)
7. [Code Standards](#code-standards)
8. [Used Libraries](#used-libraries)
9. [Requirements](#requirements)
10. [How To Deploy](#how-to-deploy)
   

-----

## folder structure 
-----

~~~
├── docs                  // The documentation files
│  
├── helpers               // helper functions
│    
├── web                   // entry point of the project
│   ├── index.php
│
├── src                   // The source codes folder
│   ├── Container         // IoC to Inject/Register services
|   ├── Controllers       // implementation of controller
|       └── Request        
|          └──  Recipe     // Form Request validation for every request  
|      ├── Response 
|   ├── Core
|       └── Contracts       // interfaces
│   ├── Recipe            // Recipe Implementation 
│     └── Core            // Core functionality of Recipe
|         └── Event
|         └── Traits
│     └── Exception 
│   ├── Routes
├── tests
~~~

## Application-life-cycle
-----   

This project does not use any framework, but it acts like a very simple framework to manage client requests more easily.
below are steps that show how a client request will proceed.

1) client hits an endpoint
2) application will be bootstrapped by loading dependencies, helper functions and finally registering services into the container
3) user request captured by the router, an instance of request object and application services injected into the controller
4) application request will be expanded by an abstraction class to apply filtering, authorizations and etc.
5) request data will be captured in step 4 and if everything went good, the request can go next step or just terminated and proper message with 
well prepared HTTP code returned to the user.
6) specific service(in this case Recipe) will be invoked, data passed into it.
7) based on requested action an event dispatched to calculate and aggregate the data. 
8) the result will be returned.
9) the user can see the valid JSON in response.

in all steps, if any exception/error happened it will be propagated into upper layers. 

## Application Architecture and Design implementation

### Architecture
- This project follows **event-driven** architecture. All actions will cause an event in the application
to control the fellow.


### Design
The Recipes are broken down into 3 parts :
1) Recipe Template
2) Recipe Builder
3) Recipe events
when a request comes to the application a **Recipe Template** will be created. That template will be filled by data that prepared by the user or by internal behavior. **Recipe Builder** will dynamically trigger a **Event** 
from that **context**.All the above parts will be covered below.

Lets see quick usage 
```php
Recipe::create(
                $data, function (RecipeTemplate $item) use ($id) {
                    $item->id($id);
                    $item->name();
                    $item->prepTime();
                    $item->difficulty();
                    $item->vegetarian();
                }
            );
```
 
### Architecture Abstractions

-  For expanding the functionality, abstractions will use traits, for example, `RedisPersistence`'s functionality will be expanded by
`RedisPersistenceTrait`.
### Request Abstraction
- Every request can be validated and filters the inputs dynamically. below codes show this abstraction

```php
abstract class AbstractRequest implements ValidateRequest
{

    use ValidateRequestTrait, SimplifyRequestBagTrait;
    /**
     * instance of request object
     *
     * @var Request
     */
    protected $requestInstance;

    /**
     * hold all errors
     *
     * @var array of errors
     */
    protected $errorBag = [];

    /**
     * return the object of Request Instance class
     *
     * @return mixed
     */
    public function getRequestInstance()
    {
        return $this->requestInstance;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract protected function rules();


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract protected function authorize();


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    abstract public function messages();

}

```
let's see one example of the implementation of the Abstraction in  (`CreateRequest.php`):
```php
class CreateRequest extends AbstractRequest
{

    public function __construct(Request $request)
    {
        $this->requestInstance = $request;
    }


    /**
     * Get the validation rules
     * these rules will be applied to request
     *
     * @return array
     */
    protected function rules()
    {
        return [
            "name" => ["required"],
            "prepTime" => ["required"],
            "difficulty" => ["required"],
        ];
    }

    /**
     * Determine if the user is authorized or not
     * if false returned , user is not able to access to resource
     *
     * @return bool
     */
    protected function authorize()
    {
        $headers = $this->getRequestInstance()->headers();

        return getAuth($headers);
    }

    /**
     * Get the error messages for
     *   the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "name.required" => "Recipe's name field is required",
            "prepTime.required" => "Recipe's prepTime field is mandatory",
            "difficulty.required" => "Recipe's difficulty field is mandatory",
        ];
    }


}

```
As you can see the method `rules()` will define the constraints on the request. method `authorize()` will indicate does this request needs **Authorization** or not, and finally `messages()` will show a related error message when any rule failed.

### Events
since every request will be converted into an event, the event abstraction will be focused in this project
below is a high-level abstraction for an event interface
```php
interface EventInterface
{
    /**
     * event type
     *
     * @return string
     */
    public static function getType() : string ;

    /**
     * get full qualified namespace prefix
     *
     * @return string
     */
    public static function getContext(): string;

    /**
     * get the full qualified namespace based on input
     *
     * @param  string $event
     * @return string
     */
    public static function getContextFromType(string $event): string;


    /**
     * event handler
     *
     * @return string
     */
    public function handle();

}
```

Since this project is developed as a production-ready application, thinking about how to scale it, is important. for satisfy this needs I added one simple Abstraction layer, under the `EventInterface`.
```php

abstract class AbstractRecipeEvent implements EventInterface
{

    /**
     * @var IteratorAggregate
     */
    protected $data;

    protected $persistenceDriver;


    /**
     * RecipeCreated constructor.
     *
     * @param IteratorAggregate $data
     */
    public function __construct(IteratorAggregate $data)
    {
        $this->data = $data;
        $this->persistenceDriver = static::getPersistentDriver();
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function getType(): string
    {
        return "Recipe";
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function getContext(): string
    {
        return "\\App\\ExposeApi\\Recipe\\Core\\Event\\";
    }

    /**
     * @inheritdoc
     *
     * @param  $event
     * @return string
     */
    public static function getContextFromType(string $event): string
    {
        return "\\App\\ExposeApi\\Recipe\\Core\\Event\\{$event}";
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public abstract function handle();


}
```

As you can see, the implementation of details will be remains to concrete classes(not in class abstraction).
let see one of these implementations in this project.

```php

final class RecipeCreated extends AbstractRecipeEvent implements IteratorAggregate, Jsonable
{

    use RedisTrait;

    /**
     * event handler
     * data will be PERSIST in redis
     *
     * @return string
     * @throws \Exception
     */
    public function handle()
    {
        $this->save($this->data->id, $this->toJson());

        return $this->getOrFail($this->data->id);
    }


    /**
     * @inheritdoc
     * @return     Traversable|void
     */
    public function getIterator()
    {
        return $this->data->getIterator();
    }

    /**
     * @inheritdoc
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->data->getFluent()->toJson($options);
    }

}
``` 

### Dispatch events
As mentioned above, the builder pattern used for this project and still is decoupled from event implementations.
 Before that let see how a Recipe class looks like:

```php
/**
 * Class Recipe
 *
 * @package App\ExposeApi\Recipe
 *
 * @method static \App\ExposeApi\Recipe\Builder create (array $data, \Closure $callback)
 * @method static \App\ExposeApi\Recipe\Builder delete (array $id, \Closure $callback = null)
 * @method static \App\ExposeApi\Recipe\Builder update (array $data, \Closure $callback)
 * @method static \App\ExposeApi\Recipe\Builder get (array $id)
 * @method static \App\ExposeApi\Recipe\Builder rate (array $data, \Closure $callback)
 * @method static \App\ExposeApi\Recipe\Builder search (array $data, \Closure $callback)
 *
 * @see \App\ExposeApi\Recipe\Builder
 */
class Recipe extends AbstractRecipe
{

    /**
     * @inheritdoc
     *
     * @return Builder|mixed
     */
    public static function getRecipeAccessor()
    {
        return new Builder();
    }
}
```
Recipe class will decide which object is responsible for access to Recipe functionalities .
And the AbstractRecipe class :
```php
<?php

namespace App\ExposeApi\Recipe;


abstract class AbstractRecipe
{
    /**
     * Get the recipe builder class instance
     *
     * @return mixed
     *
     * @see \App\ExposeApi\Recipe\Builder
     */
    abstract public static function getRecipeAccessor();

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  $method
     * @param  $arguments
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = static::getRecipeAccessor();

        if (!$instance) {
            throw new \RuntimeException("Recipe builder class does not exist");
        }


        return $instance->$method(...$arguments);
    }
}
```   


## Endpoints

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| List   | `GET`       | `/recipes`             | ✘         |
| Create | `POST`      | `/recipes`             | ✓         |
| Get    | `GET`       | `/recipes/{id}`        | ✘         |
| Update | `PUT/PATCH` | `/recipes/{id}`        | ✓         |
| Delete | `DELETE`    | `/recipes/{id}`        | ✓         |
| Rate   | `POST`      | `/recipes/{id}/rating` | ✘         |
| Search | `POST`      | `/recipes/search`      | ✘         |

### API Authentication
Below APIs needs Authorization in the header
- create
- update
- delete

Simply add an `Authorization` header, (Example:` Authorization: AccessKey {accessKey}`). to keep it as simple as in this project
`{accessKey}` can be any value (it **MUST** not be empty). 

for examples :
- ` Authorization: AccessKey 123456`  WORKS ✓
- ` Authorization: AccessKey 98745`   WORKS ✓
- ` Authorization: AccessKey fdgfgdfgfgf` WORKS ✓
- ` Authorization: AccessKey` NOT WORKS ✘
- ` Authorization: ` NOT WORKS ✘

**NOTE**
- `AccessKey` in ` Authorization: AccessKey 123456` is constant, and is mandatory.



### API Description
-----

APIs that needs **create** or **update** , **search** and **rating**, data **MUST** be passed in body as a valid json.
for example: 
~~~
{
	"name": "test name",
	"prepTime": "21 min",
	"vegetarian": false,
	"difficulty": "Hard"
}
~~~
All elements in **search** api will be **AND** together. for example below request means we are searching for a recipe that 
name=jack **AND** difficulty=hard
~~~
{
	"name": "jack",
	"difficulty": "hard"
}
~~~
**Rate** Api has below format :
for example if you want to rate the recipe with id :`f1d9ae6f-2bb2-42f4-a842-9e9cc658cad2` 
`POST /recipes/f1d9ae6f-2bb2-42f4-a842-9e9cc658cad2/rating
`
Body will be : 
~~~
{
  "rate":5
}
~~~


## Storage
data will be stored as (key, value) in `Redis`. at every update(create/delete/update/rating), data will be persisted in the disk in ASYNC mode. this also triggered as an event
```php
/**
     *  Asynchronously save the dataset to disk (in background)
     *
     * @return mixed
     */
    public function saveAsync()
    {
       return dispatch(RedisPersistence::getContextFromType('RedisPersistence'), $this->persistenceDriver);
    }

    /**
     * save and persist data on disk Asynchronously
     *
     * @param $key
     * @param $value
     */
    public function save($key, $value): void
    {
        $this->persistenceDriver->set($key, $value);

        $this->saveAsync();
    }
```

## Code Standards
I used `"squizlabs/php_codesniffer": "3.*"` as `require-dev`and Apply it to codes to make sure PSRs will be in place.

## Used Libraries
- klein/klein (as php router and service registeration, it is very light weight)
- ramsey/uui (to generate recipe id)
- predis/predis (Redis library management)
- squizlabs/php_codesniffer (PSRs standardize)
- phpunit/phpunit (unit test framweork)

## Requirements
- PHP 7.2+
- PHPUnit 7.0+

## How To Deploy

1) this project will use port `80` to connect to php container, make sure no one is using this port. you can make sure about that 
by running `sudo netstat -nlp | grep 80` command.

2) Run below commands from the project's root: (**all commands need root permission**)
```
docker-compose build
docker-compose up -d
docker exec -it exposeapi_php bash -c "composer install"
```

## Run Tests
All test located at the root of the project. currently `57 tests, 72 assertions` are provided.

**How to run :**
```
docker exec -it exposeapi_php bash -c "vendor/bin/phpunit tests/"
```
