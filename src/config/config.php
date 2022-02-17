<?php

return [
    // to use swagger you have to install darkaonline/l5-swagger
    'swagger' => true,

    'model' => true,
    'migration' => true,
    'factory_seeder' => true,
    'test' => true,

    'route' => true,
    // file in the routes' folder
    // if you have another folder in routes use this pattern: v1/api.php
    'route_path' => 'api.php'
];


// to publish config run:
// php artisan vendor:publish --provider="Alirah\LaravelRest\LaravelRestServiceProvider" --tag="config"
