<?php

namespace App\Azhoras\Container;

use ReflectionClass;
use ReflectionNamedType;

class Container
{
    private array $bindings = [];

    // Registra uma implementação concreta para uma interface
    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    // Resolve uma classe automaticamente
    public function make(string $class): object
    {
        // Se tem binding registrado, usa ele
        if (isset($this->bindings[$class])) {
            return ($this->bindings[$class])($this);
        }

        // Senão, tenta resolver automaticamente via Reflection
        return $this->resolve($class);
    }

    private function resolve(string $class): object
    {
        $reflection  = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        // Sem construtor — instancia direto
        if (!$constructor) {
            return new $class();
        }

        $dependencies = array_map(function ($param) use ($class) {
            $type = $param->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new \RuntimeException(
                    "Não foi possível resolver o parâmetro '{$param->getName()}' em '{$class}'."
                );
            }

            return $this->make($type->getName());
        }, $constructor->getParameters());

        return $reflection->newInstanceArgs($dependencies);
    }
}