<?php

return [
    // to use swagger you have to install darkaonline/l5-swagger
    'swagger' => true,

    'model' => true,
    'migration' => true,
    'factory_seeder' => true,
    'test' => true,
];


// to publish config run:
// php artisan vendor:publish --provider="Alirah\LaravelRest\LaravelRestServiceProvider" --tag="config"
