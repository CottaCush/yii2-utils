<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\HttpClient\Exceptions;

use CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException;

class Oauth2ClientExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExceptionName()
    {
        $exception = new Oauth2ClientException();
        $this->assertEquals("Oauth2ClientException", $exception->getName());
    }
}
