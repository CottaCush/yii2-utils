<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\OauthClient;

use CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException;
use CottaCush\Yii2\OauthClient\Oauth2Client;
use Exception;
use Faker\Factory;
use Faker\Generator;
use linslin\yii2\curl\Curl;
use PHPUnit\Framework\TestCase;
use yii\authclient\OAuth2;

class Oauth2ClientTest extends TestCase
{
    protected Generator $faker;

    public function setUp(): void
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

    protected function getDefaultParams(): array
    {
        return [
            Oauth2Client::AUTH_URL => $this->faker->url,
            Oauth2Client::TOKEN_URL => $this->faker->url,
            Oauth2Client::CLIENT_ID => $this->faker->md5,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5
        ];
    }

    /**
     * @param $defaultParams
     * @return Oauth2Client
     * @throws Exception
     */
    protected function getOauth2ClientWithDefaultParams($defaultParams): Oauth2Client
    {
        return new Oauth2Client($defaultParams);
    }

    /**
     * @throws Exception
     */
    public function testInitializeWithDefaultParams()
    {
        $defaultParams = $this->getDefaultParams();
        $oauthClient = $this->getOauth2ClientWithDefaultParams($defaultParams);

        $this->assertEquals($defaultParams[Oauth2Client::AUTH_URL], $oauthClient->getAuthUrl());
        $this->assertEquals($defaultParams[Oauth2Client::TOKEN_URL], $oauthClient->getTokenUrl());
        $this->assertEquals($defaultParams[Oauth2Client::CLIENT_ID], $oauthClient->getClientId());
        $this->assertEquals($defaultParams[Oauth2Client::CLIENT_SECRET], $oauthClient->getClientSecret());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetAuthUrl()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $authUrl = $this->faker->url;
        $oauthClient->setAuthUrl($authUrl);
        $this->assertEquals($authUrl, $oauthClient->getAuthUrl());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetTokenUrl()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $tokenUrl = $this->faker->url;
        $oauthClient->setTokenUrl($tokenUrl);
        $this->assertEquals($tokenUrl, $oauthClient->getTokenUrl());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetClientId()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $clientId = $this->faker->url;
        $oauthClient->setClientId($clientId);
        $this->assertEquals($clientId, $oauthClient->getClientId());
    }

    /**
     * @throws Exception
     */
    public function testSetAndGetClientSecret()
    {
        $oauthClient = $this->getOauth2ClientWithDefaultParams([]);
        $clientSecret = $this->faker->url;
        $oauthClient->setClientSecret($clientSecret);
        $this->assertEquals($clientSecret, $oauthClient->getClientSecret());
    }

    /**
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeWithInvalidAuthUrl()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = new Oauth2Client([
            Oauth2Client::CLIENT_ID => $this->faker->md5,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->authorize();
    }

    /**
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeWithInvalidClientId()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = new Oauth2Client([
            Oauth2Client::AUTH_URL => $this->faker->url,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->authorize();
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeFailure()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = $this->getOauth2ClientWithDefaultParams($this->getDefaultParams());
        $oauthClient->setAuthUrl("http://localhost"); // Set to a url that doesn't exist
        $oauthClient->authorize();
    }

    /**
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testAuthorizeWithClientSecret()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = new Oauth2Client([
            Oauth2Client::AUTH_URL => $this->faker->url,
            Oauth2Client::CLIENT_ID => $this->faker->md5,
        ]);
        $oauthClient->authorize();
    }

    /**
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testFetchTokenWithInvalidTokenUrl()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = new Oauth2Client([
            Oauth2Client::CLIENT_ID => $this->faker->md5,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->fetchAccessToken($this->faker->randomDigitNotNull);
    }

    /**
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testFetchTokenWithInvalidClientId()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = new Oauth2Client([
            Oauth2Client::TOKEN_URL => $this->faker->url,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->fetchAccessToken($this->faker->randomDigitNotNull);
    }

    /**
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testFetchTokenWithInvalidClientSecret()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = new Oauth2Client([
            Oauth2Client::TOKEN_URL => $this->faker->url,
            Oauth2Client::CLIENT_ID => $this->faker->md5,
        ]);
        $oauthClient->fetchAccessToken($this->faker->randomDigitNotNull);
    }

    /**
     * @throws Exception
     * @author Akinwunmi Taiwo <taiwo@cottacush.com>
     */
    public function testFetchTokenWithClientGrantType()
    {
        $this->expectException(Oauth2ClientException::class);
        $oauthClient = new Oauth2Client([
            Oauth2Client::TOKEN_URL => $this->faker->url,
            Oauth2Client::CLIENT_ID => $this->faker->md5,
            Oauth2Client::CLIENT_SECRET => $this->faker->md5,
        ]);
        $oauthClient->fetchAccessTokenWithClientCredentials();
    }
}
