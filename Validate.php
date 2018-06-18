<?php

namespace futuretek\shared;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

/**
 * Class Validate
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz>
 * @license Apache-2.0
 * @link    http://www.futuretek.cz
 */
class Validate
{
    const PASSWORD_COMPLEXITY_WEAK = 2;
    const PASSWORD_COMPLEXITY_MEDIUM = 3;
    const PASSWORD_COMPLEXITY_STRONG = 4;
    const PASSWORD_COMPLEXITY_VERY_STRONG = 6;

    /**
     * Administrator password length
     */
    const ADMIN_PASSWORD_LENGTH = 8;
    /**
     * User password length
     */
    const PASSWORD_LENGTH = 5;

    /**
     * Check for e-mail validity
     *
     * @param string $email e-mail address to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isEmail($email)
    {
        return preg_match(
            '/^[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+(?:[.]?[_a-z\p{L}0-9-])*\.[a-z\p{L}0-9]+$/ui',
            $email
        );
    }

    /**
     * Check for MD5 string validity
     *
     * @param string $md5 MD5 string to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isMd5($md5)
    {
        return preg_match('/^[a-f0-9A-F]{32}$/', $md5);
    }

    /**
     * Check for SHA1 string validity
     *
     * @param string $sha1 SHA1 string to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isSha1($sha1)
    {
        return preg_match('/^[a-fA-F0-9]{40}$/', $sha1);
    }

    /**
     * Check for a float number validity
     *
     * @param float $float Float number to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isFloat($float)
    {
        return (string)(float)$float === (string)$float;
    }

    /**
     * Check for a unsigned float number validity
     *
     * @param float $float Float number to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isUnsignedFloat($float)
    {
        return (string)(float)$float === (string)$float && $float >= 0;
    }

    /**
     * Check for a float number validity (or empty)
     *
     * @param float $float Float number to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isOptFloat($float)
    {
        return $float === null || self::isFloat($float);
    }

    /**
     * Check for sender name validity
     *
     * @param string $mail_name Sender name to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isMailName($mail_name)
    {
        return (\is_string($mail_name) && preg_match('/^[^<>;=#{}]*$/u', $mail_name));
    }

    /**
     * Check for e-mail subject validity
     *
     * @param string $mail_subject e-mail subject to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isMailSubject($mail_subject)
    {
        return preg_match('/^[^<>]*$/u', $mail_subject);
    }

    /**
     * Check for price validity
     *
     * @param string $price Price to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isPrice($price)
    {
        return preg_match('/^\d{1,10}(\.\d{1,9})?$/', $price);
    }

    /**
     * Check for price validity (including negative price)
     *
     * @param string $price Price to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isNegativePrice($price)
    {
        return preg_match('/^[-]?\d{1,10}(\.\d{1,9})?$/', $price);
    }

    /**
     * Check for language code (ISO) validity
     *
     * @param string $iso_code Language code (ISO) to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isLanguageIsoCode($iso_code)
    {
        return preg_match('/^[a-zA-Z]{2,3}$/', $iso_code);
    }

    /**
     * Check for language code validity (eg. cs_CZ)
     *
     * @param string $s Language code to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isLanguageCode($s)
    {
        return preg_match('/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/', $s);
    }

    public static function isStateIsoCode($iso_code)
    {
        return preg_match('/^[a-zA-Z0-9]{1,4}((-)[a-zA-Z0-9]{1,4})?$/', $iso_code);
    }

    public static function isNumericIsoCode($iso_code)
    {
        return preg_match('/^\d{2,3}$/', $iso_code);
    }

    /**
     * Check for a postal address validity
     *
     * @param string $address Address to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isAddress($address)
    {
        return preg_match('/^[^!<>?=+@{}_$%]*$/u', $address);
    }

    /**
     * Check for city name validity
     *
     * @param string $city City name to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isCityName($city)
    {
        return preg_match('/^[^!<>;?=+@#"°{}_$%]*$/u', $city);
    }

    /**
     * Check for search query validity
     *
     * @param string $search Query to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isValidSearch($search)
    {
        return preg_match('/^[^<>;=#{}]{0,64}$/u', $search);
    }

    /**
     * Check for standard name validity
     *
     * @param string $name Name to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isGenericName($name)
    {
        return preg_match('/^[^<>={}]*$/u', $name);
    }

    /**
     * Check for HTML field validity (no XSS please !)
     *
     * @param string $html HTML field to validate
     * @param bool $allowIframe Allow iframe HTML tag
     *
     * @return bool Validity is ok or not
     */
    public static function isCleanHtml($html, $allowIframe = false)
    {
        $events = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange';
        $events .= '|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave|onerror|onselect|onreset|onabort|ondragdrop|onresize|onactivate|onafterprint|onmoveend';
        $events .= '|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onmove';
        $events .= '|onbounce|oncellchange|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondeactivate|ondrag|ondragend|ondragenter|onmousewheel';
        $events .= '|ondragleave|ondragover|ondragstart|ondrop|onerrorupdate|onfilterchange|onfinish|onfocusin|onfocusout|onhashchange|onhelp|oninput|onlosecapture|onmessage|onmouseup|onmovestart';
        $events .= '|onoffline|ononline|onpaste|onpropertychange|onreadystatechange|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onsearch|onselectionchange';
        $events .= '|onselectstart|onstart|onstop';

        if (preg_match('/<[\s]*script/im', $html) || preg_match('/(' . $events . ')[\s]*=/im', $html) ||
            preg_match('/script\:/im', $html)
        ) {
            return false;
        }

        return !(!$allowIframe && preg_match('/<[\s]*(i?frame|form|input|embed|object)/im', $html));
    }

