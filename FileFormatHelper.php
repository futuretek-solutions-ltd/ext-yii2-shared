<?php

namespace futuretek\shared;

class FileFormatHelper
{
    public static function isPdfFile(string $fileName): bool
    {
        return self::isPdfStream(self::_loadFile($fileName));
    }

    public static function isPdfStream(string $content): bool
    {
        return preg_match('/^%PDF-/', $content);
    }

    public static function isXmlFile(string $fileName): bool
    {
        return self::isXmlStream(self::_loadFile($fileName));
    }

    public static function isXmlStream(string $content): bool
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($content);
        libxml_clear_errors();

        return $doc instanceof \SimpleXMLElement;
    }

    public static function isPngFile(string $fileName): bool
    {
        return imagecreatefrompng($fileName) !== false;
    }

    public static function isPngStream(string $content): bool
    {
        $fileName = tempnam(sys_get_temp_dir(), 'fts');
        file_put_contents($fileName, $content);

        return self::isPngFile($fileName);
    }

    public static function isJpgFile(string $fileName): bool
    {
        return imagecreatefromjpeg($fileName) !== false;
    }

    public static function isJpgStream(string $content): bool
    {
        $fileName = tempnam(sys_get_temp_dir(), 'fts');
        file_put_contents($fileName, $content);

        return self::isJpgFile($fileName);
    }

    private static function _loadFile(string $fileName): string
    {
        if (!is_readable($fileName)) {
            throw new \RuntimeException('File not exists or is not readable.');
        }

        $content = file_get_contents($fileName);
        if ($content === false) {
            throw new \RuntimeException('Error while reading file.');
        }

        return $content;
    }
}
