<?php

namespace CottaCush\Yii2\Date;

use DateTime;

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
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $fromTime
     * @param string $toTime
     * @param string $format
     * @return string
     */
    public static function toRelativeTime($fromTime, $toTime = 'now', $format = 'days')
    {
        $startTime = new DateTime($fromTime);
        $endTime = new DateTime($toTime);

        return $startTime->diff($endTime)->$format;
    }

    /**
     * Returns date range text
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $startDate
     * @param $endDate
     * @param string $toSeparator
     * @return string
     */
    public static function getDateRangeText($startDate, $endDate, $toSeparator = 'to')
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
    public static function format($datetime, $format = DateFormat::FORMAT_SHORT)
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
    public static function getMysqlNow()
    {
        return self::getCurrentDateTime(DateFormat::FORMAT_MYSQL_STYLE);
    }

    /**
     * Get's the current datetime in the specified format
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $format
     * @return bool|string
     */
    public static function getCurrentDateTime($format)
    {
        return date($format);
    }

    /**
     * Get's the current datetime in the oracle datetime format
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return bool|string
     */
    public static function getOracleNow()
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
    public static function convertDate($dateString, $fromFormat, $toFormat)
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
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $format
     * @param null|string $timestamp
     * @return bool|string
     */
    public static function getDateTime($format, $timestamp = 'now')
    {
        $date = strtotime($timestamp);
        if (!$date) {
            return $timestamp;
        }
        return date($format, $date);
    }
}
