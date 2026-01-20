<?php

namespace Core\Framework\Root;

use ReflectionClass;

class Container
{
    protected static $instance;
    private array $bindings = [];

    public static function setInstance(Container $container): void
    {
        self::$instance = $container;
    }

    public static function getInstance(): Container
    {
        if (!self::$instance) {
            throw new \RuntimeException('Container not initialized');
        }

        return self::$instance;
    }
    

    public function bind(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function make(string $abstract, array $parameters = [])
    {
        $concrete = $this->bindings[$abstract] ?? $abstract;
        if (is_callable($concrete)) {
            return $concrete();
        }
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
        }

        return new $concrete(...$dependencies);
    } 
}