<?php

namespace Core\Framework\Http;

use Core\Framework\Providers\RouteServiceProvider;
use Core\Application\Http\Exceptions\AppException;
use Exception;

class Kernel
{
    public function handle(): HttpResponse
    {
        $this->registerProviders();

        try {
            $request = new Request();
            return Router::runRoute($request);
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

    private function registerProviders(): void
    {
        new RouteServiceProvider()->init();
    }
}
