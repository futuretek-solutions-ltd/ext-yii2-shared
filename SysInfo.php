<?php

namespace futuretek\shared;

/**
 * Class SysInfo
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz>
 * @license Apache-2.0
 * @link    http://www.futuretek.cz
 */
class SysInfo
{
    /**
     * Get system load information
     *
     * @return array Load in last 1, 5 and 10 minutes
     * @static
     */
    public static function loadAverage()
    {
        return explode(' ', file_get_contents('/proc/loadavg'));
    }

    /**
     * Get information about memory
     *
     * @return array Get associative array with information about memory. Array key can be:
     *               <ul>
     *               <li>MemTotal</li>
     *               <li>MemFree</li>
     *               <li>Buffers</li>
     *               <li>Cached</li>
     *               <li>SwapCached</li>
     *               <li>Active</li>
     *               <li>Inactive</li>
     *               <li>Active(anon)</li>
     *               <li>Inactive(anon)</li>
     *               <li>Active(file)</li>
     *               <li>Inactive(file)</li>
     *               <li>Unevictable</li>
     *               <li>Mlocked</li>
     *               <li>SwapTotal</li>
     *               <li>SwapFree</li>
     *               <li>Dirty</li>
     *               <li>Writeback</li>
     *               <li>AnonPages</li>
     *               <li>Mapped</li>
     *               <li>Shmem</li>
     *               <li>Slab</li>
     *               <li>SReclaimable</li>
     *               <li>SUnreclaim</li>
     *               <li>KernelStack</li>
     *               <li>PageTables</li>
     *               <li>NFS_Unstable</li>
     *               <li>Bounce</li>
     *               <li>WritebackTmp</li>
     *               <li>CommitLimit</li>
     *               <li>Committed_AS</li>
     *               <li>VmallocTotal</li>
     *               <li>VmallocUsed</li>
     *               <li>VmallocChunk</li>
     *               <li>HardwareCorrupted</li>
     *               <li>AnonHugePages</li>
     *               <li>Hugepagesize</li>
     *               <li>DirectMap4k</li>
     *               <li>DirectMap2M</li>
     *               </ul>
     * @static
     */
    public static function getMemInfo()
    {
        $result = [];
        if ($n = preg_match_all('/^([\S]+):\s+(\d+)\skB$/im', file_get_contents('/proc/meminfo'), $matches)) {
            for ($i = 0; $i < $n; $i++) {
                $result[$matches[1][$i]] = $matches[2][$i];
            }
        }

        return $result;
    }

    /**
     * Get disk usage on UNIX OS
     *
     * @return array Partitions space usage. Every partition (array element) consist of:
     *               <ul>
     *               <li>0 - Device</li>
     *               <li>1 - Capacity in kB</li>
     *               <li>2 - Used kB</li>
     *               <li>3 - Available Kb</li>
     *               <li>4 - Use percentage</li>
     *               <li>5 - Mount point</li>
     *               </ul>
     * @static
     */
    public static function getDiskUsage()
    {
        $result = [];
        $lines = explode("\n", trim(shell_exec('df')));
        array_shift($lines);
        foreach ($lines as &$line) {
            if (substr($line, 0, 1) === '/') {
                $result[] = explode("\t", $line);
            }
        }

        return $result;
    }

    /**
     * Get unique system ID
     *
     * @return string System ID
     * @static
     */
    public static function getHostId()
    {
        if (self::isWindows()) {
            $uuid = explode("\r\n", trim(shell_exec('wmic csproduct get UUID')));

            return (count($uuid) === 2 ? $uuid[1] : false);
        } else {
            $uuid = trim(shell_exec('hostid'));
            return ($uuid === null ? false : $uuid);
        }
    }

    /**
     * Get server hostname
     *
     * @return string Server hostname
     * @static
     */
    public static function getHostname()
    {
        return php_uname('n');
    }

    public static function isWindows()
    {
        return (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN');
    }

    /**
     * Identify the version of php
     *
     * @return string
     */
    public static function checkPhpVersion()
    {
        $version = null;

        if (defined('PHP_VERSION')) {
            $version = PHP_VERSION;
        } else {
            $version = phpversion('');
        }

        //Case management system of ubuntu, php version return 5.2.4-2ubuntu5.2
        if (strpos($version, '-') !== false) {
            $version = substr($version, 0, strpos($version, '-'));
        }

        return $version;
    }

    /**
     * Get the server variable SERVER_NAME
     *
     * @return string server name
     */
    public static function getServerName()
    {
        if (array_key_exists('HTTP_X_FORWARDED_SERVER', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_SERVER']) {
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * getMemoryLimit allow to get the memory limit in octet
     *
     * @return int the memory limit value in octet
     */
    public static function getMemoryLimit()
    {
        $memory_limit = @ini_get('memory_limit');

        return Tools::getOctets($memory_limit);
    }

    /**
     * @return bool true if the server use 64bit arch
     */
    public static function is64bit()
    {
        return (PHP_INT_MAX === '9223372036854775807');
    }

    /**
     * @return bool true if php-cli is used
     */
    public static function isPhpCli()
    {
        return (defined('STDIN') ||
            (strtolower(php_sapi_name()) === 'cli' && (!array_key_exists('REMOTE_ADDR', $_SERVER) || empty($_SERVER['REMOTE_ADDR']))));
    }

    /**
     * Get max file upload size considering server settings and optional max value
     *
     * @param int $max_size optional max file size
     *
     * @return int max file size in bytes
     */
    public static function getMaxUploadSize($max_size = 0)
    {
        $post_max_size = Tools::unformatBytes(ini_get('post_max_size'));
        $upload_max_filesize = Tools::unformatBytes(ini_get('upload_max_filesize'));
        if ($max_size > 0) {
            $result = min($post_max_size, $upload_max_filesize, $max_size);
        } else {
            $result = min($post_max_size, $upload_max_filesize);
        }

        return $result;
    }
}
