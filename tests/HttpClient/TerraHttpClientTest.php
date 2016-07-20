<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Tests\HttpClient;

use CottaCush\Yii2\HttpClient\TerraHttpClient;
use linslin\yii2\curl\Curl;
use yii\helpers\Json;

class TerraHttpClientTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = "http://jsonplaceholder.typicode.com";

    /** @var  $httpClient TerraHttpClient */
    protected $httpClient;

    public function setUp()
    {
        parent::setUp();
        $this->httpClient = new TerraHttpClient(self::BASE_URL);
    }

    /**
     * @expectedException \CottaCush\Yii2\HttpClient\Exceptions\HttpClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testInitializeWithInvalidUrl()
    {
        new TerraHttpClient('');
    }

    public function testInitialize()
    {
        $this->assertNotNull($this->httpClient->getBaseUrl());
        $this->assertSame(self::BASE_URL . "/", $this->httpClient->getBaseUrl());
        $this->assertNotNull($this->httpClient->getCurlAgent());
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

    public function testUseAccessToken()
    {
        $this->assertTrue($this->httpClient->isUseOauth());
        $this->httpClient->setUseOauth(false);
        $this->assertFalse($this->httpClient->isUseOauth());
    }

    public function testSetAccessToken()
    {
        $this->assertNull($this->httpClient->getAccessToken());
        $accessToken = '1234567890';
        $this->httpClient->setAccessToken($accessToken);
        $this->assertEquals($accessToken, $this->httpClient->getAccessToken());
        $this->httpClient->get('posts');
        $this->assertArrayHasKey('access_token', $this->httpClient->getLastRequestParams());
    }

    public function testBuildUrl()
    {
        $this->httpClient->get('posts', ['id' => 1]);
        $this->assertEquals(
            $this->httpClient->getBaseUrl() . 'posts?' . http_build_query(['id' => 1]),
            $this->httpClient->getLastRequestUrl()
        );
    }

    /**
     * @expectedException \CottaCush\Yii2\HttpClient\Exceptions\HttpClientException
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function testGetWithInvalidParams()
    {
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

    public function testPostWithRawResponseWithoutAccessToken()
    {
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useRawResponse()
            ->setUseOauth(false)
            ->post('posts', $params);
        $this->assertJson($response);
        $this->assertEquals($params, $this->httpClient->getLastRequestParams());
    }

    public function testPostWithRawResponseWithAccessToken()
    {
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useRawResponse()
            ->setAccessToken("123456")
            ->post('posts', $params);
        $this->assertJson($response);
        $this->assertEquals(
            array_merge($params, ['access_token' => $this->httpClient->getAccessToken()]),
            $this->httpClient->getLastRequestParams()
        );
    }

    public function testPostWithJsonResponseWithoutAccessToken()
    {
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useJsonResponse()
            ->setUseOauth(false)
            ->post('posts', $params);
        $this->assertEquals(['id' => 101], $response);
        $this->assertEquals($params, $this->httpClient->getLastRequestParams());
    }

    public function testPostWithJsonResponseWithAccessToken()
    {
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useJsonResponse()
            ->setAccessToken("123456")
            ->post('posts', $params);
        $this->assertEquals(['id' => 101], $response);
        $this->assertEquals(
            array_merge($params, ['access_token' => $this->httpClient->getAccessToken()]),
            $this->httpClient->getLastRequestParams()
        );
    }

    public function testPutWithRawResponseWithoutAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useRawResponse()
            ->setUseOauth(false)
            ->put("posts/$id", $params);
        $this->assertJson($response);
        $this->assertEquals($params, $this->httpClient->getLastRequestParams());
    }

    public function testPutWithRawResponseWithAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useRawResponse()
            ->setAccessToken("123456")
            ->put("posts/$id", $params);
        $this->assertJson($response);
        $this->assertEquals(
            array_merge($params, ['access_token' => $this->httpClient->getAccessToken()]),
            $this->httpClient->getLastRequestParams()
        );
    }

    public function testPutWithJsonResponseWithoutAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useJsonResponse()
            ->setUseOauth(false)
            ->put("posts/$id", $params);
        $this->assertEquals(['id' => $id], $response);
        $this->assertEquals($params, $this->httpClient->getLastRequestParams());
    }

    public function testPutWithJsonResponseWithAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useJsonResponse()
            ->setAccessToken("123456")
            ->put("posts/$id", $params);
        $this->assertEquals(['id' => $id], $response);
        $this->assertEquals(
            array_merge($params, ['access_token' => $this->httpClient->getAccessToken()]),
            $this->httpClient->getLastRequestParams()
        );
    }

    public function testDeleteWithRawResponseWithoutAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useRawResponse()
            ->setUseOauth(false)
            ->delete("posts/$id", $params);
        $this->assertJson($response);
        $this->assertEquals($params, $this->httpClient->getLastRequestParams());
    }

    public function testDeleteWithRawResponseWithAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useRawResponse()
            ->setAccessToken("123456")
            ->delete("posts/$id", $params);
        $this->assertJson($response);
        $this->assertEquals(
            array_merge($params, ['access_token' => $this->httpClient->getAccessToken()]),
            $this->httpClient->getLastRequestParams()
        );
    }

    public function testDeleteWithJsonResponseWithoutAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useJsonResponse()
            ->setUseOauth(false)
            ->delete("posts/$id", $params);
        $this->assertEquals([], $response);
        $this->assertEquals($params, $this->httpClient->getLastRequestParams());
    }

    public function testDeleteWithJsonResponseWithAccessToken()
    {
        $id = 1;
        $params = ['title' => 'test', 'author' => 'test'];
        $response = $this->httpClient
            ->useJsonResponse()
            ->setAccessToken("123456")
            ->delete("posts/$id", $params);
        $this->assertEquals([], $response);
        $this->assertEquals(
            array_merge($params, ['access_token' => $this->httpClient->getAccessToken()]),
            $this->httpClient->getLastRequestParams()
        );
    }
}
