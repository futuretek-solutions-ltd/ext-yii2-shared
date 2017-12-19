<?php

namespace futuretek\shared;

use DateTime;
use RuntimeException;

/**
 * Class Tools
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz>, Petr Compel <petr.compel@futuretek.cz>
 * @license Apache-2.0
 * @link    http://www.futuretek.cz
 */
class Tools
{
    /** Format phone number in international format for displaying */
    const PHONE_NUMBER_FORMAT_INTERNATIONAL_NICE = 1;

    /** Format phone number in international format as number */
    const PHONE_NUMBER_FORMAT_INTERNATIONAL = 2;

    /** Format phone number for displaying */
    const PHONE_NUMBER_FORMAT_NICE = 3;

    /** Format phone number as pure number (if the phone number is international, + sign may occur) */
    const PHONE_NUMBER_FORMAT_NUMBER = 4;

    /** Format phone number for use with Smstools3 */
    const PHONE_NUMBER_FORMAT_SMSTOOLS = 5;

    /** Long date format - d. mmmm. yyyy */
    const DATE_LONG = 1;

    /** Short date format - d. m. yyyy */
    const DATE_SHORT = 2;

    /** Long date format - d. mmmm. yyyy h:m:s */
    const DATETIME_LONG = 3;

    /** Short date format - d. m. yyyy h:m:s */
    const DATETIME_SHORT = 4;

    /** Algorithm for the hashing function */
    const HASH_ALGORITHM = 'sha256';

    /** Monday */
    const DOW_MONDAY = 0;

    /** Tuesday */
    const DOW_TUESDAY = 1;

    /** Wednesday */
    const DOW_WEDNESDAY = 2;

    /** Thursday */
    const DOW_THURSDAY = 3;

    /** Friday */
    const DOW_FRIDAY = 4;

    /** Saturday */
    const DOW_SATURDAY = 5;

    /** Sunday */
    const DOW_SUNDAY = 6;

    private static $_countryCodes = array(
        'AND' => 'AD', 'ARE' => 'AE', 'AFG' => 'AF', 'ATG' => 'AG', 'AIA' => 'AI', 'ALB' => 'AL', 'ARM' => 'AM', 'ANT' => 'AN', 'AGO' => 'AO', 'ARG' => 'AR',
        'ASM' => 'AS', 'AUT' => 'AT', 'AUS' => 'AU', 'ABW' => 'AW', 'AZE' => 'AZ', 'BIH' => 'BA', 'BRB' => 'BB', 'BGD' => 'BD', 'BEL' => 'BE', 'BFA' => 'BF',
        'BGR' => 'BG', 'BHR' => 'BH', 'BDI' => 'BI', 'BEN' => 'BJ', 'BMU' => 'BM', 'BRN' => 'BN', 'BOL' => 'BO', 'BRA' => 'BR', 'BHS' => 'BS', 'BTN' => 'BT',
        'BWA' => 'BW', 'BLR' => 'BY', 'BLZ' => 'BZ', 'CAN' => 'CA', 'COD' => 'CD', 'CAF' => 'CF', 'COG' => 'CG', 'CIV' => 'CI', 'COK' => 'CK', 'CHL' => 'CL',
        'CMR' => 'CM', 'CHN' => 'CN', 'COL' => 'CO', 'CRI' => 'CR', 'CUB' => 'CU', 'CPV' => 'CV', 'CYP' => 'CY', 'CZE' => 'CZ', 'DEU' => 'DE', 'DJI' => 'DJ',
        'DNK' => 'DK', 'DMA' => 'DM', 'DOM' => 'DO', 'DZA' => 'DZ', 'ECU' => 'EC', 'EST' => 'EE', 'EGY' => 'EG', 'ESH' => 'EH', 'ERI' => 'ER', 'ESP' => 'ES',
        'ETH' => 'ET', 'FIN' => 'FI', 'FJI' => 'FJ', 'FLK' => 'FK', 'FSM' => 'FM', 'FRO' => 'FO', 'FRA' => 'FR', 'GAB' => 'GA', 'GBR' => 'GB', 'GRD' => 'GD',
        'GEO' => 'GE', 'GUF' => 'GF', 'GHA' => 'GH', 'GIB' => 'GI', 'GRL' => 'GL', 'GMB' => 'GM', 'GIN' => 'GN', 'GLP' => 'GP', 'GNQ' => 'GQ', 'GRC' => 'GR',
        'GTM' => 'GT', 'GUM' => 'GU', 'GNB' => 'GW', 'GUY' => 'GY', 'HKG' => 'HK', 'HND' => 'HN', 'HRV' => 'HR', 'HTI' => 'HT', 'HUN' => 'HU', 'CHE' => 'CH',
        'IDN' => 'ID', 'IRL' => 'IE', 'ISR' => 'IL', 'IND' => 'IN', 'IRQ' => 'IQ', 'IRN' => 'IR', 'ISL' => 'IS', 'ITA' => 'IT', 'JAM' => 'JM', 'JOR' => 'JO',
        'JPN' => 'JP', 'KEN' => 'KE', 'KGZ' => 'KG', 'KHM' => 'KH', 'KIR' => 'KI', 'COM' => 'KM', 'KNA' => 'KN', 'PRK' => 'KP', 'KOR' => 'KR', 'KWT' => 'KW',
        'CYM' => 'KY', 'KAZ' => 'KZ', 'LAO' => 'LA', 'LBN' => 'LB', 'LCA' => 'LC', 'LIE' => 'LI', 'LKA' => 'LK', 'LBR' => 'LR', 'LSO' => 'LS', 'LTU' => 'LT',
        'LUX' => 'LU', 'LVA' => 'LV', 'LBY' => 'LY', 'MAR' => 'MA', 'MCO' => 'MC', 'MDA' => 'MD', 'MDG' => 'MG', 'MHL' => 'MH', 'MKD' => 'MK', 'MLI' => 'ML',
        'MMR' => 'MM', 'MNG' => 'MN', 'MAC' => 'MO', 'MNP' => 'MP', 'MTQ' => 'MQ', 'MRT' => 'MR', 'MSR' => 'MS', 'MLT' => 'MT', 'MUS' => 'MU', 'MDV' => 'MV',
        'MWI' => 'MW', 'MEX' => 'MX', 'MYS' => 'MY', 'MOZ' => 'MZ', 'NAM' => 'NA', 'NCL' => 'NC', 'NER' => 'NE', 'NFK' => 'NF', 'NGA' => 'NG', 'NIC' => 'NI',
        'NLD' => 'NL', 'NOR' => 'NO', 'NPL' => 'NP', 'NRU' => 'NR', 'NIU' => 'NU', 'NZL' => 'NZ', 'OMN' => 'OM', 'PAN' => 'PA', 'PER' => 'PE', 'PYF' => 'PF',
        'PNG' => 'PG', 'PHL' => 'PH', 'PAK' => 'PK', 'POL' => 'PL', 'SPM' => 'PM', 'PCN' => 'PN', 'PRI' => 'PR', 'PRT' => 'PT', 'PLW' => 'PW', 'PRY' => 'PY',
        'QAT' => 'QA', 'REU' => 'RE', 'ROM' => 'RO', 'RUS' => 'RU', 'RWA' => 'RW', 'SAU' => 'SA', 'SLB' => 'SB', 'SYC' => 'SC', 'SDN' => 'SD', 'SWE' => 'SE',
        'SGP' => 'SG', 'SHN' => 'SH', 'SVN' => 'SI', 'SJM' => 'SJ', 'SVK' => 'SK', 'SLE' => 'SL', 'SMR' => 'SM', 'SEN' => 'SN', 'SOM' => 'SO', 'SUR' => 'SR',
        'STP' => 'ST', 'SLV' => 'SV', 'SYR' => 'SY', 'SWZ' => 'SZ', 'TCA' => 'TC', 'TCD' => 'TD', 'TGO' => 'TG', 'THA' => 'TH', 'TJK' => 'TJ', 'TKL' => 'TK',
        'TKM' => 'TM', 'TUN' => 'TN', 'TON' => 'TO', 'TUR' => 'TR', 'TTO' => 'TT', 'TUV' => 'TV', 'TWN' => 'TW', 'TZA' => 'TZ', 'UKR' => 'UA', 'UGA' => 'UG',
        'USA' => 'US', 'URY' => 'UY', 'UZB' => 'UZ', 'VAT' => 'VA', 'VCT' => 'VC', 'VEN' => 'VE', 'VGB' => 'VG', 'VIR' => 'VI', 'VNM' => 'VN', 'VUT' => 'VU',
        'WLF' => 'WF', 'WSM' => 'WS', 'YEM' => 'YE', 'ZAF' => 'ZA', 'ZMB' => 'ZM', 'ZWE' => 'ZW',
    );

