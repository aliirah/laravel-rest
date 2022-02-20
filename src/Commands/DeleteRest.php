<?php

namespace Alirah\LaravelRest\Commands;

use Alirah\LaravelRest\Util;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeleteRest extends Command
{
    /**
     * The console command name
     *
     * @var string
     */
    protected $name = 'rest:delete';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Delete LaravelRest';

    public $composer;
    public $model;
    public $modelLower;
    public $modelLowerPlural;
    public $modelFull;
    public $namespace;
    public $tableName;
    public $force;
    public $config;
    public $util;


    public function __construct()
    {
        parent::__construct();

        $this->util = new Util;
        $this->composer = app()['composer'];
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rest:delete {model} {--F|force}';

    public function handle()
    {
        $this->config = config('laravel-rest');

        $this->modelFull = $this->util->transformInput($this->argument('model'));
        $this->model = array_reverse(explode("\\", $this->modelFull))[0];
        $this->modelLower = lcfirst($this->model);
        $this->modelLowerPlural = Str::plural($this->modelLower);
        $this->tableName = '';

        // get all options
        $this->force = $this->option('force');

        $this->deleteAllFiles();
        $this->replaceStrings();

        $this->composer->dumpOptimized();
    }

    /**
     * @return string[]
     */
    public function getAllFilesForDeletes(): array
    {
        return [
            [
                'path' => app_path() . "\\Http\\Request\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "StoreRequest.php",
            ],
            [
                'path' => app_path() . "\\Http\\Request\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "UpdateRequest.php",
            ],
            [
                'path' => app_path() . "\\Http\\Resource\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "{$this->model}Resource.php",
            ],
            [
                'path' => app_path() . "\\Http\\Controllers\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "{$this->model}Controller.php",
            ],
            [
                'path' => app_path() . "\\Models" . DIRECTORY_SEPARATOR . "{$this->model}.php",
            ],
            [
                'path' => database_path() . "\\factories" . DIRECTORY_SEPARATOR . "{$this->model}Factory.php",
            ],
            [
                'path' => database_path() . "\\seeders" . DIRECTORY_SEPARATOR . "{$this->model}Seeder.php",
            ],
            [
                'path' => base_path() . "\\tests\\Feature\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "{$this->model}Test.php",
            ]
        ];
    }

    /**
     * @return void
     */
    public function deleteAllFiles(): void
    {
        $allDeleteFiles = $this->getAllFilesForDeletes();
        foreach ($allDeleteFiles as $file) {
            $path = $file['path'];
            if (File::exists($path)) {
                if ($this->force) {
                    File::delete($path);
                    $this->info("Removes $path");
                } else if ($this->confirm("Do you want to delete $path")) {
                    File::delete($path);
                    $this->info("Removes $path");
                }
            }
        }
    }

    public function getAllReplaceFiles(): array
    {
        $routePath = $this->config['route_path'] ?? 'api.php';
        return [
            [
                'path' => base_path("\\routes\\$routePath"),
                'old_string' => "Route::apiResource('{$this->modelLowerPlural}', \App\Http\Controllers\\$this->modelFull\\{$this->model}Controller::class);",
                'new_string' => ""
            ]
        ];
    }

    public function replaceStrings()
    {
        $allReplaceFiles = $this->getAllReplaceFiles();

        foreach ($allReplaceFiles as $file) {
            $path = $file['path'];
            $oldString = $file['old_string'];
            $newString = $file['new_string'];

            $match = $this->util->matchInFile($path, $oldString);

            if ($match && $match > 0) {
                if ($this->force) {
                    file_put_contents($path, str_replace($oldString, $newString, file_get_contents($path)));
                    $this->info("Removes $oldString");
                } else if ($this->confirm("Do you want to delete $oldString")) {
                    file_put_contents($path, str_replace($oldString, $newString, file_get_contents($path)));
                    $this->info("Removes $oldString");
                }
            }
        }
    }
}
