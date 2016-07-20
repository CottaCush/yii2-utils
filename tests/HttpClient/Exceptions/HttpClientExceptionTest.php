<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\HttpClient\Exceptions;

use CottaCush\Yii2\HttpClient\Exceptions\HttpClientException;

class HttpClientExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExceptionName()
    {
        $exception = new HttpClientException();
        $this->assertEquals("HttpClientException", $exception->getName());
    }
}