    /**
     * Remove all space characters in string
     *
     * @param string $str Input string
     *
     * @return string String with removed spaces
     * @static
     */
    public static function removeSpace($str)
    {
        return strtr($str, [' ' => '']);
    }

    /**
     * Format phone number
     *
     * @param string $phoneNumber Phone number
     * @param int $formatType Phone number format:
     *                            <ul>
     *                            <li>Tools::PHONE_NUMBER_FORMAT_INTERNATIONAL_NICE</li>
     *                            <li>Tools::PHONE_NUMBER_FORMAT_INTERNATIONAL</li>
     *                            <li>Tools::PHONE_NUMBER_FORMAT_NICE</li>
     *                            <li>Tools::PHONE_NUMBER_FORMAT_NUMBER</li>
     *                            <li>Tools::PHONE_NUMBER_FORMAT_SMSTOOLS</li>
     *                            </ul>
     *
     * @return string|bool Formatted phone number or false when the phone number is invalid
     * @static
     */
    public static function formatPhoneNumber($phoneNumber, $formatType = Tools::PHONE_NUMBER_FORMAT_NUMBER)
    {
        $formatType = (int)$formatType;

        if ($formatType !== self::PHONE_NUMBER_FORMAT_INTERNATIONAL && $formatType !== self::PHONE_NUMBER_FORMAT_INTERNATIONAL_NICE &&
            $formatType !== self::PHONE_NUMBER_FORMAT_NUMBER && $formatType !== self::PHONE_NUMBER_FORMAT_NICE &&
            $formatType !== self::PHONE_NUMBER_FORMAT_SMSTOOLS
        ) {
            return false;
        }

        if (!Validate::isPhoneNumber($phoneNumber)) {
            return false;
        }

        $phoneNumber = self::removeSpace($phoneNumber);
        $phoneLen = strlen($phoneNumber);

        if ($phoneLen > 9 && 0 !== strpos($phoneNumber, '+')) {
            $phoneNumber = '+' . $phoneNumber;
            $phoneLen++;
        }

        if ($phoneLen !== 9 && !($phoneLen >= 11 && $phoneLen <= 13 && 0 === strpos($phoneNumber, '+'))) {
            return false;
        }

        $international = ($phoneLen !== 9);

        switch ($formatType) {
            case self::PHONE_NUMBER_FORMAT_INTERNATIONAL_NICE:
                $formattedPhone = preg_replace(
                    '/^(\+\d{1,3})(\d{3})(\d{3})(\d{3})$/',
                    '$1 $2 $3 $4',
                    $international ? $phoneNumber : '+420' . $phoneNumber
                );
                break;
            case self::PHONE_NUMBER_FORMAT_INTERNATIONAL:
                $formattedPhone = $international ? $phoneNumber : '+420' . $phoneNumber;
                break;
            case self::PHONE_NUMBER_FORMAT_NICE:
                $formattedPhone = preg_replace(
                    '/^(\+\d{1,3})(\d{3})(\d{3})(\d{3})$/',
                    '$2 $3 $4',
                    $international ? $phoneNumber : '+420' . $phoneNumber
                );
                break;
            case self::PHONE_NUMBER_FORMAT_NUMBER:
                $formattedPhone = $international ? substr($phoneNumber, -9) : $phoneNumber;
                break;
            case self::PHONE_NUMBER_FORMAT_SMSTOOLS:
                $formattedPhone = $international ? trim($phoneNumber, '+') : '420' . $phoneNumber;
                break;
            default:
                $formattedPhone = false;
        }

        return $formattedPhone;
    }

