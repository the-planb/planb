<?php

use PlanB\DS\Map\Map;
use PlanB\DS\Map\MapMutable;
use PlanB\DS\Vector\Vector;
use PlanB\DS\Vector\VectorMutable;
use PlanB\Text\TextList;

if (!function_exists('map')) {
    function map(iterable $input = [], ?callable $mapping = null, array $types = []): Map
    {
        return new Map($input, $mapping, $types);
    }
}

if (!function_exists('mutable_map')) {
    function mutable_map(iterable $input = [], ?callable $mapping = null, array $types = []): MapMutable
    {
        return new MapMutable($input, $mapping, $types);
    }
}

if (!function_exists('vector')) {
    function vector(iterable $input = [], ?callable $mapping = null, array $types = []): Vector
    {

        return new Vector($input, $mapping, $types);
    }
}

if (!function_exists('mutable_vector')) {
    function mutable_vector(iterable $input = [], ?callable $mapping = null, array $types = []): VectorMutable
    {
        return new VectorMutable($input, $mapping, $types);
    }
}

if (!function_exists('textList')) {
    function textList(iterable $input = [], ?callable $mapping = null): TextList
    {

        return TextList::collect($input, $mapping);
    }
}
