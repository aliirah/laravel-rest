<?php

namespace Alirah\LaravelRest;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class LaravelRest
{
    /**
     * @param $data
     * @param $code
     * @return object
     */
    public function response($data, $code): object
    {
        if ($data instanceof JsonResource)
            return $data
                ->response()
                ->setStatusCode($code);

        return response()
            ->json($data)
            ->setStatusCode($code);
    }

    // for get request
    // response 200
    public function ok($data): object
    {
        return $this->response($data , Response::HTTP_OK);
    }

    // for post,put,patch,delete request
    // response 201
    public function accepted($data): object
    {
        return $this->response($data , Response::HTTP_ACCEPTED);
    }

    // response 400
    public function badRequest($data): object
    {
        return $this->response($data , Response::HTTP_BAD_REQUEST);
    }

    // response 401
    public function unauthorized($data): object
    {
        return $this->response($data , Response::HTTP_UNAUTHORIZED);
    }

    // response 403
    public function forbidden($data): object
    {
        return $this->response($data , Response::HTTP_FORBIDDEN);
    }

    // response 404
    public function notFound($data): object
    {
        return $this->response($data , Response::HTTP_NOT_FOUND);
    }

    // response 500
    public function error($data): object
    {
        if (env('APP_DEBUG', false)) {
            return $this->response($data , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->response(__('status.error') , Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function custom($data , $statusCode): object
    {
        return $this->response($data ,$statusCode);
    }
}
