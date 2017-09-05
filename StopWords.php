<?php

namespace futuretek\shared;

/**
 * Class StopWords
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz>
 * @license Apache-2.0
 * @link    http://www.futuretek.cz
 */
class StopWords
{
    /**
     * Get stop-words list in array format
     *
     * @param string $language Language code (en, es, cs)
     * @return array
     */
    public static function getArray($language)
    {
        $fileName = __DIR__ . '/stop-words/' . $language . '.txt';
        if (file_exists($fileName)) {
            return array_map('trim', file($fileName));
        }

        return [];
    }

    /**
     * Get stop-words list in string format - separated by defined separator
     *
     * @param string $language Language code (en, es, cs)
     * @param string $separator word separator
     * @return string
     */
    public static function getString($language, $separator = ',')
    {
        $fileName = __DIR__ . '/stop-words/' . $language . '.txt';
        if (file_exists($fileName)) {
            return implode($separator, array_map('trim', file($fileName)));
        }

        return '';
    }
}