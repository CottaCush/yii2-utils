<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\HttpClient\Exceptions;

use CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException;
use PHPUnit\Framework\TestCase;

class Oauth2ClientExceptionTest extends TestCase
{
    public function testGetExceptionName()
    {
        $exception = new Oauth2ClientException();
        $this->assertEquals("Oauth2ClientException", $exception->getName());
    }
}