    /**
     * Format date/datetime
     *
     * @param string|integer $value Timestamp or date accepted by strtotime()
     * @param integer $format Date format
     *
     * @return bool|string Formatted date
     * @static
     */
    public static function formatDate($value, $format)
    {
        if ($value === null) {
            return false;
        }

        //Make sure we have timestamp
        if (!is_int($value)) {
            $value = strtotime($value);
        }
        switch ($format) {
            case self::DATE_LONG:
                $inflection = new Inflection();
                $date = date('j', $value) . '. ' . $inflection->inflect(self::getMonthName(date('n', $value)))[2] . ' ' . date('Y', $value);
                break;
            case self::DATE_SHORT:
                $date = date('j. n. Y', $value);
                break;
            case self::DATETIME_LONG:
                $inflection = new Inflection();
                $date =
                    date('j', $value) . '. ' . $inflection->inflect(self::getMonthName(date('n', $value)))[2] . ' ' .
                    date('Y G:i:s', $value);
                break;
            case self::DATETIME_SHORT:
                $date = date('j. n. Y G:i:s', $value);
                break;
            default:
                return false;
        }

        return $date;
    }

    /**
     * Get month name in current language
     *
     * @param int $month Month number
     *
     * @return string Localised month name
     * @static
     */
    public static function getMonthName($month)
    {
        if ($month < 1 || $month > 12) {
            return '';
        }

        $monthNames = [
            1 => Tools::poorManTranslate('fts-shared', 'January'),
            2 => Tools::poorManTranslate('fts-shared', 'February'),
            3 => Tools::poorManTranslate('fts-shared', 'March'),
            4 => Tools::poorManTranslate('fts-shared', 'April'),
            5 => Tools::poorManTranslate('fts-shared', 'May'),
            6 => Tools::poorManTranslate('fts-shared', 'June'),
            7 => Tools::poorManTranslate('fts-shared', 'July'),
            8 => Tools::poorManTranslate('fts-shared', 'August'),
            9 => Tools::poorManTranslate('fts-shared', 'September'),
            10 => Tools::poorManTranslate('fts-shared', 'October'),
            11 => Tools::poorManTranslate('fts-shared', 'November'),
            12 => Tools::poorManTranslate('fts-shared', 'December'),
        ];

        return $monthNames[$month];
    }

    /**
     * Get day name in current language
     *
     * @param int $day Day of week number according to Tools::dow()
     *
     * @return string Localised day name
     * @static
     */
    public static function getDayName($day)
    {
        if ($day < self::DOW_MONDAY || $day > self::DOW_SUNDAY) {
            return '';
        }

        $dayNames = [
            self::DOW_MONDAY => Tools::poorManTranslate('fts-shared', 'Monday'),
            self::DOW_TUESDAY => Tools::poorManTranslate('fts-shared', 'Tuesday'),
            self::DOW_WEDNESDAY => Tools::poorManTranslate('fts-shared', 'Wednesday'),
            self::DOW_THURSDAY => Tools::poorManTranslate('fts-shared', 'Thursday'),
            self::DOW_FRIDAY => Tools::poorManTranslate('fts-shared', 'Friday'),
            self::DOW_SATURDAY => Tools::poorManTranslate('fts-shared', 'Saturday'),
            self::DOW_SUNDAY => Tools::poorManTranslate('fts-shared', 'Sunday'),
        ];

        return $dayNames[$day];
    }

    /**
     * Get textual representation of time difference in style 'some time ago'
     *
     * @param int|string $value Time in any format accepted by strtotime()
     *
     * @return string Textual representation of time difference
     * @static
     */
    public static function getRelativeTime($value)
    {
        //Make sure we have timestamp
        if (!is_int($value)) {
            $value = strtotime($value);
        }

        $diff = abs($value - time());
        if ($value < time()) {
            if ($diff < 60) {
                //Less than a minute
                return Tools::poorManTranslate('fts-shared', 'a few seconds ago');
            } elseif ($diff < 3600) {
                //Less than a hour
                return Tools::poorManTranslate('fts-shared', 'a {n, plural, =1{minute} other{# minutes}} ago', ['n' => floor($diff / 60)]);
            } elseif ($diff < 86400) {
                //Less than a day
                return Tools::poorManTranslate('fts-shared', 'a {n, plural, =1{hour} other{# hours}} ago', ['n' => floor($diff / 3600)]);
            } elseif ($diff < 172800) {
                //Less than two days
                return Tools::poorManTranslate('fts-shared', 'yesterday');
            } elseif ($diff < 604800) {
                //Less than a week
                return Tools::poorManTranslate('fts-shared', 'a {n} days ago', ['n' => floor($diff / 86400)]);
            } elseif ($diff < 2628000) {
                //Less than a month
                return Tools::poorManTranslate('fts-shared', 'a {n, plural, =1{week} other{# weeks}} ago', ['n' => floor($diff / 604800)]);
            } elseif ($diff < 31556926) {
                //Less than a year
                return Tools::poorManTranslate('fts-shared', 'a {n, plural, =1{month} other{# months}} ago', ['n' => floor($diff / 2628000)]);
            } else {
                //Less than eternity :-)
                return Tools::poorManTranslate('fts-shared', 'a {n, plural, =1{year} other{# years}} ago', ['n' => floor($diff / 31556926)]);
            }
        } else {
            if ($diff < 60) {
                //Less than a minute
                return Tools::poorManTranslate('fts-shared', 'after a few seconds');
            } elseif ($diff < 3600) {
                //Less than a hour
                return Tools::poorManTranslate('fts-shared', 'after a {n, plural, =1{minute} other{# minutes}}', ['n' => floor($diff / 60)]);
            } elseif ($diff < 86400) {
                //Less than a day
                return Tools::poorManTranslate('fts-shared', 'after a {n, plural, =1{hour} other{# hours}}', ['n' => floor($diff / 3600)]);
            } elseif ($diff < 172800) {
                //Less than two days
                return Tools::poorManTranslate('fts-shared', 'tomorrow');
            } elseif ($diff < 604800) {
                //Less than a week
                return Tools::poorManTranslate('fts-shared', 'after a {n, plural, =1{day} other{# days}}', ['n' => floor($diff / 86400)]);
            } elseif ($diff < 2628000) {
                //Less than a month
                return Tools::poorManTranslate('fts-shared', 'after a {n, plural, =1{week} other{# weeks}}', ['n' => floor($diff / 604800)]);
            } elseif ($diff < 31556926) {
                //Less than a year
                return Tools::poorManTranslate('fts-shared', 'after a {n, plural, =1{month} other{# months}}', ['n' => floor($diff / 2628000)]);
            } else {
                //Less than eternity :-)
                return Tools::poorManTranslate('fts-shared', 'after a {n, plural, =1{year} other{# years}}', ['n' => floor($diff / 31556926)]);
            }
        }
    }

