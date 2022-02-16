<?php

namespace Alirah\LaravelRest\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateRest extends Command
{
    /**
     * The console command name
     *
     * @var string
     */
    protected $name = 'rest:make';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Create New LaravelRest';

    public mixed $composer;
    public string $model;
    public string $modelLower;
    public string $modelFull;
    public string $namespace;
    public string $tableName;
    public bool $force;

    public function __construct()
    {
        parent::__construct();

        $this->composer = app()['composer'];
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rest:make {model} {{--F|force}}';

    public function handle()
    {
        $this->modelFull = str_replace("/", "\\", $this->argument('model'));
        $this->model = array_reverse(explode("\\", $this->modelFull))[0];
        $this->modelLower = lcfirst($this->model);
        $this->tableName = '';

        $this->force = $this->option('force');

        $this->createStoreRequests();
        $this->createUpdateRequests();
        $this->createResource();
        $this->createController();

        $this->createModel();
        $this->createMigration();

        $this->createFactory();
        $this->createSeeder();

        $this->composer->dumpOptimized();
    }

    /**
     * @param $path
     * @param $fileName
     * @param $contents
     * @param string $baseFolder
     * @return void
     */
    public function createFile($path, $fileName, $contents, string $baseFolder = 'app')
    {
        $pathWithOutApp = str_replace('App', '', $path);

        if ($baseFolder == 'app') $path = app_path() . $pathWithOutApp . DIRECTORY_SEPARATOR;
        else $path = $baseFolder . $pathWithOutApp . DIRECTORY_SEPARATOR;

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fullPath = $path.$fileName;

        file_put_contents($fullPath, $contents);
    }

    /**
     * @param $stub
     * @param bool $hasNamespace
     * @return array|bool|string
     */
    public function getTemplate($stub, bool $hasNamespace = true): array|bool|string
    {
        if ($hasNamespace) $namespace = $this->namespace;
        else $namespace = null;

        return str_replace(
            ['{{ namespace }}', '{{ modelFull }}', '{{ model }}', '{{ modelLower }}', '{{ tableName }}'],
            [$namespace, $this->modelFull, $this->model, $this->modelLower, $this->tableName],
            file_get_contents(dirname(__DIR__ ,1) . $stub)
        );
    }

    /**
     * @return void
     */
    public function createStoreRequests(): void
    {
        $this->namespace = "App\\Http\\Request\\" . $this->modelFull;
        $storeRequestTemplate = $this->getTemplate('/stubs/request/StoreRequest.stub');

        $existsPath = app_path() . "\\Http\\Request\\{$this->modelFull}\\StoreRequest.php";
        if (!$this->force && File::exists($existsPath)) {
            $this->warn("{$this->modelFull}\\StoreRequest already exists.");
            if (!$this->confirm('Do you want to override StoreRequest?')) return;
        }

        $this->createFile($this->namespace, "StoreRequest.php", $storeRequestTemplate);
    }

    /**
     * @return void
     */
    public function createUpdateRequests(): void
    {
        $this->namespace = "App\\Http\\Request\\" . $this->modelFull;
        $updateRequestTemplate = $this->getTemplate('/stubs/request/UpdateRequest.stub');

        $existsPath = app_path() . "\\Http\\Request\\{$this->modelFull}\\UpdateRequest.php";
        if (!$this->force && File::exists($existsPath)) {
            $this->warn("{$this->modelFull}\\UpdateRequest already exists.");
            if (!$this->confirm('Do you want to override UpdateRequest?')) return;
        }

        $this->createFile($this->namespace, "UpdateRequest.php", $updateRequestTemplate);
        $this->info('StoreRequest & UpdateRequest created');
    }

    /**
     * @return void
     */
    public function createResource(): void
    {
        $this->namespace = "App\\Http\\Resource\\" . $this->modelFull;
        $resource = $this->getTemplate('/stubs/resource/Resource.stub');

        $existsPath = app_path() . "\\Http\\Resource\\{$this->modelFull}\\{$this->model}Resource.php";

        if (!$this->force && File::exists($existsPath)) {
            $this->warn("{$this->modelFull}Resource already exists.");
            if (!$this->confirm('Do you want to override resource?')) return;
        }

        $this->createFile($this->namespace, "{$this->model}Resource.php", $resource);
        $this->info("{$this->model}Resource created");
    }

    /**
     * @return void
     */
    public function createController(): void
    {
        $this->namespace = "App\\Http\\Controllers\\" . $this->modelFull;
        $controller = $this->getTemplate('/stubs/controller/Controller.stub');

        $existsPath = app_path() . "\\Http\\Controllers\\{$this->modelFull}\\{$this->model}Controller.php";
        if (!$this->force && File::exists($existsPath)) {
            $this->warn("{$this->modelFull}Controller already exists.");
            if (!$this->confirm('Do you want to override controller?')) return;
        }

        $this->createFile($this->namespace, "{$this->model}Controller.php", $controller);
        $this->info("{$this->model}Controller created");
    }

    /**
     * @return void
     */
    public function createMigration(): void
    {
        $this->tableName = Str::plural($this->camelCase2UnderScore($this->model));
        $fullName = date('Y_m_d_His', time()) . "_create_{$this->tableName}_table";
        $this->namespace = '/migrations/';

        if (!$this->force && DB::table($this->tableName)) {
            $this->warn("Table {$this->tableName} already exists.");
            if (!$this->confirm('Do you want to create migration anyway?')) return;
        }

        $migration = $this->getTemplate('/stubs/migration/migration.stub', false);
        $this->createFile($this->namespace, "$fullName.php", $migration, database_path());
        $this->info("$fullName.php created");
    }

    /**
     * @return void
     */
    public function createModel(): void
    {
        $this->namespace = "App\\Models";
        $model = $this->getTemplate('/stubs/model/Model.stub');

        $existsPath = app_path() . "\\Models\\$this->model.php";
        if (!$this->force && File::exists($existsPath)) {
            $this->warn("model {$this->model} already exists.");
            if (!$this->confirm('Do you want to override model?')) return;
        }

        $this->createFile($this->namespace, "{$this->model}.php", $model);
        $this->info("{$this->model} Model created");
    }

    /**
     * @return void
     */
    public function createFactory(): void
    {
        $this->namespace = "\\factories";
        $factory = $this->getTemplate('/stubs/factory/Factory.stub');

        $existsPath = database_path() . "\\factories\\{$this->model}Factory.php";
        if (!$this->force && File::exists($existsPath)) {
            $this->warn("{$this->model}Factory already exists.");
            if (!$this->confirm('Do you want to override factory?')) return;
        }

        $this->createFile($this->namespace, "{$this->model}Factory.php", $factory, database_path());
        $this->info("{$this->model}Factory created");
    }

    /**
     * @return void
     */
    public function createSeeder(): void
    {
        $this->namespace = "\\seeders";
        $seeder = $this->getTemplate('/stubs/seeder/Seeder.stub');

        $existsPath = database_path() . "\\seeders\\{$this->model}Seeder.php";
        if (!$this->force && File::exists($existsPath)) {
            $this->warn("{$this->model}Seeder already exists.");
            if (!$this->confirm('Do you want to override seeder?')) return;
        }

        $this->createFile($this->namespace, "{$this->model}Seeder.php", $seeder, database_path());
        $this->info("{$this->model}Seeder created");
    }

    /**
     * @param $str
     * @param string $separator
     * @return string
     */
    public function camelCase2UnderScore($str, string $separator = "_"): string
    {
        if (empty($str)) return $str;

        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", $separator . "$0", $str);
        return strtolower($str);
    }
}
