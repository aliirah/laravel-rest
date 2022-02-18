<?php

namespace Alirah\LaravelRest\Facade;

use Illuminate\Support\Facades\Facade;

class Rest extends Facade
{
    /**
     * @method static ok(mixed $array)
     * @method static accepted(mixed $array)
     * @method static badRequest(mixed $array)
     * @method static unauthorized(mixed $array)
     * @method static forbidden(mixed $array)
     * @method static notFound(mixed $array)
     * @method static error(mixed $array)
     * @method static custom(mixed $array , int $statusCode)
     */

    /**
     * Get the binding in the IOC container
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Alirah\LaravelRest\Facade\LaravelRest';
    }
}
