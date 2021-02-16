<?php

namespace CottaCush\Yii2\Text;

use CottaCush\Yii2\Template\HandlebarsTemplatingEngine;
use Exception;
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
    public static function asteriskise($text, $untouchedLength = 6, $char = '*'): string
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
     * @throws Exception
     */
    public static function encodeId($id, $salt, $hashLength = self::MIN_HASH_LENGTH): string
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
     * @throws Exception
     */
    public static function decodeId($hash, $salt, $hashLength = self::MIN_HASH_LENGTH): int
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
    public static function getStatusHtml($status, $extraClasses = '', $baseClass = 'label', $tag = 'span'): string
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
    public static function formatPhoneNumberToInternationalFormat($countryCode, $number, $numberLength): string
    {
        $actualNumber = substr($number, -($numberLength), $numberLength);

        if (!$actualNumber) {
            return $number;
        }

        return '+' . $countryCode . $actualNumber;
    }

    /**
     * @param array $base
     * @param array $challenger
     * @return bool
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public static function isArrayDifferent(array $base, array $challenger): bool
    {
        return count($base) != count($challenger) || array_diff($challenger, $base);
    }

    /**
     * @param array $array
     * @return array
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public static function flattenArray(array $array): array
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

    /**
     * @param $amount
     * @return string
     */
    public static function formatToNaira($amount): string
    {
        if (is_null($amount) || !is_numeric($amount)) {
            return $amount;
        }

        return number_format($amount, 2) . 'NGN';
    }

    /**
     * @author Kehinde Ladipo <kehinde.ladipo@cottacush.com>
     * @param $template
     * @param array $params
     * @return string
     */
    public static function getActualMessage($template, array $params): string
    {
        $engine = new HandlebarsTemplatingEngine();
        return $engine->renderTemplate($template, $params);
    }
}
