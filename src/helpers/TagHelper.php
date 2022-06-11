<?php
namespace wheelform\helpers;

class TagHelper
{
    const PATTERN = '/\[([^\]]*)\]/';

    public static function replacePlaseholders(string $string, $values)
    {
        return preg_replace_callback(self::PATTERN, function($m) use ($values) {
            return (!empty($values[$m[1]]) ? $values[$m[1]] : '');
        }, $string);
    }

    public static function getTags(array $lines)
    {
        $tags = [];

        foreach ($lines as $l) {
            $out = [];
            preg_match_all(self::PATTERN, $l, $out);
            if (!empty($out[1])) {
                foreach ($out[1] as $tag) {
                    $tags[$tag] = '';
                }
            }
        }

        return $tags;
    }
}
