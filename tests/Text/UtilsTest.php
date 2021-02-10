<?php

namespace CottaCush\Yii2\Tests\Text;

use CottaCush\Yii2\Text\Utils;
use PHPUnit\Framework\TestCase;

/**
 * Class UtilsTest
 * @author Kehinde Ladipo <kehinde.ladipo@cottacush.com>
 * @package CottaCush\Yii2\Tests\Text
 */
class UtilsTest extends TestCase
{

    public function testFormatPhoneNumberToInternationalFormat()
    {
        $internationalNumber = Utils::formatPhoneNumberToInternationalFormat(
            '234',
            '08111111111',
            10
        );

        $this->assertEquals('+2348111111111', $internationalNumber);

        $internationalNumber = Utils::formatPhoneNumberToInternationalFormat(
            '234',
            '8111111111',
            10
        );

        $this->assertEquals('+2348111111111', $internationalNumber);

        $internationalNumber = Utils::formatPhoneNumberToInternationalFormat(
            '234',
            '+2348000000000',
            10
        );

        $this->assertEquals('+2348000000000', $internationalNumber);

        $internationalNumber = Utils::formatPhoneNumberToInternationalFormat(
            '234',
            '23408111111111',
            10
        );

        $this->assertEquals('+2348111111111', $internationalNumber);

        $internationalNumber = Utils::formatPhoneNumberToInternationalFormat(
            '234',
            '+23408111111111',
            10
        );

        $this->assertEquals('+2348111111111', $internationalNumber);
    }


    public function testFormattingToNigerianNaira()
    {
        $template = '{{formatToNaira money}}';
        $data = ['money' => 500];
        $this->assertEquals('500.00NGN', Utils::getActualMessage($template, $data));
    }

    public function testAddCountableSuffix()
    {
        $template = '{{appendCountableSuffix number child children}}';
        $data = ['number' => 500];
        $this->assertEquals('500 children', Utils::getActualMessage($template, $data));
        $data = ['number' => 1];
        $this->assertEquals('1 child', Utils::getActualMessage($template, $data));
    }
}
