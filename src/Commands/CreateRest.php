<?php

namespace Alirah\LaravelRest\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

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
    protected $signature = 'rest:make {model} {{--F|full}}';

    public function handle()
    {
        $this->modelFull = $this->argument('model');
        $this->model = array_reverse(explode("\\", $this->modelFull))[0];
        $this->modelLower = lcfirst($this->model);
        $this->tableName = '';

        $this->createRequests();
        $this->createResource();
        $this->createController();

        $this->createModel();
        $this->createMigration();

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
    public function createRequests(): void
    {
        $this->namespace = "App\\Http\\Request\\" . $this->modelFull;
        $storeRequestTemplate = $this->getTemplate('/stubs/request/StoreRequest.stub');
        $this->createFile($this->namespace, "StoreRequest.php", $storeRequestTemplate);

        $this->namespace = "App\\Http\\Request\\" . $this->modelFull;
        $updateRequestTemplate = $this->getTemplate('/stubs/request/UpdateRequest.stub');
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

        if (DB::table($this->tableName)) {
            $this->warn("Table with {$this->tableName} already exists.");
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
        $this->createFile($this->namespace, "{$this->model}.php", $model);
        $this->info("{$this->model} created");
    }

    public function camelCase2UnderScore($str, $separator = "_"): string
    {
        if (empty($str)) {
            return $str;
        }
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", $separator . "$0", $str);
        return strtolower($str);
    }

    /**
     * @return void
     */
    public function createFactory(): void
    {

    }
}
