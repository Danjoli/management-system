<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    /** @var array<string, mixed> */
    private array $bindings = [];

    public function set(string $id, mixed $value): void
    {
        $this->bindings[$id] = $value;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->bindings)) {
            $value = $this->bindings[$id];
            return $value instanceof Closure ? $value($this) : $value;
        }

        if (!class_exists($id)) {
            throw new RuntimeException("Dependency {$id} is not registered.");
        }

        $reflection = new ReflectionClass($id);
        $constructor = $reflection->getConstructor();
        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return new $id();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException("Cannot resolve {$id}::\${$parameter->getName()}.");
            }
            $dependencies[] = $this->get((string) $type);
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
