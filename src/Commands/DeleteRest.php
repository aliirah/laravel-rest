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
    public string|array|bool|null $force;

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
    protected $signature = 'rest:delete {model} {--F|force}';

    public function handle()
    {
        $this->modelFull = $this->argument('model');
        $this->model = array_reverse(explode("\\", $this->modelFull))[0];
        $this->modelLower = lcfirst($this->model);

        // get all options
        $this->force = $this->option('force');

        $allFiles = $this->getAllFiles();
        $this->deleteAllFiles($allFiles);

        $this->composer->dumpOptimized();
    }

    /**
     * @return string[]
     */
    public function getAllFiles(): array
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
            if (File::exists($path)) {
                if ($this->force) {
                    File::delete($path);
                    $this->info("Removes $path");
                } else if ($this->confirm("Do you want to delete $path")) {
                    File::delete($path);
                    $this->info("Removes $path");
                };
            }
        }
    }
}
