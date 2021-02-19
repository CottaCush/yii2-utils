<?php

namespace CottaCush\Yii2\Date;

use DateTime;
use Exception;

/**
 * Class DateUtils
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package CottaCush\Yii2
 */
class DateUtils
{
    const INTERVAL_LAST_WEEK = "-1 week";

    /**
     * Gets the difference between two date periods
     * @param $fromTime
     * @param string $toTime
     * @param string $format
     * @return string
     * @throws Exception
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public static function toRelativeTime($fromTime, $toTime = 'now', $format = 'days'): string
    {
        $startTime = new DateTime($fromTime);
        $endTime = new DateTime($toTime);

        return $startTime->diff($endTime)->$format;
    }

    /**
     * Returns date range text
     * @param $startDate
     * @param $endDate
     * @param string $toSeparator
     * @return bool|string
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public static function getDateRangeText($startDate, $endDate, $toSeparator = 'to'): bool|string
    {
        if ($startDate == $endDate) {
            return self::format($startDate, DateFormat::FORMAT_SHORT);
        } elseif (self::format($startDate, DateFormat::FORMAT_YEAR)
            == self::format($endDate, DateFormat::FORMAT_YEAR)
        ) {
            $start_date = (
                self::format($startDate, DateFormat::FORMAT_MONTH)
                == self::format($endDate, DateFormat::FORMAT_MONTH))
                ? self::format($startDate, DateFormat::FORMAT_DAY) :
                self::format($startDate, DateFormat::FORMAT_SHORT_NO_YEAR);
        } else {
            $start_date = self::format($startDate, DateFormat::FORMAT_SHORT);
        }
        return $start_date . ' ' . $toSeparator . ' ' . self::format($endDate, DateFormat::FORMAT_SHORT);
    }

    /**
     * Format date
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $format
     * @param $datetime
     * @return bool|string
     */
    public static function format($datetime, $format = DateFormat::FORMAT_SHORT): bool|string
    {
        $datetime = strtotime($datetime);
        if (!$datetime) {
            return '';
        }
        return date($format, $datetime);
    }

    /**
     * Get's the current datetime in the mysql datetime format
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return bool|string
     */
    public static function getMysqlNow(): bool|string
    {
        return self::getCurrentDateTime(DateFormat::FORMAT_MYSQL_STYLE);
    }

    /**
     * Get's the current datetime in the specified format
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $format
     * @return bool|string
     */
    public static function getCurrentDateTime($format): bool|string
    {
        return date($format);
    }

    /**
     * Get's the current datetime in the oracle datetime format
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return bool|string
     */
    public static function getOracleNow(): bool|string
    {
        return self::getCurrentDateTime(DateFormat::FORMAT_ORACLE);
    }

    /**
     * Converts a datetime string from one format to another format
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $dateString
     * @param $fromFormat
     * @param $toFormat
     * @return string
     */
    public static function convertDate($dateString, $fromFormat, $toFormat): string
    {
        $date = DateTime::createFromFormat($fromFormat, $dateString);
        if (!$date && $fromFormat == DateFormat::FORMAT_ORACLE_WITH_MICROSECONDS) {
            $date = DateTime::createFromFormat(DateFormat::FORMAT_ORACLE_DATE_ONLY, $dateString);
        }

        if ($date) {
            return $date->format($toFormat);
        }
        return $dateString;
    }

    /**
     * Get's the datetime in the specified format
     * @param $format
     * @param string $timestamp
     * @return bool|string|null
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public static function getDateTime($format, $timestamp = 'now'): bool|string|null
    {
        $date = strtotime($timestamp);
        if (!$date) {
            return $timestamp;
        }
        return date($format, $date);
    }
}
