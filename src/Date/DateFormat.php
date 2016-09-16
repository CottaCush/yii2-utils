<?php

namespace CottaCush\Yii2\Date;

/**
 * Class DateFormat
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package CottaCush\Yii2
 */
class DateFormat
{

    const FORMAT_LAST_DAY_OF_MONTH = 'Y-m-t';
    const FORMAT_YEAR = 'Y';
    const FORMAT_FRIENDLY_DATE = 'd M Y';
    const FORMAT_TIME_12H = 'g:ia';
    const FORMAT_TIME_24H = 'H:i';
    const FORMAT_DATE_PICKER = 'd/m/Y';
    const FORMAT_ORACLE_DATE_ONLY = "d-M-y";
    const FORMAT_FIRST_DAY_OF_YEAR = 'Y-1-1';
    const FORMAT_FIRST_DAY_OF_MONTH = 'Y-m-1';
    const FORMAT_MYSQL_STYLE_NO_TIME = 'Y-m-d';
    const FORMAT_DATE_TIME_SHORT = 'j M Y H:i';
    const FORMAT_SHORT = 'jS M Y';
    const FORMAT_ACCOUNT_SERVICE = "Y-m-d\\TH:i:s";
    const FORMAT_DATE_PICKADAY = 'Y/m/d';
    const FORMAT_MONTH = 'M';
    const FORMAT_DATE_SHORT_MONTH_YEAR = 'j M Y';
    const FORMAT_LAST_DAY_OF_YEAR = 'Y-12-1';
    const FORMAT_ORACLE_WITH_MICROSECONDS = "d-M-y h.i.s.u a";
    const FORMAT_ORACLE = "d-M-y h:i:s a";
    const FORMAT_SHORT_MONTH_DATE = 'M Y';
    const FORMAT_DATE_PICKER_MONTH_AND_YEAR = 'm/Y';
    const FORMAT_MYSQL_STYLE_TIME = 'H:i:s';
    const FORMAT_SHORT_NO_YEAR = 'jS M';
    const FORMAT_DATE_TIME_12H = 'j M Y g:ia';
    const FORMAT_LONG_WITH_OF = 'jS \\o\\f F Y';
    const FORMAT_MYSQL_STYLE = 'Y-m-d H:i:s';
    const FORMAT_DAY = 'jS';
    const FORMAT_LONG = 'jS F Y';
}
