<?php

namespace PlanB\Text;

final class Text
{
    public static function explode(string $text, ?string $separator = null, ?int $limit = null): TextList
    {
        if (is_null($separator)) {
            return self::split($text, '/\s+/', $limit ?? +-1);
        }

        if (is_null($limit)) {
            return TextList::collect(explode($separator, $text));
        }

        return TextList::collect(explode($separator, $text, $limit));
    }

    public static function split(string $text, string $pattern, int $limit = -1, int $flags = 0): TextList
    {
        return TextList::collect(preg_split($pattern, $text, $limit, $flags));
    }

    //    public static function camelCaseTo(string $separator = ' '): string
    //    {
    //        $filter = new CamelCaseToSeparator($separator);
    //        return $filter->filter('ThisIsMyContent');
    //    }

}
