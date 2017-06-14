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
     * @param $status
     * @param string $extraClasses
     * @return string
     */
    public static function getStatusHtml($status, $extraClasses = '')
    {
        $status = strtolower($status);
        $statusHyphenated = implode('-', explode(' ', $status));
        $class = trim("label label-$statusHyphenated $extraClasses");
        return Html::tag('span', $status, ['class' => $class]);
    }

    /**
     * Returns the max number of words followed by ellipsis if string has more words
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $string
     * @param $maxNoOfWords
     * @return string
     */
    public static function wordEllipsis($string, $maxNoOfWords)
    {
        $words = explode(' ', $string);
        $numOfWords = count($words);
        return sprintf(
            "%s%s",
            implode(' ', ($numOfWords > $maxNoOfWords) ? array_splice($words, 0, $maxNoOfWords) : $words),
            ($numOfWords > $maxNoOfWords) ? "..." : ""
        );
    }
}
