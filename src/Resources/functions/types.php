<?php

if (! function_exists('is_of_the_type')) {
    function is_of_the_type(mixed $value, string ...$types): bool
    {
        if (0 === count($types)) {
            return false;
        }

        $temp = false;
        foreach ($types as $type) {
            $type = strtolower($type);
            $temp = $temp
                || match ($type) {
                    'mixed' => true,
                    'object' => is_object($value),
                    'resource' => is_resource($value),
                    'array' => is_array($value),
                    'string' => is_string($value),
                    'int' => is_int($value),
                    'integer' => is_integer($value),
                    'float' => is_float($value),
                    'double' => is_double($value),
                    'bool', 'boolean' => is_bool($value),
                    'null' => is_null($value),
                    'callable' => is_callable($value),
                    'countable' => is_countable($value),
                    'iterable' => is_iterable($value),
                    default => is_a($value, $type)
                };
        }

        return $temp;
    }
}

if (! function_exists('type_of')) {
    function type_of(mixed $value): string
    {
        $type = is_object($value) ? $value::class : gettype($value);

        return match ($type) {
            'NULL' => 'null',
            Closure::class => 'callable',
            'resource (closed)' => 'resource',
            default => $type
        };
    }
}
