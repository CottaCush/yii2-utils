<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\HttpClient;

use CottaCush\Yii2\HttpClient\Exceptions\HttpClientException;
use CottaCush\Yii2\HttpClient\TerraHttpClient;
use linslin\yii2\curl\Curl;
use PHPUnit\Framework\TestCase;
use yii\helpers\Json;

class TerraHttpClientTest extends TestCase
{
    const BASE_URL = "http://jsonplaceholder.typicode.com";
    private array $testPostParams = ['title' => 'test', 'author' => 'test'];
    const ACCESS_TOKEN = "123456";

    /** @var  $httpClient TerraHttpClient */
    protected TerraHttpClient $httpClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new TerraHttpClient(self::BASE_URL);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testInitializeWithInvalidUrl()
    {
        $this->expectException(HttpClientException::class);
        new TerraHttpClient('');
    }

    public function testBaseUrlNotNull()
    {
        $this->assertNotNull($this->httpClient->getBaseUrl());
    }

    public function testBaseUrl()
    {
        $this->assertSame(self::BASE_URL . "/", $this->httpClient->getBaseUrl());
    }

    public function testCurlAgentNotNull()
    {
        $this->assertNotNull($this->httpClient->getCurlAgent());
    }

    public function testCurlAgent()
    {
        $this->assertInstanceOf(Curl::class, $this->httpClient->getCurlAgent());
    }

    public function testUseRawResponse()
    {
        $this->assertTrue($this->httpClient->useRawResponse()->isRawResponse());
    }

    public function testUseJsonResponse()
    {
        $this->assertFalse($this->httpClient->useJsonResponse()->isRawResponse());
    }

    public function testUseAccessTokenWhenUseOauthTrue()
    {
        $this->assertTrue($this->httpClient->isUseOauth());
    }

    public function testUseAccessTokenWhenUseOauthFalse()
    {
        $this->httpClient->setUseOauth(false);
        $this->assertFalse($this->httpClient->isUseOauth());
    }

    /**
     * @throws HttpClientException
     */
    public function testSetAccessToken()
    {
        $this->assertNull($this->httpClient->getAccessToken());
        $this->httpClient->setAccessToken(self::ACCESS_TOKEN);
        $this->assertEquals(self::ACCESS_TOKEN, $this->httpClient->getAccessToken());
        $this->httpClient->get('posts');
        $this->assertStringContainsString(
            'access_token=' . $this->httpClient->getAccessToken(),
            $this->httpClient->getLastRequestUrl()
        );
    }

    /**
     * @throws HttpClientException
     */
    public function testBuildUrl()
    {
        $this->httpClient->get('posts', ['id' => 1]);
        $this->assertEquals(
            $this->httpClient->getBaseUrl() . 'posts?' . http_build_query(['id' => 1]),
            $this->httpClient->getLastRequestUrl()
        );
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testGetWithInvalidParams()
    {
        $this->expectException(HttpClientException::class);
        $this->httpClient->get('posts', "Adegoke Obasa");
    }

    public function testGetWithRawResponse()
    {
        $response = $this->httpClient->setUseOauth(false)
            ->useRawResponse()
            ->get('posts');
        $this->assertJson($response);
    }

    public function testGetWithJsonResponse()
    {
        $rawResponse = $this->httpClient->setUseOauth(false)
            ->useRawResponse()
            ->get('posts');

        $jsonResponse = $this->httpClient
            ->useJsonResponse()
            ->get('posts');
        $this->assertEquals(Json::decode($rawResponse), $jsonResponse);
    }

    public function testPostWithRawResponse()
    {
        $response = $this->httpClient
            ->useRawResponse()
            ->setUseOauth(false)
            ->post('posts', $this->testPostParams);
        $this->assertJson($response);
    }

    public function testPostWithJsonResponse()
    {
        $response = $this->httpClient
            ->useJsonResponse()
            ->setUseOauth(false)
            ->post('posts', $this->testPostParams);
        $this->assertEquals(['id' => 101], $response);
    }

    public function testPutWithRawResponse()
    {
        $id = 1;
        $response = $this->httpClient
            ->useRawResponse()
            ->setUseOauth(false)
            ->put("posts/$id", $this->testPostParams);
        $this->assertJson($response);
    }

    public function testPutWithJsonResponse()
    {
        $id = 1;
        $response = $this->httpClient
            ->useJsonResponse()
            ->setUseOauth(false)
            ->put("posts/$id", $this->testPostParams);
        $this->assertEquals(['id' => $id], $response);
    }

    public function testDeleteWithRawResponse()
    {
        $id = 1;
        $response = $this->httpClient
            ->useRawResponse()
            ->setUseOauth(false)
            ->delete("posts/$id", $this->testPostParams);
        $this->assertJson($response);
    }

    public function testDeleteWithJsonResponse()
    {
        $id = 1;
        $response = $this->httpClient
            ->useJsonResponse()
            ->setUseOauth(false)
            ->delete("posts/$id", $this->testPostParams);
        $this->assertEquals([], $response);
    }

    public function testGetParams()
    {
        $this->httpClient
            ->useRawResponse()
            ->setAccessToken(self::ACCESS_TOKEN)
            ->get('posts', $this->testPostParams);
        $this->assertEquals($this->testPostParams, $this->httpClient->getLastRequestParams());
    }

    public function testPostParams()
    {
        $this->httpClient
            ->useRawResponse()
            ->setAccessToken(self::ACCESS_TOKEN)
            ->post('posts', $this->testPostParams);
        $this->assertEquals($this->testPostParams, $this->httpClient->getLastRequestParams());
    }

    public function testPostJsonBody()
    {
        $params = Json::encode($this->testPostParams);
        $this->httpClient
            ->useRawResponse()
            ->setAccessToken(self::ACCESS_TOKEN)
            ->post('posts', $params);
        $this->assertJson($this->httpClient->getLastRequestParams());
    }
}
