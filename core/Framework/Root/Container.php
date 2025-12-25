<?php

namespace Core\Framework\Root;

use ReflectionClass;

class Container
{
    private array $bindings = [];

    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function make(string $abstract, array $parameters = [])
    {
        try {
        $concrete = $this->bindings[$abstract] ?? $abstract;

        $reflector = new ReflectionClass($concrete);
        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            return new $concrete();
        }

        $constructorParams = $constructor->getParameters();
        $dependencies = [];

        foreach ($constructorParams as $constructorParam) {
            $name = $constructorParam->getName();
            if (array_key_exists($name, $parameters)) {
                $dependencies[] = $parameters[$name];
                continue;
            }

            $type = $constructorParam->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->make($type->getName());
            }
        }} catch (\Throwable $e) {
            throw $e;
            die;
        }

        return new $concrete(...$dependencies);
    } 
}