    /**
     * Truncate string to a maximal specific length with smart truncate on given character
     *
     * @param string $string Input string
     * @param integer $limit Maximal output string length
     * @param string $break Character on which the output string should be truncated.
     * @param string $pad String that will be appended at the end of truncated string
     *
     * @return string Truncated string
     * @static
     */
    public static function truncate($string, $limit, $break = ' ', $pad = '...')
    {
        if (strlen($string) <= $limit) {
            return $string;
        }

        if (false !== ($breakpoint = strpos($string, $break, $limit)) && $breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        } else {
            $string = substr($string, 0, $limit) . $pad;
        }

        return $string;
    }

    /**
     * Generate v4 UUID
     *
     * Version 4 UUIDs are pseudo-random.
     *
     * @return string GUIDv4
     * @static
     */
    public static function GUIDv4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Generate v5 UUID
     *
     * Version 5 UUIDs are named based. They require a namespace (another
     * valid UUID) and a value (the name). Given the same namespace and
     * name, the output is always the same.
     *
     * @param string $namespace Valid namespace (GUID)
     * @param string $name Valid name (value)
     *
     * @return string|bool GUIDv5 or false when the namespace is not a valid GUID
     * @static
     */
    public static function GUIDv5($namespace, $name)
    {
        if (!Validate::isGuid($namespace)) {
            return false;
        }

        $nHex = str_replace(['-', '{', '}'], '', $namespace);
        $nStr = '';

        $nHexLen = strlen($nHex);
        for ($i = 0; $i < $nHexLen; $i += 2) {
            $nStr .= chr(intval($nHex[$i] . $nHex[$i + 1], 16));
        }

        $hash = sha1($nStr . $name);

        return sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (intval(substr($hash, 12, 4), 16) & 0x0fff) | 0x5000,
            (intval(substr($hash, 16, 4), 16) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -> firstName)
     *
     * @param      $str
     * @param bool $catapitalise_first_char
     *
     * @return mixed
     */
    public static function toCamelCase($str, $catapitalise_first_char = false)
    {
        $str = strtolower($str);
        if ($catapitalise_first_char) {
            $str = ucfirst($str);
        }

        return preg_replace_callback('/_+([a-z])/', create_function('$c', 'return strtoupper($c[1]);'), $str);
    }

    /**
     * Transform a CamelCase string to underscore_case string
     *
     * @param string $string
     *
     * @return string
     */
    public static function toUnderscoreCase($string)
    {
        return strtolower(trim(preg_replace('/([A-Z][a-z])/', '_$1', $string), '_'));
    }

    /**
     * Transform a CamelCase string to comma-case string
     *
     * @param string $string
     *
     * @return string
     */
    public static function toCommaCase($string)
    {
        return strtolower(trim(preg_replace('/([A-Z][a-z])/', '-$1', $string), '-'));
    }

    /**
     * Random password generator
     *
     * @param integer $length Desired length (optional)
     * @param string $flag Output type (NUMERIC, ALPHANUMERIC, ALPHA, ALPHA_LOWER)
     *
     * @return string Password
     */
    public static function passwdGen($length = 8, $flag = 'ALPHANUMERIC')
    {
        switch ($flag) {
            case 'NUMERIC':
                $str = '0123456789';
                break;
            case 'ALPHA':
                $str = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'ALPHA_LOWER':
                $str = 'abcdefghijkmnopqrstuvwxyz';
                break;
            default:
                $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }

        for ($i = 0, $passwd = ''; $i < $length; $i++) {
            $passwd .= substr($str, mt_rand(0, strlen($str) - 1), 1);
        }

        return $passwd;
    }

    /**
     * Get the server variable REMOTE_ADDR, or the first ip of HTTP_X_FORWARDED_FOR (when using proxy)
     *
     * @return string $remote_addr ip of client
     */
    public static function getRemoteAddr()
    {
        // This condition is necessary when using CDN, don't remove it.
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR'] &&
            (!array_key_exists('REMOTE_ADDR', $_SERVER) || preg_match('/^127\./', trim($_SERVER['REMOTE_ADDR'])) ||
                preg_match('/^172\.16\./', trim($_SERVER['REMOTE_ADDR'])) ||
                preg_match('/^192\.168\./', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\./', trim($_SERVER['REMOTE_ADDR'])))
        ) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Delete directory and subdirectories
     *
     * @param string $dirName Directory name
     * @param bool $deleteSelf Delete also specified directory
     *
     * @return bool
     */
    public static function deleteDirectory($dirName, $deleteSelf = true)
    {
        $dirName = rtrim($dirName, '/') . '/';
        if (file_exists($dirName) && $files = scandir($dirName)) {
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && $file !== '.svn') {
                    if (is_dir($dirName . $file)) {
                        Tools::deleteDirectory($dirName . $file, true);
                    } elseif (file_exists($dirName . $file)) {
                        @chmod($dirName . $file, 0777); // NT ?
                        unlink($dirName . $file);
                    }
                }
            }
            if ($deleteSelf && !rmdir($dirName)) {
                @chmod($dirName, 0777); // NT ?

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Delete file
     *
     * @param string $file File path
     * @param array $excludeFiles Excluded files
     */
    public static function deleteFile($file, $excludeFiles = [])
    {
        if (!is_array($excludeFiles)) {
            $excludeFiles = [$excludeFiles];
        }

        if (file_exists($file) && is_file($file) && in_array(basename($file), $excludeFiles, true) === false) {
            @chmod($file, 0777); // NT ?
            unlink($file);
        }
    }

    /**
     * Replace all accented chars by their equivalent non accented chars.
     *
     * @param string $str
     *
     * @return string
     */
    public static function replaceAccentedChars($str)
    {
        /* One source among others:
            http://www.tachyonsoft.com/uc0000.htm
            http://www.tachyonsoft.com/uc0001.htm
            http://www.tachyonsoft.com/uc0004.htm
        */
        $patterns = [

            /* Lowercase */
            /* a  */
            '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}]/u',
            /* b  */
            '/[\x{0431}]/u',
            /* c  */
            '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
            /* d  */
            '/[\x{010F}\x{0111}\x{0434}]/u',
            /* e  */
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}]/u',
            /* f  */
            '/[\x{0444}]/u',
            /* g  */
            '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
            /* h  */
            '/[\x{0125}\x{0127}]/u',
            /* i  */
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}]/u',
            /* j  */
            '/[\x{0135}\x{0439}]/u',
            /* k  */
            '/[\x{0137}\x{0138}\x{043A}]/u',
            /* l  */
            '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
            /* m  */
            '/[\x{043C}]/u',
            /* n  */
            '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
            /* o  */
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}]/u',
            /* p  */
            '/[\x{043F}]/u',
            /* r  */
            '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
            /* s  */
            '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
            /* ss */
            '/[\x{00DF}]/u',
            /* t  */
            '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
            /* u  */
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}]/u',
            /* v  */
            '/[\x{0432}]/u',
            /* w  */
            '/[\x{0175}]/u',
            /* y  */
            '/[\x{00FF}\x{0177}\x{00FD}\x{044B}]/u',
            /* z  */
            '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
            /* ae */
            '/[\x{00E6}]/u',
            /* ch */
            '/[\x{0447}]/u',
            /* kh */
            '/[\x{0445}]/u',
            /* oe */
            '/[\x{0153}]/u',
            /* sh */
            '/[\x{0448}]/u',
            /* shh*/
            '/[\x{0449}]/u',
            /* ya */
            '/[\x{044F}]/u',
            /* ye */
            '/[\x{0454}]/u',
            /* yi */
            '/[\x{0457}]/u',
            /* yo */
            '/[\x{0451}]/u',
            /* yu */
            '/[\x{044E}]/u',
            /* zh */
            '/[\x{0436}]/u',
            /* Uppercase */
            /* A  */
            '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
            /* B  */
            '/[\x{0411}]]/u',
            /* C  */
            '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
            /* D  */
            '/[\x{010E}\x{0110}\x{0414}]/u',
            /* E  */
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
            /* F  */
            '/[\x{0424}]/u',
            /* G  */
            '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
            /* H  */
            '/[\x{0124}\x{0126}]/u',
            /* I  */
            '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
            /* J  */
            '/[\x{0134}\x{0419}]/u',
            /* K  */
            '/[\x{0136}\x{041A}]/u',
            /* L  */
            '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
            /* M  */
            '/[\x{041C}]/u',
            /* N  */
            '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
            /* O  */
            '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
            /* P  */
            '/[\x{041F}]/u',
            /* R  */
            '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
            /* S  */
            '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
            /* T  */
            '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
            /* U  */
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
            /* V  */
            '/[\x{0412}]/u',
            /* W  */
            '/[\x{0174}]/u',
            /* Y  */
            '/[\x{0176}\x{042B}]/u',
            /* Z  */
            '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
            /* AE */
            '/[\x{00C6}]/u',
            /* CH */
            '/[\x{0427}]/u',
            /* KH */
            '/[\x{0425}]/u',
            /* OE */
            '/[\x{0152}]/u',
            /* SH */
            '/[\x{0428}]/u',
            /* SHH*/
            '/[\x{0429}]/u',
            /* YA */
            '/[\x{042F}]/u',
            /* YE */
            '/[\x{0404}]/u',
            /* YI */
            '/[\x{0407}]/u',
            /* YO */
            '/[\x{0401}]/u',
            /* YU */
            '/[\x{042E}]/u',
            /* ZH */
            '/[\x{0416}]/u',
        ];

        // ö to oe
        // å to aa
        // ä to ae

        $replacements = [
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            'ss',
            't',
            'u',
            'v',
            'w',
            'y',
            'z',
            'ae',
            'ch',
            'kh',
            'oe',
            'sh',
            'shh',
            'ya',
            'ye',
            'yi',
            'yo',
            'yu',
            'zh',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'Y',
            'Z',
            'AE',
            'CH',
            'KH',
            'OE',
            'SH',
            'SHH',
            'YA',
            'YE',
            'YI',
            'YO',
            'YU',
            'ZH',
        ];

        return preg_replace($patterns, $replacements, $str);
    }

    /**
     * Format a number into a human readable format
     * e.g. 24962496 => 23.81M
     *
     * @param     $size
     * @param int $precision
     *
     * @return string
     */
    public static function formatBytes($size, $precision = 2)
    {
        if (!$size) {
            return '0';
        }
        $base = log($size) / log(1024);
        $suffixes = ['', 'k', 'M', 'G', 'T'];

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[(int)floor($base)];
    }

    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value
     *
     * @param string $value value to convert
     *
     * @return int
     */
    public static function unformatBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = (int)substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }

            return $qty;
        }
    }

    /**
     * getOctet allow to gets the value of a configuration option in octet
     *
     * @param string $option
     *
     * @return int the value of a configuration option in octet
     */
    public static function getOctets($option)
    {
        if (preg_match('/\d+k/i', $option)) {
            return 1024 * (int)$option;
        }

        if (preg_match('/\d+m/i', $option)) {
            return 1024 * 1024 * (int)$option;
        }

        if (preg_match('/\d+g/i', $option)) {
            return 1024 * 1024 * 1024 * (int)$option;
        }

        return $option;
    }

    /**
     * Get user OS
     *
     * @return string User OS
     * @static
     */
    public static function getUserPlatform()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $user_platform = 'unknown';

        if (false !== stripos($user_agent, 'linux')) {
            $user_platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $user_platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $user_platform = 'Windows';
        }

        return $user_platform;
    }

    /**
     * Get user browser
     *
     * @return string User browser
     * @static
     */
    public static function getUserBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $user_browser = 'unknown';

        if (false !== stripos($user_agent, 'MSIE') && false === stripos($user_agent, 'Opera')) {
            $user_browser = 'Internet Explorer';
        } elseif (false !== stripos($user_agent, 'Firefox')) {
            $user_browser = 'Mozilla Firefox';
        } elseif (false !== stripos($user_agent, 'Chrome')) {
            $user_browser = 'Google Chrome';
        } elseif (false !== stripos($user_agent, 'Safari')) {
            $user_browser = 'Apple Safari';
        } elseif (false !== stripos($user_agent, 'Opera')) {
            $user_browser = 'Opera';
        } elseif (false !== stripos($user_agent, 'Netscape')) {
            $user_browser = 'Netscape';
        }

        return $user_browser;
    }

    /**
     * Convert wildcard IP address range notation (172.16.*.*) to CIDR netmask notation (172.16.0.0/16)
     *
     * @param string $wildcardIp Wildcard IP address range notation
     *
     * @return string CIDR netmask notation
     * @static
     */
    public static function ipWildcardToCIDR($wildcardIp)
    {
        $wildcardIp = str_replace('*', '0', $wildcardIp, $count);

        return $wildcardIp . '/' . (32 - ($count * 8));
    }

    /**
     * Test if IP address is in specified range. Range can be specified using IP1-IP2, wildcard 172.16.*.* or CIDR netmask 172.16.0.0/16
     *
     * @param string $ip Tested IP address
     * @param string $range Range
     *
     * @return bool Is IP in range
     * @static
     */
    public static function isIpInRange($ip, $range)
    {
        if (($pos = strpos($range, '-')) !== false) {
            $from = ip2long(substr($range, 0, $pos));
            $to = ip2long(substr($range, $pos + 1));
            $ipInt = ip2long($ip);

            return ($from < $to and $ipInt >= $from and $ipInt <= $to);
        }

        if (strpos($range, '*') !== false) {
            $range = Tools::ipWildcardToCIDR($range);
        }

        @list($net, $bits) = explode('/', $range);
        $bits = null !== $bits ? $bits : 32;
        $bitMask = -pow(2, 32 - $bits) & 0x00000000FFFFFFFF;
        $netMask = ip2long($net) & $bitMask;
        $ip_bits = ip2long($ip) & $bitMask;

        return (($netMask ^ $ip_bits) === 0);
    }

    /**
     * Hash string
     *
     * @param string $data Data to hash
     *
     * @return string Hashed data
     * @static
     */
    public static function hash($data)
    {
        return hash(self::HASH_ALGORITHM, $data);
    }

    /**
     * Get files count in specified directory
     *
     * @param string $directory Directory name
     *
     * @return int Directory files count
     * @static
     */
    public static function getDirectoryFilesCount($directory)
    {
        return count(scandir($directory)) - 2;
    }

    /**
     * Whether the specified date is Czech holiday
     *
     * @param string|DateTime|int $date Any valid date or datetime
     *
     * @return bool Is holiday
     * @throws RuntimeException
     * @static
     */
    public static function isCzechHoliday($date)
    {
        if (!$date instanceof DateTime) {
            if (is_int($date)) {
                $date = new DateTime('@' . $date);
            } elseif (is_string($date)) {
                $date = new DateTime($date);
            } else {
                throw new RuntimeException(self::poorManTranslate('fts-shared', 'Invalid date format'));
            }
        }

        $holidays = ['01-01', '05-01', '05-08', '07-05', '07-06', '09-28', '10-28', '11-17', '12-24', '12-25', '12-26'];

        if (in_array($date->format('m-d'), $holidays, true)) {
            return true;
        }

        //Easter
        $easterDays = easter_days($date->format('Y')); //Return number of days from base to easter sunday
        $easter = new DateTime($date->format('Y') . '-03-21');
        $easter->add(new \DateInterval('P' . $easterDays . 'D')); //Sunday
        $easter->sub(new \DateInterval('P2D')); //Friday
        if ($date->format('Y-m-d') === $easter->format('Y-m-d')) {
            return true;
        }
        $easter->add(new \DateInterval('P3D')); //Monday

        return ($easter->format('Y-m-d') === $date->format('Y-m-d'));
    }

    /**
     * Return greeting based on time of the day
     *
     * @param string|int|null $time Time in string format, timestamp or null for current time
     *
     * @return string Greeting
     * @static
     */
    public static function getGreeting($time = null)
    {
        if ($time === null) {
            $time = time();
        } elseif (is_string($time)) {
            $time = strtotime($time);
        }

        switch (date('G', $time)) {
            case 0:
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
                return Tools::poorManTranslate('fts-shared', 'Good morning');
            case 8:
            case 9:
            case 10:
            case 11:
                //Underscore to better translate something between morning and noon
                return trim(Tools::poorManTranslate('fts-shared', '_Good morning'), '_');
            case 12:
                return Tools::poorManTranslate('fts-shared', 'Good noon');
            case 13:
            case 14:
            case 15:
            case 16:
                return Tools::poorManTranslate('fts-shared', 'Good afternoon');
            case 17:
            case 18:
            case 19:
                //Underscore to better translate something between noon and evening
                return trim(Tools::poorManTranslate('fts-shared', '_Good afternoon'), '_');
            case 20:
            case 21:
            case 22:
            case 23:
                return Tools::poorManTranslate('fts-shared', 'Good evening');
            default:
                return '';
        }
    }

    /**
     * Calculates distance in kilometers between two GPS coordinates
     *
     * @param float $lat1 GPS latitude 1
     * @param float $lon1 GPS longitude 1
     * @param float $lat2 GPS latitude 2
     * @param float $lon2 GPS longitude 2
     *
     * @return float Distance between points in kilometers
     * @static
     */
    public static function gpsDistance($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $lonDelta = $lon2 - $lon1;
        $a = pow(cos($lat2) * sin($lonDelta), 2) + pow(cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($lonDelta), 2);
        $b = sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lonDelta);

        return atan2(sqrt($a), $b) * 6371.0088;
    }

    /**
     * Get day of week
     * Function is compatible with SQL function WEEKDAY()
     *
     * @param DateTime|int|string|null $date Datetime object, timestamp, date in valid format or null for current date
     * @return int Day index (0 = Monday, 6 = Sunday)
     * @throws RuntimeException
     */
    public static function dow($date = null)
    {
        if ($date === null) {
            $date = new DateTime();
        } elseif (!$date instanceof DateTime) {
            if (is_int($date)) {
                $date = new DateTime('@' . $date);
            } elseif (is_string($date)) {
                $date = new DateTime($date);
            } else {
                throw new RuntimeException(self::poorManTranslate('fts-shared', 'Invalid date format'));
            }
        }

        return (int)$date->format('N') - 1;
    }

    /**
     * Translate function with automatic Yii detection
     *
     * @param $category
     * @param $text
     * @param array $params
     * @return string
     */
    public static function poorManTranslate($category, $text, array $params = [])
    {
        if (class_exists('Yii')) {
            return \Yii::t($category, $text, $params);
        } else {
            $pos = strrpos($category, '/');
            $category = $pos === false ? $category : substr($category, $pos + 1);
            $translation = @include_once 'messages/cs/' . $category . '.php';
            if ($translation !== null && is_array($translation) && array_key_exists($text, $translation)) {
                $keys = array_keys($params);
                array_walk($keys, function (&$v) {
                    $v = '{' . $v . '}';
                });

                return str_replace(array_values($params), $keys, $translation[$text]);
            } else {
                return $text;
            }
        }
    }

    /**
     * Return a friendly url made from the provided string
     *
     * @param string $str Input string
     * @param bool $allowUnicodeChars If unicode characters are allowed in URL
     * @return string Link rewrite
     */
    public static function linkRewrite($str, $allowUnicodeChars = false)
    {
        if (!is_string($str)) {
            return false;
        }

        $str = trim($str);

        if (function_exists('mb_strtolower')) {
            $str = mb_strtolower($str, 'utf-8');
        }
        if (!$allowUnicodeChars) {
            $str = Tools::replaceAccentedChars($str);
        }

        // Remove all non-whitelist chars.
        if ($allowUnicodeChars) {
            $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-\pL]/u', '', $str);
        } else {
            $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-]/', '', $str);
        }

        $str = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $str);
        $str = str_replace(array(' ', '/'), '-', $str);

        // If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
        // This way we lose fewer special chars.
        if (!function_exists('mb_strtolower')) {
            $str = strtolower($str);
        }

        return $str;
    }

    /**
     * Get birth date from the birth number
     *
     * @param string $no Birth number
     * @return DateTime Birth date or null
     */
    public static function getDateFromBirthNumber($no)
    {
        if (!preg_match('#^\s*(\d\d)(\d\d)(\d\d)[ /]*(\d\d\d)(\d?)\s*$#', $no, $matches)) {
            return null;
        }

        list(, $year, $month, $day, $ext, $c) = $matches;

        if ($c === '') {
            $year += $year < 54 ? 1900 : 1800;
        } else {
            $mod = ($year . $month . $day . $ext) % 11;
            if ($mod === 10) {
                $mod = 0;
            }
            if ($mod !== (int)$c) {
                return null;
            }
            $year += $year < 54 ? 2000 : 1900;
        }
        if ($year > 2003) {
            if ($month > 70) {
                $month -= 70;
            }
            if ($month > 20 && $month < 50) {
                $month -= 20;
            }
        }
        if ($month > 50) {
            $month -= 50;
        }

        return new DateTime(sprintf('%04d-%02d-%02d', $year, $month, $day));
    }

    /**
     * Generate random PIN based on
     *
     * @param string $salt Salt
     * @param int $length PIN length
     * @param bool $useMinutes Generate different PIN each minute (default is each hour)
     * @return int Pin
     */
    public static function generatePin($salt, $length = 6, $useMinutes = false)
    {
        $seed = sha1($salt . (new \DateTime('now', new \DateTimeZone('Europe/Prague')))->format('Ymd' . ($useMinutes ? 'i' : '')), true);
        for ($i = 0; $i <= (new \DateTime('now', new \DateTimeZone('Europe/Prague')))->format('G'); $i++) {
            $seed = sha1($seed . $i);
        }

        $data = unpack('V1/V2', $seed);
        $data[1] = $data[1] < 0 ? $data[1] * -1 : $data[1];
        $data[2] = $data[2] < 0 ? $data[2] * -1 : $data[2];
        $mask = $data[1] ^ $data[2];

        if ($mask % 1000000 === 0 || $mask % 1000000 === 999999) {
            return self::generatePin($salt . $seed, $length, $useMinutes);
        } else {
            return round(
                (((float)($mask % 1000000) - 0.5 + ((float)($mask % 200) / 199)) / 999999) *
                ((pow(10, $length) - 1) - pow(10, $length - 1)) + pow(10, $length - 1));
        }
    }

    /**
     * Send message to HipChat room
     *
     * @param string $room Room name or ID
     * @param string $token Token
     * @param string $text Message body
     * @param bool $notify Determine if should notify users
     * @param string $format Format of message
     */
    public static function sendHipChatMessage($room, $token, $text, $notify = true, $format = 'text')
    {
        $session = curl_init();
        curl_setopt($session, CURLOPT_URL, 'https://api.hipchat.com/v2/room/' . $room . '/notification?auth_token=' . $token);
        curl_setopt($session, CURLOPT_POST, 1);
        curl_setopt(
            $session,
            CURLOPT_POSTFIELDS,
            http_build_query(
                [
                    'message' => $text,
                    'message_format' => $format,
                    'notify' => $notify,
                ]
            )
        );

        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_exec($session);
        curl_close($session);
    }

    /**
     * Converts pretty time format (2w 1d 5h 30m) to seconds
     *
     * @param string $time Pretty time format
     * @param int $dayLength Length of the work day in seconds
     * @param int $weekLength Length of the work week is seconds
     * @return int Time in seconds
     */
    public static function prettyTimeToSeconds($time, $dayLength = 28800, $weekLength = 144000)
    {
        $minus = 0 === strpos($time, '-');
        if ($minus) {
            $time = substr($time, 1);
        }
        if (preg_match('/^(?:(?<weeks>\d+)w\s*)?(?:(?<days>\d+)d\s*)?(?:(?<hours>\d+)h\s*)?(?:(?<minutes>\d+)m\s*)?(?:(?<seconds>\d+)s\s*)?$/', $time, $matches)) {
            return
                ($minus ? -1 : 1) *
                (!empty($matches['weeks']) ? (int)$matches['weeks'] * $weekLength : 0) +
                (!empty($matches['days']) ? (int)$matches['days'] * $dayLength : 0) +
                (!empty($matches['hours']) ? (int)$matches['hours'] * 3600 : 0) +
                (!empty($matches['minutes']) ? (int)$matches['minutes'] * 60 : 0) +
                (!empty($matches['seconds']) ? (int)$matches['seconds'] : 0);
        }

        return 0;
    }

    /**
     * Converts seconds to pretty time format (2w 1d 5h 30m)
     *
     * @param int $seconds Time in seconds
     * @param int $dayLength Length of the work day in seconds
     * @param int $weekLength Length of the work week is seconds
     * @return string Pretty time format
     */
    public static function secondsToPrettyTime($seconds, $dayLength = 28800, $weekLength = 144000)
    {
        $minus = $seconds < 0;
        $seconds = (int)abs($seconds);
        if ($seconds === 0) {
            return '0s';
        }

        $out = [];
        $units = ['w' => $weekLength, 'd' => $dayLength, 'h' => 3600, 'm' => 60, 's' => 1];
        foreach ($units as $sign => $value) {
            if ($seconds < $value) {
                continue;
            }

            $i = floor($seconds / $value);
            $out[] = $i . $sign;
            $seconds -= ($i * $value);
        }

        return ($minus ? '-' : '') . implode(' ', $out);
    }

    /**
     * Get difference between two dates in seconds
     *
     * @param string $start Start date in DateTime compatible syntax
     * @param string $end End date in DateTime compatible syntax
     * @param bool $absolute Return absolute (positive) number
     * @param string $timezone Timezone @see http://php.net/manual/en/timezones.php
     * @return int Difference in seconds
     */
    public static function secondsBetweenDates($start, $end, $absolute = true, $timezone = 'Europe/Prague')
    {
        $timezoneObj = new \DateTimeZone($timezone);
        $date = new DateTime($end, $timezoneObj);
        $diff = $date->diff(new DateTime($start, $timezoneObj), $absolute);

        return ($diff->invert ? -1 : 1) * (($diff->days * 86400) + ($diff->h * 3600) + ($diff->i * 60) + $diff->s);
    }

    /**
     * Counts number of seconds between two dates taking into account only working days
     *
     * @param string $dateFrom Start date in DateTime compatible syntax
     * @param string $dateTo End date in DateTime compatible syntax
     * @param string $workDayFrom Working hours start time in DateTime compatible syntax
     * @param string $workDayTo Working hours end time in DateTime compatible syntax
     * @param bool $weekends Whether to count weekends as a working days
     * @param bool $holidays Whether to count holidays as a working days
     * @param string $timeZone Timezone @see http://php.net/manual/en/timezones.php
     * @return int Difference in seconds
     *
     * @throws RuntimeException
     */
    public static function secondsBetweenWorkingDays($dateFrom, $dateTo, $workDayFrom, $workDayTo, $weekends = false, $holidays = false, $timeZone = 'Europe/Prague')
    {
        $timeZoneObj = new \DateTimeZone($timeZone);
        $dateFromObj = new DateTime($dateFrom, $timeZoneObj);
        $dateToObj = new DateTime($dateTo, $timeZoneObj);
        $workDayFromObj = new DateTime($workDayFrom, $timeZoneObj);
        $workDayToObj = new DateTime($workDayTo, $timeZoneObj);
        $workDayLength = self::secondsBetweenDates($workDayFrom, $workDayTo, true, $timeZone);

        $period = new \DatePeriod(
            new DateTime($dateFromObj->format('Y-m-d 00:00:00'), $timeZoneObj),
            new \DateInterval('P1D'),
            new DateTime($dateToObj->format('Y-m-d 23:59:59'), $timeZoneObj)
        );

        $workedTime = 0;
        foreach ($period as $date) {
            /** @var DateTime $date */
            if ((!$weekends && (int)$date->format('N') > 5) || (!$holidays && self::isCzechHoliday($date))) {
                continue;
            }

            if ($date->format('Y-m-d') === $dateFromObj->format('Y-m-d')) {
                //First day
                $endOfDay = new DateTime($date->format('Y-m-d ' . $workDayToObj->format('H:i:s')), $timeZoneObj);

                if ($dateFromObj < $endOfDay && $dateFromObj->format('Y-m-d') === $dateToObj->format('Y-m-d')) {
                    //Only one day - before workday end
                    $diff = $dateToObj->diff($dateFromObj)->format('%H:%I:%S');
                } else {
                    $diff = $endOfDay->diff($dateFromObj)->format('%H:%I:%S');
                }
                $diff = explode(':', $diff);
                $diff = $diff[0] * 3600 + $diff[1] * 60 + $diff[0];
                $workedTime += $diff;
            } elseif ($date->format('Y-m-d') === $dateToObj->format('Y-m-d')) {
                //Last day
                $startOfDay = new DateTime($date->format('Y-m-d ' . $workDayFromObj->format('H:i:s')), $timeZoneObj);
                if ($dateToObj > $startOfDay) {
                    $diff = $startOfDay->diff($dateToObj)->format('%H:%I:%S');
                    $diff = explode(':', $diff);
                    $diff = $diff[0] * 3600 + $diff[1] * 60 + $diff[0];
                    $workedTime += $diff;
                }
            } else {
                //Full day
                $workedTime += $workDayLength;
            }
        }

        return $workedTime;
    }

    /**
     * Transpose an array
     *
     * @param array $array Array to transpose
     * @return array
     */
    public static function transpose($array)
    {
        array_unshift($array, null);
        return call_user_func_array('array_map', $array);
    }

    /**
     * Get max count of multiple arrays
     *
     * @param array ...$array Array of arrays to count
     * @return int Max item count
     */
    public static function maxCount()
    {
        $array = func_get_args();
        if (!is_array($array)) {
            return 0;
        }

        $maxCnt = 0;
        foreach ($array as $item) {
            if (!is_array($item)) {
                continue;
            }
            $cnt = count($item);
            $maxCnt = $cnt > $maxCnt ? $cnt : $maxCnt;
        }

        return $maxCnt;
    }

    /**
     * Extend array to desired size by filling it with fill data
     *
     * @param array $array Array to fill
     * @param int $size Desired size
     * @param mixed $fill Fill data
     */
    public static function fillToSize(&$array, $size, $fill)
    {
        $cnt = count($array);
        if ($cnt >= $size) {
            return;
        }
        $array = array_merge($array, array_fill($cnt + 1, $size - $cnt, $fill));
    }

    /**
     * Converts two digit country code to three digit one or return false if code does not exists
     *
     * @param string $code Two digit country code
     * @return string|false
     */
    public static function countryCodeTwoToThree($code)
    {
        $codes = array_flip(self::$_countryCodes);
        if (!array_key_exists($code, $codes)) {
            return false;
        }

        return $codes[$code];
    }

    /**
     * Converts three digit country code to two digit one or return false if code does not exists
     *
     * @param string $code Three digit country code
     * @return string|false
     */
    public static function countryCodeThreeToTwo($code)
    {
        if (!array_key_exists($code, self::$_countryCodes)) {
            return false;
        }

        return self::$_countryCodes[$code];
    }
}
