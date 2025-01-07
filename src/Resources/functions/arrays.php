<?php

if (!function_exists('iterable_to_array')) {
    function iterable_to_array(iterable $input, bool $preserve_keys = true): array
    {
        if ($input instanceof Traversable) {
            return iterator_to_array($input, $preserve_keys);
        }

        return $preserve_keys ? (array)$input : array_values((array)$input);
    }
}

if (!function_exists('array_flatten')) {
    function array_flatten(iterable $input, int $depth = PHP_INT_MAX): array
    {
        $data = [];

        foreach ($input as $item) {
            $item = is_iterable($item) ? iterable_to_array($item) : $item;

            if (!is_array($item)) {
                $data[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : array_flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $data[] = $value;
                }
            }
        }

        return $data;
    }
}

if (!function_exists('array_collapse')) {
    function array_collapse(
        iterable $input,
        int      $depth = PHP_INT_MAX,
        string   $glue = DIRECTORY_SEPARATOR,
        string   $carry = ''
    ): array {
        $temp = [];

        foreach ($input as $key => $value) {
            if ($depth > 1 and is_iterable($value) and count($value) > 0) {
                $temp = array_merge($temp, array_collapse($value, $depth - 1, $glue, "{$carry}{$key}{$glue}"));
            } else {
                $temp["{$carry}{$key}"] = $value;
            }
        }

        return $temp;
    }
}

if (!function_exists('cartesian_product')) {
    function cartesian_product(iterable ...$inputs): array
    {
        $params = [[]];

        foreach ($inputs as $input) {
            $temp = [];
            foreach ($params as $partial) {
                foreach ($input as $element) {
                    $temp[] = array_merge($partial, [$element]);
                }
            }
            $params = $temp;
        }

        return $params;
    }
}
