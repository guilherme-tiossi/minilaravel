<?php

namespace Bootstrap\Http;

use App\Http\Exceptions\AppException;
use Bootstrap\Http\Router;
use Exception;

class Kernel
{
    public function handle(): HttpResponse
    {
        try {
            $request = new Request();
            $router = Router::getInstance();
            return $router->runRoute($request);
        } catch (AppException $e) {
            return new HttpResponse($e->getCode(), ['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return new HttpResponse(500, [
                'message' => 'ERROR: a fatal exception has occured',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }
}
