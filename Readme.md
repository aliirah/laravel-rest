# Laravel Rest

This is a Laravel Package to build RESTFUL API much easier. This package supports `Laravel 5.8+`.


> With this package you can build all the things needed for RESTFUL API with only one command. Such as Controller, Resource, Request, Model, Migration, Route, Test and swagger. Moreover, you can delete it as well.


_____________________________

# List of contents

- [Laravel Rest](/#Laravel-Rest)
- [List of contents](/#list-of-contents)
- [Install](/#Install)
- [Configure](/#Configure)
- [Swagger](/#Swagger)
- [Testing](/#Testing)
- [How to use](#How-to-use)
    - [Create](#Create)
    - [Delete](#Delete)
    - [Versioning](#Versioning)
        - [Versioning Create](#versioning-create)
        - [Versioning Delete](#versioning-delete)
    - [Rest Facade](#rest-facade)
    - [Example](#example)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

_____________________________


##Install

Via Composer

``` bash
$ composer require aliirah/laravel-rest
```

## Configure

If you are using `Laravel 5.5` or higher than you don't need to add the provider and alias. (Skip to b)

a. In your `config/app.php` file add these two lines.

```php
// In your providers array.
'providers' => [
    ...
    Alirah\LaravelRest\Provider\LaravelRestServiceProvider::class,
],

// In your aliases array.
'aliases' => [
    ...
    'Rest' => Alirah\LaravelRest\Facade\Rest::class,
],
```
b. then run the command in below to publish `config/laravel-rest.php` file in your config directory :

```php
$ php artisan vendor:publish --provider="Alirah\LaravelRest\Provider\LaravelRestServiceProvider" --tag="config"
```


After running the command , you can set your desired configuration.

_____________________________

```php
    // to use swagger you have to install darkaonline/l5-swagger
    'swagger' => false,
    'swagger_route_prefix' => 'api',

    'model' => true,
    'migration' => true,
    'factory_seeder' => true,
    'test' => true,

    'route' => true,
    // file in the routes' folder
    // if you have another folder in routes use this pattern: v1/api.php
    'route_path' => 'api.php'
```
_____________________________

## Swagger

In order to use Swagger, you need to follow these steps:

a. Run this command:

```php
$ composer require darkaonline/l5-swagger
```

b. Next, publish config/views from Service Provider:

```php
$ php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

c. Copy the code in below then paste it to the top of the Controller class in  App\Http\Controller\Controller :

```php
/**
 * @OA\Info (
 *      title="Laravel Rest Swagger",
 *      version="1.0.0",
 * )
 */
```

At the end, your controller must be like this:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info (
 *      title="Laravel Rest Swagger",
 *      version="1.0.0",
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

```

d. Turn swagger field to true in your config\laravel-rest.php:

```php
    'swagger' => true,
    'swagger_route_prefix' => 'api',
```

e. To generate your swagger run:

```php
$ php artisan l5-swagger:generate
```

To see the full documentation you can check https://github.com/DarkaOnLine/L5-Swagger.

_____________________________

## Testing

To get better results we suggest to use sqlite database for your tests. To use sqlite follow these steps:

a. First uncomment the DB_CONNECTION line in phpunit.xml in the root folder
```xml
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
<!--        <env name="DB_DATABASE" value=":memory:"/>-->
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php> 
```
b. Change the sqlite database path in config/database.php:
```php
    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
        ...
    ]
```
c. Create a file database.sqlite in database folder

d. To run your tests run command:
```php
$ php artisan test

//or

$  .\vendor\bin\phpunit
```

_____________________________
## How to use

### Create
To create a rest recourse, you should run:
```php
$ php artisan rest:make ModelName
```
This Command create these files:
* app\Models\ModelName.php
* app\Controllers\ModelName\ModelNameController.php
* app\Request\ModelName\StoreRequest.php
* app\Request\ModelName\UpdateRequest.php
* app\Resource\ModelName\ModelNameResource.php
* database\migrations\\...model_names.php
* database\factories\ModelNameFactory.php
* database\seeders\ModelNameSeeder.php
* tests\Feature\ModelName\ModelNameTest.php
* and remove the line below line in ./routes/api.php

```php
...
Route::apiResource('modelNames', \App\Http\Controllers\ModelName\ModelNameController::class);
```
- You can change the api.php file in config.
- For overriding a file with same name, it needs your permission in cmd.
- You can use -F or --force flag to force it.

### Delete
To delete a rest recourse, you should run:
```php
$ php artisan rest:delete ModelName
```
This Command delete these files:
* app\Models\ModelName.php
* app\Controllers\ModelName\ModelNameController.php
* app\Request\ModelName\StoreRequest.php
* app\Request\ModelName\UpdateRequest.php
* app\Resource\ModelName\ModelNameResource.php
* database\migrations\\...model_names.php
* database\factories\ModelNameFactory.php
* database\seeders\ModelNameSeeder.php
* tests\Feature\ModelName\ModelNameTest.php
* and add the line below line to ./routes/api.php

```php
...
Route::apiResource('modelNames', \App\Http\Controllers\ModelName\ModelNameController::class);
```
- You can change the api.php file in config.
- For deleting every file, it needs your permission in cmd.
- You can use -F or --force flag to force it.

### Versioning
For versioning you can put prefix for your swagger routes in your config:
```php
    ...,
    'swagger_route_prefix' => 'api',
    ...
```

#### Versioning Create
TO create version resource You can run:
```php
$ php artisan rest:make V1\ModelName
```
This Command create these files:
* app\Models\ModelName.php
* app\Controllers\V1\ModelName\ModelNameController.php
* app\Request\V1\ModelName\StoreRequest.php
* app\Request\V1\ModelName\UpdateRequest.php
* app\Resource\V1\ModelName\ModelNameResource.php
* database\migrations\\...model_names.php
* database\factories\ModelNameFactory.php
* database\seeders\ModelNameSeeder.php
* tests\Feature\V1\ModelName\ModelNameTest.php

#### Versioning Delete
To delete a version resource You can run:
```php
$ php artisan rest:delete V1\ModelName
```
This Command delete these files:
* app\Models\ModelName.php
* app\Controllers\V1\ModelName\ModelNameController.php
* app\Request\V1\ModelName\StoreRequest.php
* app\Request\V1\ModelName\UpdateRequest.php
* app\Resource\V1\ModelName\ModelNameResource.php
* database\migrations\\...model_names.php
* database\factories\ModelNameFactory.php
* database\seeders\ModelNameSeeder.php
* tests\Feature\V1\ModelName\ModelNameTest.php



### Rest Facade

The Rest facade is used for return JsonResource in controller.
```php
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::paginate(20);
        return Rest::ok(UserResource::collection($users));
    }
```

Available methods for Rest facade:

- `ok($data)`: return $data with 200 status code
- `accepted($data)`: return $data with 202 status code
- `badRequest($data)`: return $data with 400 status code
- `unauthorized($data)`: return $data with 401 status code
- `forbidden($data)`: return $data with 403 status code
- `notFound($data)`: return $data with 404 status code
- `error($data)`: return $data with 500 status code
- `custom($data, $statusCode)`: return $data with $statusCode status code

The $data should be Laravel resource or an array

### Example
When run ```php artisan rest:make Blog``` and enable swagger, this is the result:

app\Models\Blog.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];
}
```

app\Http\Controllers\Blog\BlogController
```php
<?php

namespace App\Http\Controllers\Blog;

use Alirah\LaravelRest\Facade\Rest;
use App\Http\Controllers\Controller;
use App\Http\Request\Blog\StoreRequest;
use App\Http\Request\Blog\UpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Blog;
use App\Http\Resource\Blog\BlogResource;

class BlogController extends Controller
{


    /**
     * @OA\Get(
     *      path="/api/blogs",
     *      operationId="getBlogsList",
     *      tags={"Blogs"},
     *      summary="Get list of blogs",
     *      description="Returns list of blogs",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *     @OA\JsonContent
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *     @OA\JsonContent
     *      )
     *     )
     */
    public function index(): JsonResponse
    {
        $blogs = Blog::paginate(20);
        // TODO handle query
        return Rest::ok(BlogResource::collection($blogs));
    }

/**
     * @OA\Post(
     *      path="/api/blogs",
     *      operationId="storeBlog",
     *      tags={"Blogs"},
     *      summary="Store new blog",
     *      description="Returns blog data",
     *      @OA\RequestBody(
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *     @OA\JsonContent
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *     @OA\JsonContent
     *      )
     * )
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $item = Blog::create($request->validated());

        return Rest::accepted(new BlogResource($item));
    }

/**
     * @OA\Get(
     *      path="/api/blogs/{id}",
     *      operationId="getBlogById",
     *      tags={"Blogs"},
     *      summary="Get blog information",
     *      description="Returns blog data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Blog id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *     @OA\JsonContent
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Blog Not Found",
     *     @OA\JsonContent
     *      )
     * )
     */
    public function show(Blog $blog): JsonResponse
    {
        // you can load relationships by using
        // $blog->load('relation-1', 'relation-2');

        return Rest::ok(new BlogResource($blog));
    }

/**
     * @OA\Put(
     *      path="/api/blogs/{id}",
     *      operationId="updateBlog",
     *      tags={"Blogs"},
     *      summary="Update existing blog",
     *      description="Returns updated blog data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Blog id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *     @OA\JsonContent
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Blog Not Found",
     *     @OA\JsonContent
     *      )
     * )
     */
    public function update(UpdateRequest $request, Blog $blog): JsonResponse
    {
        // TODO handle updated fields
        $blog->update($request->only(''));

        return Rest::accepted(new BlogResource($blog));
    }

/**
     * @OA\Delete(
     *      path="/api/blogs/{id}",
     *      operationId="deleteBlog",
     *      tags={"Blogs"},
     *      summary="Delete existing blog",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Blog id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *     @OA\JsonContent
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *     @OA\JsonContent
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Blog Not Found",
     *     @OA\JsonContent
     *      )
     * )
     */
    public function destroy(Blog $blog): JsonResponse
    {
        $blog->delete();

        return Rest::accepted([
            'message' => 'blog deleted successfully'
        ]);
    }
}
```

app\Http\Request\Blog\StoreRequest
```php
<?php

namespace App\Http\Request\Blog;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        //
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // TODO validation
        return [
            //
        ];
    }
}
```

app\Http\Request\Blog\UpdateRequest.php
```php
<?php

namespace App\Http\Request\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        //
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // TODO validation
        return [
            //
        ];
    }
}
```

app\Http\Resource\Blog\BlogResource.php
```php
<?php

namespace App\Http\Resource\Blog;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        // TODO return Blog fields
        return [
            'id' => $this->id,
            // your fields
            'createdAt' => $this->created_at
        ];
    }
}
```
database\migrations\\...create_blogs_table.php
```php
<?php

 use Illuminate\Database\Migrations\Migration;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Support\Facades\Schema;

 return new class extends Migration
 {
     /**
      * Run the migrations.
      *
      * @return void
      */
     public function up()
     {
          if (!Schema::hasTable('blogs')) {
             Schema::create('blogs', function (Blueprint $table) {
                 $table->id();
                 // TODO table fields
                 $table->timestamps();
             });
          }
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::dropIfExists('blogs');
     }
 };
```

database\factories\BlogFactory.php
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // TODO factory fields
        return [
            // for example: 'title' => $this->faker->sentence(1),
        ];
    }
}
```

database\seeders\BlogSeeder.php
```php
<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // TODO handle factory
        // TODO you should add it to DatabaseSeeder
        // for example:
        // public function run()
        //    {
        //        $this->call([
        //            BlogSeeder::class
        //        ]);
        //    }
        Blog::factory(10)->create();
    }
}
```
tests\Feature\Blog\BlogTests.php
```php
<?php

namespace Tests\Feature\Blog;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected array $dataStruct;

    public function setUp(): void
    {
        parent::setUp();

        $this->dataStruct = [
            'id',
            // TODO Enter Fields that return from BlogResource
            'createdAt'
        ];

        Blog::factory(10)->create();
    }

    public function test_index()
    {
        $response = $this->json('get', '/api/blogs');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    $this->dataStruct
                ]
            ]);
    }

    public function test_store()
    {
        $response = $this->json('post', '/api/blogs', [
            // Todo enter a test data to store
            // for example 'title' => 'foo'
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'data' => $this->dataStruct
            ]);
    }

    public function test_show()
    {
        $response = $this->json('get', '/api/blogs/1');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => $this->dataStruct
            ]);
    }

    public function test_update()
    {
        $response = $this->json('put', '/api/blogs/1', $this->dataStruct);

        $response->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'data' => $this->dataStruct
            ]);
    }

    public function test_delete()
    {
        $response = $this->json('delete', '/api/blogs/1');

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
```
routes/api.php
```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


.
..
...
Route::apiResource('blogs', \App\Http\Controllers\Blog\BlogController::class);
```

-----------------------------------------------
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has been changed recently.


## Security

If you discover any security related issues, please email alirahgoshy@gmail.com instead of using the issue tracker.

## Credits

- [Ali Rahgoshay][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-en]: README.md
[link-packagist]: #
[link-author]: https://github.com/aliirah
