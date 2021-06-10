<?php

namespace futuretek\shared;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\InvalidWriterException;
use Endroid\QrCode\QrCode;
use RuntimeException;

/**
 * Class QRPayment
 *
 * This class implements Short Payments Definition version 1.0
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz>
 * @license Apache-2.0
 * @link    http://www.futuretek.cz
 */
class QRPayment
{
    /**
     * Peer to peer
     */
    const PAYMENT_PEER_TO_PEER = 'P2P';

    /**
     * Notify via SMS
     */
    const NOTIFY_PHONE = 'P';

    /**
     * Notify via email
     */
    const NOTIFY_EMAIL = 'E';

    /**
     * Regexp for allowed input characters
     */
    const BINARY_QR_REGEXP = '[0-9A-Z $%*+-./:]';

    /**
     * Parts delimiter
     */
    const DELIMITER = '*';

    /**
     * Key - value delimiter
     */
    const KV_DELIMITER = ':';

    /**
     * @var bool Append CRC32 checksum
     */
    public $appendCRC32 = true;

    /**
     * @var string Spayd version
     */
    protected $version = '1.0';

    /**
     * @var array Spayd content holder
     */
    private $content = [];

    /**
     * @var array Spayd parts definition
     */
    private static $keysDefinition = [
        'ACC' => '[A-Z]{2}\d{2,32}(\+[A-Z0-9]{4}[A-Z]{2}[A-Z0-9]{2,5})?',
        'ALT-ACC' => '[A-Z]{2}\d{2,32}(\+[A-Z0-9]{4}[A-Z]{2}[A-Z0-9]{2,5})?(,[A-Z]{2}\d{2,32}(\+[A-Z0-9]{4}[A-Z]{2}[A-Z0-9]{2,5})?)?',
        'AM' => '^[0-9]{0,10}(\\.[0-9]{0,2})?$',
        'CC' => '[A-Z]{3}',
        'RF' => '\d{1,16}',
        'RN' => '[0-9A-Z $%+-./:]{1,35}',
        'DT' => '[12]\d{3}[01]\d[0-3]\d',
        'PT' => '[0-9A-Z $%+-./:]{1,3}',
        'MSG' => '[0-9A-Z $%+-./:]{1,60}',
        'CRC32' => '[A-F0-9]{8}',
        'NT' => '[PE]',
        'NTA' => '((\+|00)\'{12}|.+@.+\..+)',
        'X-PER' => '\d{1,2}',
        'X-VS' => '\d{1,10}',
        'X-SS' => '\d{1,10}',
        'X-KS' => '\d{1,10}',
        'X-ID' => '[0-9A-Z $%+-./:]{1,20}',
        'X-URL' => '[0-9A-Z $%+-./:]{1,140}',
    ];

