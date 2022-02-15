<?php

namespace Alirah\LaravelRest\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;

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

    public mixed $composer;
    public string $model;
    public string $modelLower;
    public string $modelFull;
    public array $models;

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
    protected $signature = 'rest:delete {model} {--F|force} {--D|withDirectory}';

    public function handle()
    {
        $this->modelFull = $this->argument('model');
        $this->model = array_reverse(explode("\\", $this->modelFull))[0];
        $this->modelLower = lcfirst($this->model);

        // get all options
        $force = $this->option('force');
        $deleteDirectory = $this->option('withDirectory');

//        // check model exists
//        $models = $this->getAllModels();
//        if (!in_array($this->model, $models)){
//            $this->warn("There is no Model with");
//            return;
//        }

        // handle confirm
        $deleteDirectory
            ? $question = "Do you want to delete rest {$this->model} (Model, Controller, Request, Resource) with it's whole directory ?!!"
            : $question = "Do you want to delete rest {$this->model} (Model, Controller, Request, Resource) ?!";
        if ($force) $delete = true;
        elseif ($this->confirm($question)) $delete = true;
        else $delete = false;

        if ($delete) {
            $allFiles = $this->getAllFiles();
            $this->deleteAllFiles($allFiles);

            if ($deleteDirectory) $this->deleteAllFolders($allFiles);

            $this->composer->dumpOptimized();
        }
    }

    /**
     * @return string[]
     */
    public function getAllFiles(): array
    {
        return [
            [
                'path' => app_path() . "\\Http\\Request\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "StoreRequest.php",
                'has_folder' => true,
            ],
            [
                'path' => app_path() . "\\Http\\Request\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "UpdateRequest.php",
                'has_folder' => true
            ],
            [
                'path' => app_path() . "\\Http\\Resource\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "{$this->model}Resource.php",
                'has_folder' => true
            ],
            [
                'path' => app_path() . "\\Http\\Controllers\\{$this->modelFull}" . DIRECTORY_SEPARATOR . "{$this->model}Controller.php",
                'has_folder' => true
            ],
            [
                'path' => app_path() . "\\Models" . DIRECTORY_SEPARATOR . "{$this->model}.php",
                'has_folder' => false
            ],
        ];
    }

    /**
     * @param array $allFiles
     * @return void
     */
    public function deleteAllFiles(array $allFiles): void
    {
        foreach ($allFiles as $file) {
            $path = $file['path'];
            File::delete($path);
            $this->info("Removes $path");
        }
    }

    /**
     * @param array $allFiles
     * @return void
     */
    public function deleteAllFolders(array $allFiles): void
    {
        foreach ($allFiles as $file) {
            if ($file['has_folder']) {
                $path = $file['path'];
                $folder = str_replace(array_reverse(explode("\\", $path))[0], "", $path);

                if (File::exists($folder))
                    File::deleteDirectory($folder);

                $this->info("Removes $folder");
            }
        }
    }

    /**
     * @return array
     */
    public function getAllModels(): array
    {
        $models = [];
        $modelsPath = app_path('Models');
        $modelFiles = File::allFiles($modelsPath);
        foreach ($modelFiles as $modelFile) {
            $models[] = $modelFile->getFilenameWithoutExtension();
        }

        return $models;
    }
}