    /**
     * Check for password validity
     *
     * @param string $passwd Password to validate
     * @param int $size
     *
     * @return boolean Validity is ok or not
     */
    public static function isPasswd($passwd, $size = Validate::PASSWORD_LENGTH)
    {
        return self::getPasswordComplexity($passwd, $size) >= self::PASSWORD_COMPLEXITY_MEDIUM;
    }

    public static function isPasswdAdmin($passwd)
    {
        return self::getPasswordComplexity($passwd, self::ADMIN_PASSWORD_LENGTH) >= self::PASSWORD_COMPLEXITY_STRONG;
    }

    /**
     * Check for configuration key validity
     *
     * @param string $config_name Configuration key to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isConfigName($config_name)
    {
        return preg_match('/^[a-zA-Z_0-9-]+$/', $config_name);
    }

    /**
     * Check for date format
     *
     * @param string $date Date to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isDateFormat($date)
    {
        return preg_match('/^(\d{4})-((0?\d)|(1[0-2]))-((0?\d)|([1-2]\d)|(3[01]))( \d{2}:\d{2}:\d{2})?$/', $date);
    }

    /**
     * Check for date validity
     *
     * @param string $date Date to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isDate($date)
    {
        if (!preg_match('/^(\d{4})-((?:0?\d)|(?:1[0-2]))-((?:0?\d)|(?:[1-2]\d)|(?:3[01]))( \d{2}:\d{2}:\d{2})?$/', $date, $matches)) {
            return false;
        }

        return checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]);
    }

    /**
     * Check for birthDate validity
     *
     * @param string $date birthdate to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isBirthDate($date)
    {
        if ($date === null || $date === '0000-00-00') {
            return true;
        }
        if (preg_match('/^(\d{4})-((?:0?[1-9])|(?:1[0-2]))-((?:0?[1-9])|(?:[1-2]\d)|(?:3[01]))(\d{2}:\d{2}:\d{2})?$/', $date, $birth_date)) {
            return !(($birth_date[1] > date('Y') && $birth_date[2] > date('m') && $birth_date[3] > date('d')) ||
                ($birth_date[1] === date('Y') && $birth_date[2] === date('m') && $birth_date[3] > date('d')) ||
                ($birth_date[1] === date('Y') && $birth_date[2] > date('m'))
            );
        }

        return false;
    }

    /**
     * Check for boolean validity
     *
     * @param boolean $bool Boolean to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isBool($bool)
    {
        return $bool === null || \is_bool($bool) || preg_match('/^0|1$/', $bool);
    }

    /**
     * Check for phone number validity
     *
     * @param string $number Phone number to validate
     * @param string $country Two digit uppercase country code (CZ, US, AU)
     *
     * @return boolean Validity is ok or not
     */
    public static function isPhoneNumber($number, $country)
    {
        return self::isPhoneNumber2($number, $country);
    }

    /**
     * Extended check for phone number validity (including specific country format)
     *
     * @param string $number Phone number to validate
     * @param string $country Two digit uppercase country code (CZ, US, AU)
     * @return bool Validity is ok or not
     */
    public static function isPhoneNumber2($number, $country)
    {
        if (empty($number)) {
            return false;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($number, strtoupper($country));

            return $phoneUtil->isValidNumber($numberProto);
        } catch (NumberParseException $e) {
            return false;
        }
    }

    /**
     * Check for time in hh:mm:ss format validity
     *
     * @param string $time Time
     *
     * @return boolean Validity is ok or not
     */
    public static function isTime($time)
    {
        return preg_match('/^(([0-1]\d)|([2][0-3])):([0-5]\d):([0-5]\d)$/', $time);
    }

