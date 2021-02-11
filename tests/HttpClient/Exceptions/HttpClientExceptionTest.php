<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\HttpClient\Exceptions;

use CottaCush\Yii2\HttpClient\Exceptions\HttpClientException;
use PHPUnit\Framework\TestCase;

class HttpClientExceptionTest extends TestCase
{
    public function testGetExceptionName()
    {
        $exception = new HttpClientException();
        $this->assertEquals("HttpClientException", $exception->getName());
    }
}