    /**
     * Set account
     *
     * @param string $iban IBAN
     * @param string|null $swift SWIFT
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setAccount($iban, $swift = null)
    {
        if ($swift !== null) {
            $iban .= '+' . $swift;
        }

        return $this->add('ACC', $iban);
    }

    /**
     * Set alternative accounts
     *
     * @param string $iban1 Account 1 - IBAN
     * @param string|null $swift1 Account 1 - SWIFT
     * @param string|null $iban2 Account 2 - IBAN
     * @param string|null $swift2 Account 2 - SWIFT
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setAlternativeAccount($iban1, $swift1 = null, $iban2 = null, $swift2 = null)
    {
        if ($swift1 !== null) {
            $iban1 .= '+' . $swift1;
        }
        if ($iban2 !== null) {
            if ($swift2 !== null) {
                $iban2 .= '+' . $swift2;
            }
            $iban1 .= ',' . $iban2;
        }

        return $this->add('ALT-ACC', $iban1);
    }

    /**
     * Set payment amount
     *
     * @param float $amount Amount
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setAmount($amount)
    {
        return $this->add('AM', $amount);
    }

    /**
     * Set currency
     *
     * @param float $code Currency code (USD, EUR, CZK)
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setCurrency($code)
    {
        return $this->add('CC', $code);
    }

    /**
     * Set recipient identificator
     *
     * @param string $id Recipient identificator (number, max 16 digits)
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setRecipientId($id)
    {
        return $this->add('RF', $id);
    }

    /**
     * Set recipient name
     *
     * @param string $name Recipient name (max 16 characters)
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setRecipientName($name)
    {
        return $this->add('RN', $name);
    }

    /**
     * Set due date
     *
     * @param \DateTime $dueDate Due date
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setDueDate(\DateTime $dueDate)
    {
        return $this->add('DT', $dueDate->format('Ymd'));
    }

    /**
     * Set payment type
     *
     * @param string $paymentType Payment type - see constants PAYMENT_*
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setPaymentType($paymentType)
    {
        if (self::PAYMENT_PEER_TO_PEER !== $paymentType) {
            throw new RuntimeException(Tools::poorManTranslate('fts-shared', 'Invalid payment type.'));
        }

        return $this->add('PT', $paymentType);
    }

    /**
     * Set message
     *
     * @param string $message Message (max. 60 characters)
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setMessage($message)
    {
        return $this->add('MSG', $message);
    }

    /**
     * Set notify type (bank-specific)
     *
     * @param string $notifyType Notify type - see constants NOTIFY_*
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setNotifyType($notifyType)
    {
        if (!\in_array($notifyType, [self::NOTIFY_EMAIL, self::NOTIFY_PHONE], true)) {
            throw new RuntimeException(Tools::poorManTranslate('fts-shared', 'Invalid notify type.'));
        }

        return $this->add('NT', $notifyType);
    }

    /**
     * Set notify target (phone number or email address)
     *
     * @param string $address Notify address
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setNotifyAddress($address)
    {
        return $this->add('NTA', $address);
    }

    /**
     * Set payment execution retry (in case there is not enough funds on senders account)
     *
     * @param string $days Number of days
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setPaymentExecutionRetry($days)
    {
        return $this->add('X-PER', (int)$days);
    }

    /**
     * Set variable symbol
     *
     * @param string $vs Variable symbol
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setVS($vs)
    {
        return $this->add('X-VS', (int)$vs);
    }

    /**
     * Set specific symbol
     *
     * @param string $ss Specific symbol
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setSS($ss)
    {
        return $this->add('X-SS', (int)$ss);
    }

    /**
     * Set constant symbol
     *
     * @param string $ks Constant symbol
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setKS($ks)
    {
        return $this->add('X-KS', (int)$ks);
    }

    /**
     * Set sender identificator
     *
     * @param string $id Sender identificator (max 20 characters)
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setSenderId($id)
    {
        return $this->add('X-ID', $id);
    }

    /**
     * Set URL
     *
     * @param string $url URL address (max. 140 characters)
     * @return QRPayment
     * @throws RuntimeException
     */
    public function setUrl($url)
    {
        return $this->add('X-URL', $url);
    }

