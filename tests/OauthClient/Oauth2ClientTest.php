<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\HttpClient;

use CottaCush\Yii2\OauthClient\Oauth2Client;
use Faker\Factory;
use Faker\Generator;
use linslin\yii2\curl\Curl;
use yii\authclient\OAuth2;

class Oauth2ClientTest extends \PHPUnit_Framework_TestCase
{

    /** @var Generator $faker */
    protected $faker;

    public function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function testCurlAgentIsValid()
    {
        $oauth2Client = new Oauth2Client();
        $this->assertInstanceOf(Curl::class, $oauth2Client->getCurl());
    }

    public function testAuthClientIsValid()
    {
        $oauth2Client = new Oauth2Client();
        $this->assertInstanceOf(OAuth2::class, $oauth2Client->getOauth2());
    }

    protected function getDefaultParams()
    {
        return [
            Oauth2Client::AUTH_URL => $this->faker->url,
            Oauth2Client::TOKEN_URL => $this->faker->url,
            Oauth2Client::CLIENT_ID => $this->faker->md5,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5
        ];
    }

    protected function getOauth2ClientWithDefaultParams($defaultParams)
    {
        return new Oauth2Client($defaultParams);
    }

    public function testInitializeWithDefaultParams()
    {
        $defaultParams = $this->getDefaultParams();
        $oauthClient = $this->getOauth2ClientWithDefaultParams($defaultParams);

        $this->assertEquals($defaultParams[Oauth2Client::AUTH_URL], $oauthClient->getAuthUrl());
        $this->assertEquals($defaultParams[Oauth2Client::TOKEN_URL], $oauthClient->getTokenUrl());
        $this->assertEquals($defaultParams[Oauth2Client::CLIENT_ID], $oauthClient->getClientId());
        $this->assertEquals($defaultParams[Oauth2Client::CLIENT_SECRET], $oauthClient->getClientSecret());
    }

    public function testSetAndGetAuthUrl()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $authUrl = $this->faker->url;
        $oauthClient->setAuthUrl($authUrl);
        $this->assertEquals($authUrl, $oauthClient->getAuthUrl());
    }

    public function testSetAndGetTokenUrl()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $tokenUrl = $this->faker->url;
        $oauthClient->setTokenUrl($tokenUrl);
        $this->assertEquals($tokenUrl, $oauthClient->getTokenUrl());
    }

    public function testSetAndGetClientId()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $clientId = $this->faker->url;
        $oauthClient->setClientId($clientId);
        $this->assertEquals($clientId, $oauthClient->getClientId());
    }

    public function testSetAndGetClientSecret()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $clientSecret = $this->faker->url;
        $oauthClient->setClientSecret($clientSecret);
        $this->assertEquals($clientSecret, $oauthClient->getClientSecret());
    }

    /**
     * @expectedException \CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeWithInvalidAuthUrl()
    {
        $oauthClient = new Oauth2Client([
            Oauth2Client::CLIENT_ID => $this->faker->md5,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->authorize();
    }

    /**
     * @expectedException \CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeWithInvalidClientId()
    {
        $oauthClient = new Oauth2Client([
            Oauth2Client::AUTH_URL => $this->faker->url,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->authorize();
    }

    /**
     * @expectedException \CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeFailure()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams($this->getDefaultParams());
        $oauthClient->setAuthUrl("http://localhost"); // Set to a url that doesn't exist
        $oauthClient->authorize();
    }

    /**
     * @expectedException \CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeWithClientSecret()
    {
        $oauthClient = new Oauth2Client([
            Oauth2Client::AUTH_URL => $this->faker->url,
            Oauth2Client::CLIENT_ID => $this->faker->md5,
        ]);
        $oauthClient->authorize();
    }

    /**
     * @expectedException \CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testFetchTokenWithInvalidTokenUrl()
    {
        $oauthClient = new Oauth2Client([
            Oauth2Client::CLIENT_ID => $this->faker->md5,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->fetchAccessToken($this->faker->randomDigitNotNull);
    }

    /**
     * @expectedException \CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testFetchTokenWithInvalidClientId()
    {
        $oauthClient = new Oauth2Client([
            Oauth2Client::TOKEN_URL => $this->faker->url,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->fetchAccessToken($this->faker->randomDigitNotNull);
    }

    /**
     * @expectedException \CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testFetchTokenWithInvalidClientSecret()
    {
        $oauthClient = new Oauth2Client([
            Oauth2Client::TOKEN_URL => $this->faker->url,
            Oauth2Client::CLIENT_ID => $this->faker->md5,
        ]);
        $oauthClient->fetchAccessToken($this->faker->randomDigitNotNull);
    }
}
