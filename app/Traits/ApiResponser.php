<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser {

    /**
     * if response is successfully established
     *
     * @param array $data
     * @param number $code
     * @return void
     */
    private function successResponse($data, $code){
        return response()->json($data, $code);
    }

    /**
     * handle error response
     *
     * @param string $message
     * @param number $code
     * @return void
     */
    protected function errorResponse($message, $code){
        return response()->json([
            'error' => $message,
            'code' => $code,
        ], $code);
    }

    /**
     * Return multiple data from the request
     *
     * @param Collection $collection
     * @param integer $code
     * @return void
     */
    protected function showAll($code = 200, $description ,Collection $collection){
        return $this->successResponse([
            'code' => $code,
            'description' => $description,
            'contents' => $collection,
        ], $code);
    }

    /**
     * Return single value of a request
     *
     * @param Model $model
     * @param integer $code
     * @return void
     */
    protected function showOne($code = 200 ,$description ,Model $model){
        return $this->successResponse([
            'code' => $code,
            'description' => $description,
            'contents' => $model,
        ], $code);
    }
}
