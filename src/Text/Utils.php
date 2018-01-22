<?php

namespace CottaCush\Yii2\Text;

use Hashids\Hashids;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Utils
 * @package app\libs
 * @author Olawale Lawal <wale@cottacush.com>
 * @codeCoverageIgnore
 */
class Utils
{
    const MIN_HASH_LENGTH = 12;

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $text
     * @param int $untouchedLength
     * @param string $char
     * @return string
     */
    public static function asteriskise($text, $untouchedLength = 6, $char = '*')
    {
        $length = strlen($text);
        if ($length <= $untouchedLength) {
            return str_repeat($char, $length);
        }

        $asteriskLength = $length - $untouchedLength;
        $asterisk = str_repeat($char, $asteriskLength);

        $maxLength = $length - $asteriskLength;
        $start = $maxLength / 2;
        $end = $length - $maxLength;
        return substr_replace($text, $asterisk, $start, $end);
    }

    /**
     * Encodes an id into an hash
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $id
     * @param $salt
     * @param int $hashLength
     * @return string
     */
    public static function encodeId($id, $salt, $hashLength = self::MIN_HASH_LENGTH)
    {
        $hashIds = new Hashids($salt, $hashLength);
        return $hashIds->encode($id);
    }

    /**
     * Decodes a hash into an id
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $hash
     * @param $salt
     * @param int $hashLength
     * @return int
     */
    public static function decodeId($hash, $salt, $hashLength = self::MIN_HASH_LENGTH)
    {
        $hashIds = new Hashids($salt, $hashLength);
        return ArrayHelper::getValue($hashIds->decode($hash), '0');
    }

    /**
     * Returns the styled label for the status
     * @author Olajide Oye <jide@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $status
     * @param string $extraClasses
     * @param string $baseClass
     * @param string $tag
     * @return string
     */
    public static function getStatusHtml($status, $extraClasses = '', $baseClass = 'label', $tag ='span')
    {
        $status = strtolower($status);
        $statusHyphenated = implode('-', explode(' ', $status));
        $class = trim("{$baseClass} {$baseClass}-$statusHyphenated $extraClasses");
        return Html::tag($tag, $status, ['class' => $class]);
    }

    /**
     * Returns the international format of the given number
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $countryCode
     * @param $number
     * @param $numberLength
     * @return string
     */
    public static function formatPhoneNumberToInternationalFormat($countryCode, $number, $numberLength)
    {
        $actualNumber = substr($number, -($numberLength), $numberLength);

        if (!$actualNumber) {
            return $number;
        }

        return '+' . $countryCode . $actualNumber;
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param array $base
     * @param array $challenger
     * @return bool
     */
    public static function isArrayDifferent($base, $challenger)
    {
        return count($base) != count($challenger) || array_diff($challenger, $base);
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param array $array
     * @return array
     */
    public static function flattenArray($array)
    {
        $result = array();

        if (!is_array($array)) {
            $array = func_get_args();
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::flattenArray($value));
            } else {
                $result = array_merge($result, array($key => $value));
            }
        }

        return $result;
    }
}
