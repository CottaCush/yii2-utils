<?php

namespace CottaCush\Yii2\Tests\Date;

use CottaCush\Yii2\Date\DateFormat;
use CottaCush\Yii2\Date\DateUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class DateUtilsTest
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package CottaCush\Yii2\Tests\Date
 */
class DateUtilsTest extends TestCase
{
    public function testToRelativeTime()
    {
        $relativeTime = DateUtils::toRelativeTime(
            date(DateFormat::FORMAT_MYSQL_STYLE_NO_TIME, strtotime('-1 day'))
        );
        $this->assertEquals(1, $relativeTime);
    }

    /**
     * @param $startDate string starting date
     * @param $endDate string ending date
     * @param $expectedDateRange string date range text
     * @dataProvider providerTestGetDateRangeText
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function testGetDateRangeText(string $startDate, string $endDate, string $expectedDateRange)
    {
        $dateRangeText = DateUtils::getDateRangeText($startDate, $endDate);
        $this->assertEquals($expectedDateRange, $dateRangeText);
    }

    public function providerTestGetDateRangeText(): array
    {
        return [
            ['2016-09-15', '2016-09-16', '15th to 16th Sep 2016'],
            ['2015-09-15', '2016-09-16', '15th Sep 2015 to 16th Sep 2016'],
            ['2016-08-16', '2016-09-16', '16th Aug to 16th Sep 2016'],
            ['2016-08-16', '2016-08-16', '16th Aug 2016'],
        ];
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @provider providerTestFormat
     * @param $format
     * @param $actualDateText
     * @param string $dateTime
     * @dataProvider providerTestFormat
     */
    public function testFormat($format, $actualDateText, $dateTime = '2016-09-16 11:40:40')
    {
        $dateText = DateUtils::format($dateTime, $format);
        $this->assertEquals($actualDateText, $dateText);
    }

    public function providerTestFormat(): array
    {
        return [
            [DateFormat::FORMAT_MYSQL_STYLE, '2016-09-16 11:40:40'],
            [DateFormat::FORMAT_MYSQL_STYLE_NO_TIME, '2016-09-16'],
            [DateFormat::FORMAT_MYSQL_STYLE_TIME, '11:40:40'],
            [DateFormat::FORMAT_SHORT, '16th Sep 2016'],
            [DateFormat::FORMAT_SHORT_NO_YEAR, '16th Sep'],
            [DateFormat::FORMAT_LONG, '16th September 2016'],
            [DateFormat::FORMAT_LONG_WITH_OF, '16th of September 2016'],
            [DateFormat::FORMAT_YEAR, '2016'],
            [DateFormat::FORMAT_MONTH, 'Sep'],
            [DateFormat::FORMAT_DAY, '16th'],
            [DateFormat::FORMAT_TIME_24H, '11:40'],
            [DateFormat::FORMAT_TIME_12H, '11:40am'],
            [DateFormat::FORMAT_FIRST_DAY_OF_MONTH, '2016-09-1'],
            [DateFormat::FORMAT_LAST_DAY_OF_MONTH, '2016-09-30'],
            [DateFormat::FORMAT_LAST_DAY_OF_YEAR, '2016-12-1'],
            [DateFormat::FORMAT_FIRST_DAY_OF_YEAR, '2016-1-1'],
            [DateFormat::FORMAT_DATE_TIME_SHORT, '16 Sep 2016 11:40'],
            [DateFormat::FORMAT_DATE_SHORT_MONTH_YEAR, '16 Sep 2016'],
            [DateFormat::FORMAT_DATE_TIME_12H, '16 Sep 2016 11:40am'],
            [DateFormat::FORMAT_DATE_PICKADAY, '2016/09/16'],
            [DateFormat::FORMAT_DATE_PICKER, '16/09/2016'],
            [DateFormat::FORMAT_DATE_PICKER_MONTH_AND_YEAR, '09/2016'],
            [DateFormat::FORMAT_ORACLE, '16-Sep-16 11:40:40 am'],
            [DateFormat::FORMAT_ORACLE_WITH_MICROSECONDS, '16-Sep-16 11.40.40.000000 am'],
            [DateFormat::FORMAT_ORACLE_DATE_ONLY, '16-Sep-16'],
            [DateFormat::FORMAT_FRIENDLY_DATE, '16 Sep 2016'],
            [DateFormat::FORMAT_SHORT_MONTH_DATE, 'Sep 2016'],
            [DateFormat::FORMAT_ACCOUNT_SERVICE, '2016-09-16T11:40:40'],
            [DateFormat::FORMAT_ACCOUNT_SERVICE, '', ''],
            ['', '']
        ];
    }

    public function testGetMysqlNow()
    {
        $dateTime = DateUtils::getMysqlNow();
        $this->assertEquals(date(DateFormat::FORMAT_MYSQL_STYLE), $dateTime);
    }

    public function testGetCurrentDateTime()
    {
        $dateTime = DateUtils::getCurrentDateTime(DateFormat::FORMAT_MYSQL_STYLE);
        $this->assertEquals(date(DateFormat::FORMAT_MYSQL_STYLE), $dateTime);
    }

    public function testGetOracleNow()
    {
        $dateTime = DateUtils::getOracleNow();
        $this->assertEquals(date(DateFormat::FORMAT_ORACLE), $dateTime);
    }

    /**
     * @param $fromFormat
     * @param $toFormat
     * @param $expectedDateTime
     * @param string $dateTime
     * @dataProvider providerTestConvertDate
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function testConvertDate($fromFormat, $toFormat, $expectedDateTime, string $dateTime)
    {
        $convertedDate = DateUtils::convertDate(
            $dateTime,
            $fromFormat,
            $toFormat
        );
        $this->assertEquals($expectedDateTime, $convertedDate);
    }

    public function providerTestConvertDate(): array
    {
        return [
            [
                DateFormat::FORMAT_MYSQL_STYLE,
                DateFormat::FORMAT_DATE_TIME_SHORT,
                '16 Sep 2016 11:40',
                '2016-09-16 11:40:40'
            ],
            [
                DateFormat::FORMAT_ORACLE_WITH_MICROSECONDS,
                DateFormat::FORMAT_DATE_TIME_SHORT,
                '2016-09-16 11:40',
                '2016-09-16 11:40'
            ]
        ];
    }

    public function testGetDateTime()
    {
        $dateTime = DateUtils::getDateTime(DateFormat::FORMAT_MYSQL_STYLE);
        $this->assertEquals(date('Y-m-d H:i:s'), $dateTime);

        $dateTime = DateUtils::getDateTime(DateFormat::FORMAT_MYSQL_STYLE, false);
        $this->assertEquals($dateTime, '');
    }
}