    /**
     * @param string $key
     * @param string $value
     * @return QRPayment
     * @throws RuntimeException
     */
    protected function add($key, $value)
    {
        $key = $this->normalizeKey($key);
        if (!array_key_exists($key, self::$keysDefinition) && strpos($key, 'X-') !== 0) {
            throw new RuntimeException(Tools::poorManTranslate('fts-shared', 'Key {key} is not defined in specification.', ['key' => $key]));
        }

        if (array_key_exists($key, self::$keysDefinition) && !preg_match('~^' . self::$keysDefinition[$key] . '$~', $value)) {
            throw new RuntimeException(Tools::poorManTranslate('fts-shared', 'Key {key} with value {value} does not match defined format.', ['key' => $key, 'value' => $value]));
        }

        $this->content[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return QRPayment
     */
    protected function delete($key)
    {
        unset($this->content[$this->normalizeKey($key)]);

        return $this;
    }

    /**
     * Normalize key
     *
     * @param string $key
     * @return string
     */
    private function normalizeKey($key)
    {
        return strtoupper($key);
    }

    /**
     * Generate SPD as text
     *
     * @return string
     */
    public function generateText()
    {
        $result = 'SPD' . self::DELIMITER . $this->version . self::DELIMITER . $this->implodeContent();

        if ($this->appendCRC32) {
            $result .= self::DELIMITER . 'CRC32:' . sprintf('%x', crc32($result));
        }

        return $result;
    }

    /**
     * Generate SPD as PNG image and output it
     *
     * @param string|bool $filename Image filename
     * @param int $level QR code error correction level - please see Constants::QR_ECLEVEL_*
     * @param int $size QR code size (1 - 1024)
     * @param int $margin QR code margin
     * @throws InvalidWriterException
     */
    public function generateImage($filename = false, $level = ErrorCorrectionLevel::LOW, $size = 160, $margin = 4)
    {
        $result = 'SPD' . self::DELIMITER . $this->version . self::DELIMITER . $this->implodeContent();

        if ($this->appendCRC32) {
            $result .= self::DELIMITER . 'CRC32:' . sprintf('%x', crc32($result));
        }

        $qrCode = new QrCode($result);
        $qrCode
            ->setWriterByName('png')
            ->setSize($size)
            ->setMargin($margin)
            //->setEncoding('UTF-8')
            ->setErrorCorrectionLevel($level)
            ->setValidateResult(false);

        if ($filename) {
            $qrCode->writeFile($filename);

            return;
        }

        header('Content-Type: ' . $qrCode->getContentType());
        echo $qrCode->writeString();

        die();
    }

    /**
     * Generate SPD
     *
     * @return string
     *
     * @deprecated  Please use QRPayment::generateText() instead
     */
    public function generate()
    {
        return $this->generateText();
    }

    /**
     * Build Spayd content from parts
     *
     * @return string
     */
    private function implodeContent()
    {
        ksort($this->content);
        $output = '';
        foreach ($this->content as $key => $value) {
            $output .= $key . self::KV_DELIMITER . $value . self::DELIMITER;
        }

        return rtrim($output, self::DELIMITER);
    }

    /**
     * Normalize account number
     *
     * @param string $account Account number
     * @return string
     */
    public static function normalizeAccountNumber($account)
    {
        $account = str_replace(' ', '', $account);
        if (false === strpos($account, '-')) {
            $account = '000000-' . $account;
        }
        $parts = explode('-', $account);
        $parts[0] = str_pad($parts[0], 6, '0', STR_PAD_LEFT);
        $parts2 = explode('/', $parts[1]);
        $parts2[0] = str_pad($parts2[0], 10, '0', STR_PAD_LEFT);
        $parts2[1] = str_pad($parts2[1], 4, '0', STR_PAD_LEFT);
        $parts[1] = implode('/', $parts2);

        return implode('-', $parts);
    }

    /**
     * Convert bank account number to IBAN
     *
     * @param string $account Normal account number in format: prefix-account/bank
     * @param string $country ISO standard country code
     * @return string IBAN
     * @throws RuntimeException
     */
    public static function accountToIBAN($account, $country = 'CZ')
    {
        $allowedCountries = ['AT', 'BE', 'BG', 'CZ', 'CY', 'DK', 'EE', 'FI', 'FR', 'DE', 'GI', 'GR', 'HU', 'IE', 'IS', 'IT', 'LI', 'LT', 'LU', 'LV', 'MC', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'CH', 'SI', 'SK', 'ES', 'GB'];

        $account = self::normalizeAccountNumber($account);

        $accountArray = explode('/', str_replace('-', '', $account));
        if (2 !== \count($accountArray)) {
            throw new RuntimeException(Tools::poorManTranslate('fts-shared', 'Wrong bank account (some part missing).'));
        }

        $country = strtoupper($country);
        if (!\in_array($country, $allowedCountries, true)) {
            throw new RuntimeException(Tools::poorManTranslate('fts-shared', 'Invalid country code.'));
        }

        $accountStr = str_pad($accountArray[1], 4, '0', STR_PAD_LEFT) . str_pad($accountArray[0], 16, '0', STR_PAD_LEFT) . (\ord($country[0]) - 55) . (\ord($country[1]) - 55) . '00';
        $crc = '';
        $pos = 0;

        while (\strlen($accountStr) > 0) {
            $len = 9 - \strlen($crc);
            $crc = (int)($crc . substr($accountStr, $pos, $len)) % 97;
            $accountStr = substr($accountStr, $len);
        }

        return ($country . str_pad(98 - $crc, 2, '0', STR_PAD_LEFT) . $accountArray[1] . $accountArray[0]);
    }
}