    /**
     * Check for time in hh:mm:ss format validity. Accepts also null value
     *
     * @param string $time Time or null
     *
     * @return boolean Validity is ok or not
     */
    public static function isTimeOrNull($time)
    {
        return preg_match('/^(([0-1]\d)|([2][0-3])):([0-5]\d):([0-5]\d)$/', $time) or $time === null;
    }

    /**
     * Check for barcode validity (EAN-13)
     *
     * @param string $ean13 Barcode to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isEan13($ean13)
    {
        return preg_match('/^\d{0,13}$/', $ean13);
    }

    /**
     * Check for barcode validity (UPC)
     *
     * @param string $upc Barcode to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isUpc($upc)
    {
        return preg_match('/^\d{0,12}$/', $upc);
    }

    /**
     * Check for postal code validity
     *
     * @param string $postcode Postal code to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isPostCode($postcode)
    {
        return preg_match('/^[a-zA-Z 0-9-]+$/', $postcode);
    }

    /**
     * Check for zip code format validity
     *
     * @param string $zip_code zip code format to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isZipCodeFormat($zip_code)
    {
        return preg_match('/^[NLCnlc 0-9-]+$/', $zip_code);
    }

    /**
     * Check for table or identifier validity
     * Mostly used in database for ordering : ASC / DESC
     *
     * @param string $way Keyword to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isOrderWay($way)
    {
        return ($way === 'ASC' | $way === 'DESC' | $way === 'asc' | $way === 'desc');
    }

    /**
     * Check for table or identifier validity
     * Mostly used in database for ordering : ORDER BY field
     *
     * @param string $order Field to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isOrderBy($order)
    {
        return preg_match('/^[a-zA-Z0-9.!_-]+$/', $order);
    }

    /**
     * Check for tags list validity
     *
     * @param string $list List to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isTagsList($list)
    {
        return preg_match('/^[^!<>;?=+#"°{}_$%]*$/u', $list);
    }

    /**
     * Check for an integer validity
     *
     * @param integer $value Integer to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isInt($value)
    {
        return ((string)(int)$value === (string)$value || $value === false);
    }

    /**
     * Check for an integer validity (unsigned)
     *
     * @param integer $value Integer to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isUnsignedInt($value)
    {
        return (preg_match('#^\d+$#', (string)$value) && $value < 4294967296 && $value >= 0);
    }

    /**
     * Check for an percentage validity (between 0 and 100)
     *
     * @param float $value Float to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isPercentage($value)
    {
        return (self::isFloat($value) && $value >= 0 && $value <= 100);
    }

    /**
     * Check for an integer validity (unsigned)
     * Mostly used in database for auto-increment
     *
     * @param integer $id Integer to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isUnsignedId($id)
    {
        return self::isUnsignedInt($id); /* Because an id could be equal to zero when there is no association */
    }

    public static function isNullOrUnsignedId($id)
    {
        return $id === null || self::isUnsignedId($id);
    }

    /**
     * Check object validity
     *
     * @param object $object Object to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isLoadedObject($object)
    {
        return \is_object($object) && $object->id;
    }

    /**
     * Check hexadecimal color validity
     *
     * @param string $color Hexadecimal color to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isColor($color)
    {
        return preg_match('/^(#[0-9a-fA-F]{6}|[a-zA-Z0-9-]*)$/', $color);
    }

    /**
     * Check url validity (disallowed empty string)
     *
     * @param string $url Url to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isUrl($url)
    {
        return preg_match('/^[~:#,$%&_=\(\)\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
    }

    /**
     * Check tracking number validity (disallowed empty string)
     *
     * @param string $tracking_number Tracking number to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isTrackingNumber($tracking_number)
    {
        return preg_match('/^[~:#,%&_=\(\)\[\]\.\? \+\-@\/a-zA-Z0-9]+$/', $tracking_number);
    }

    /**
     * Check url validity (allowed empty string)
     *
     * @param string $url Url to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isUrlOrEmpty($url)
    {
        return $url === null || self::isUrl($url);
    }

    /**
     * Check if URL is absolute
     *
     * @param string $url URL to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isAbsoluteUrl($url)
    {
        return preg_match('/^https?:\/\/[$~:;#,%&_=\(\)\[\]\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
    }

    public static function isUnixName($data)
    {
        return preg_match('/^[a-z0-9\._-]+$/ui', $data);
    }

    public static function isTablePrefix($data)
    {
        // Even if "-" is theoretically allowed, it will be considered a syntax error if you do not add back quotes (`) around the table name
        return preg_match('/^[a-z0-9_]+$/ui', $data);
    }

    /**
     * Check for standard name file validity
     *
     * @param string $name Name to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isFileName($name)
    {
        return preg_match('/^[a-zA-Z0-9_.-]+$/', $name);
    }

    /**
     * Check for standard name directory validity
     *
     * @param string $dir Directory to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isDirName($dir)
    {
        return (bool)preg_match('/^[a-zA-Z0-9_.-]*$/', $dir);
    }

    public static function isWeightUnit($unit)
    {
        return (self::isGenericName($unit) & (\strlen($unit) < 5));
    }

    public static function isDistanceUnit($unit)
    {
        return (self::isGenericName($unit) & (\strlen($unit) < 5));
    }

    public static function isSubDomainName($domain)
    {
        return preg_match('/^[a-zA-Z0-9-_]*$/', $domain);
    }

    /**
     * Price display method validity
     *
     * @param string $data Data to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isString($data)
    {
        return \is_string($data);
    }

    /**
     * Check for PHP serialized data
     *
     * @param string $data Serialized data to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isSerializedArray($data)
    {
        return $data === null || (\is_string($data) && preg_match('/^a:\d+:{.*;}$/s', $data));
    }

    /**
     * Check for Latitude/Longitude
     *
     * @param string $data Coordinate to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isCoordinate($data)
    {
        return $data === null || preg_match('/^\-?\d{1,8}\.\d{1,8}$/', $data);
    }

    /**
     * Check for Language Iso Code
     *
     * @param string $iso_code
     *
     * @return boolean Validity is ok or not
     */
    public static function isLangIsoCode($iso_code)
    {
        return (bool)preg_match('/^[a-zA-Z]{2,3}$/', $iso_code);
    }

    /**
     *
     * @param array $ids
     *
     * @return boolean return true if the array contain only unsigned int value
     */
    public static function isArrayWithIds($ids)
    {
        if (\count($ids)) {
            foreach ($ids as $id) {
                if ((int)$id === 0 || !self::isUnsignedInt($id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if the phone number is mobile
     *
     * @param string $phone Phone number
     *
     * @return bool Is phone number mobile
     * @static
     */
    public static function isMobilePhoneNumber($phone)
    {
        $phoneNumber = substr(Tools::removeSpace($phone), -9, 1);

        return (!self::isCzechPhoneNumber($phoneNumber) || ($phoneNumber === '6' || $phoneNumber === '7'));
    }

    /**
     * Determine whether the phone number is from Czech Republic
     *
     * @param string $phone Phone number
     *
     * @return bool Is Czech phone number
     * @static
     */
    public static function isCzechPhoneNumber($phone)
    {
        return (bool)preg_match("/^(\\+420)? ?\\d{3} ?\\d{3} ?\\d{3}$/", $phone);
    }

    /**
     * Check if the GUID is valid
     *
     * @param string $uuid GUID
     *
     * @return bool Whether the GUID is valid
     * @static
     */
    public static function isGuid($uuid)
    {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' . '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }

    /**
     * Check if the birth number is valid
     *
     * @param string $no Birth number
     * @return bool Is valid
     */
    public static function isBirthNumber($no)
    {
        if (!preg_match('#^\s*(\d\d)(\d\d)(\d\d)[ /]*(\d\d\d)(\d?)\s*$#', $no, $matches)) {
            return false;
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
                return false;
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

        return checkdate($month, $day, $year);
    }

    /**
     * Check if specified string is valid pretty time
     *
     * @param string $time Pretty time
     * @return bool Is valid
     */
    public static function isPrettyTime($time)
    {
        return (bool)preg_match('/^(?:(?<weeks>\d+)w\s*)?(?:(?<days>\d+)d\s*)?(?:(?<hours>\d+)h\s*)?(?:(?<minutes>\d+)m\s*)?(?:(?<seconds>\d+)s\s*)?$/', $time);
    }

    /**
     * Check if input string is link-rewrite
     *
     * @param string $name Name to validate
     *
     * @return boolean Validity is ok or not
     */
    public static function isLinkRewrite($name)
    {
        return preg_match('/^[a-z0-9-]*$/u', $name);
    }

    /**
     * Get password complexity
     *
     * @param string $password Password
     * @param int $minLength Minimal length
     * @return int Password score
     */
    public static function getPasswordComplexity($password, $minLength)
    {
        $group = [
            'upper' => '/[A-Z]/',
            'lower' => '/[a-z]/',
            'number' => '/[0-9]/',
            'special' => '/[^A-Za-z0-9]/',
        ];
        $score = 0;
        $length = \strlen($password);

        if ($length < $minLength) {
            return 0;
        }

        // Increment the score for each of these conditions
        foreach ($group as $pattern) {
            if (preg_match($pattern, $password)) {
                $score++;
            }
        }

        // Penalize if there aren't at least three char types
        if ($score < 3) {
            $score--;
        }

        // Increment the score for every 2 chars longer than the minimum
        if ($length > $minLength) {
            $score += (int)floor(($length - $minLength) / 2);
        }

        return $score;
    }
}
