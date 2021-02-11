<?php
namespace CottaCush\Yii2\Tests\HttpResponse;

use CottaCush\Yii2\HttpResponse\JSendResponse;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

/**
 * Class JSendResponseTest
 * @package CottaCush\Yii2\Tests\HttpResponse
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class JSendResponseTest extends TestCase
{
    /** @var Generator $faker */
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function testGetResponseParsedIsFalseByDefault()
    {
        $jsonResponse = new JSendResponse([]);
        $this->assertEquals($jsonResponse->isResponseParsed(), true);
    }

    public function testGetResponseParsedIsActualValueSet()
    {
        $jsonResponse = new JSendResponse(json_encode([]), false);
        $this->assertEquals($jsonResponse->isResponseParsed(), false);
    }

    public function testResponseRawResponseIsSet()
    {
        $words = $this->faker->words;

        $rawResponse = json_encode($words);
        $jsonResponse = new JSendResponse(json_encode($words));
        $this->assertEquals($jsonResponse->getRawResponse(), $rawResponse);
    }

    public function testResponseParsedResponseIsSetWhenNotParsed()
    {
        $words = $this->faker->words;

        $rawResponse = json_encode($words);
        $jsonResponse = new JSendResponse(json_encode($words));
        $this->assertEquals($jsonResponse->getParsedResponse(), $rawResponse);
    }

    public function testResponseParsedResponseIsSetWhenParsed()
    {
        $words = $this->faker->words;

        $jsonResponse = new JSendResponse(json_encode($words), false);
        $this->assertEquals($jsonResponse->getParsedResponse(), $words);
    }

    public function testStatusParamIsCorrect()
    {
        $jsonResponse = new JSendResponse(json_encode([]), false);
        $this->assertEquals($jsonResponse->getStatusParam(), JSendResponse::RESPONSE_STATUS_PARAM);
    }

    public function testCodeParamIsCorrect()
    {
        $jsonResponse = new JSendResponse(json_encode([]), false);
        $this->assertEquals($jsonResponse->getCodeParam(), JSendResponse::RESPONSE_CODE_PARAM);
    }

    public function testMessageParamIsCorrect()
    {
        $jsonResponse = new JSendResponse(json_encode([]), false);
        $this->assertEquals($jsonResponse->getMessageParam(), JSendResponse::RESPONSE_MESSAGE_PARAM);
    }

    public function testSuccessValueIsCorrect()
    {
        $jsonResponse = new JSendResponse(json_encode([]), false);
        $this->assertEquals($jsonResponse->getSuccessValue(), JSendResponse::RESPONSE_STATUS_SUCCESS);
    }

    public function testIsSuccessReturnsTrueWhenStatusIsSuccessWhenResponseIsParsed()
    {
        $jsonResponse = new JSendResponse(json_encode(['status' => JSendResponse::RESPONSE_STATUS_SUCCESS]), false);
        $this->assertEquals($jsonResponse->isSuccess(), true);
    }

    public function testIsSuccessReturnsTrueWhenStatusIsSuccessWhenResponseNotParsed()
    {
        $jsonResponse = new JSendResponse(['status' => JSendResponse::RESPONSE_STATUS_SUCCESS]);
        $this->assertEquals($jsonResponse->isSuccess(), true);
    }

    public function testIsSuccessReturnsFalseWhenStatusIsSuccessWhenResponseIsParsed()
    {
        $jsonResponse = new JSendResponse(json_encode(['status' => 'error']), false);
        $this->assertEquals($jsonResponse->isSuccess(), false);
    }

    public function testIsSuccessReturnsFalseWhenStatusIsSuccessWhenResponseNotParsed()
    {
        $jsonResponse = new JSendResponse(['status' => 'error']);
        $this->assertEquals($jsonResponse->isSuccess(), false);
    }

    public function testGetErrorMessageWhenResponseIsNotParsed()
    {
        $message = $this->faker->sentence;
        $jsonResponse = new JSendResponse(['status' => 'error', 'message' => $message]);
        $this->assertEquals($jsonResponse->getErrorMessage(), $message);
    }

    public function testGetErrorMessageWhenResponseIsParsed()
    {
        $message = $this->faker->sentence;
        $jsonResponse = new JSendResponse(json_encode(['status' => 'error', 'message' => $message]), false);
        $this->assertEquals($jsonResponse->getErrorMessage(), $message);
    }

    public function testGetCodeWhenResponseIsNotParsed()
    {
        $code = $this->faker->randomDigitNotNull;
        $jsonResponse = new JSendResponse(['status' => 'error', 'code' => $code]);
        $this->assertEquals($jsonResponse->getCode(), $code);
    }

    public function testGetCodeWhenResponseIsParsed()
    {
        $code = $this->faker->randomDigitNotNull;
        $jsonResponse = new JSendResponse(json_encode(['status' => 'error', 'code' => $code]), false);
        $this->assertEquals($jsonResponse->getCode(), $code);
    }

    public function testGetCodeWhenNotSetAndResponseIsNotParsed()
    {
        $jsonResponse = new JSendResponse(['status' => 'error']);
        $this->assertEquals($jsonResponse->getCode(), JSendResponse::CODE_NO_CODE);
    }

    public function testGetCodeWhenSetAndResponseIsParsed()
    {
        $jsonResponse = new JSendResponse(json_encode(['status' => 'error']), false);
        $this->assertEquals($jsonResponse->getCode(), JSendResponse::CODE_NO_CODE);
    }

    public function testGetDataWhenResponseIsNotParsed()
    {
        $words = $this->faker->words;
        $jsonResponse = new JSendResponse(['status' => 'success', 'data' => $words]);
        $this->assertEquals($jsonResponse->getData(), $words);
    }

    public function testGetDataWhenResponseIsParsed()
    {
        $words = $this->faker->words;
        $jsonResponse = new JSendResponse(json_encode(['status' => 'success', 'data' => $words]), false);
        $this->assertEquals($jsonResponse->getData(), $words);
    }

    public function testGetDataWhenNotSetAndResponseIsNotParsed()
    {
        $jsonResponse = new JSendResponse(['status' => 'success']);
        $this->assertEquals($jsonResponse->getData([]), []);
    }

    public function testGetDataWhenNotSetAndResponseIsParsed()
    {
        $jsonResponse = new JSendResponse(json_encode(['status' => 'success']), false);
        $this->assertEquals($jsonResponse->getData([]), []);
    }

    public function testGetWhenResponseIsNotParsed()
    {
        $words = $this->faker->words;
        $jsonResponse = new JSendResponse(['status' => 'success', 'data' => compact('words')]);
        $this->assertEquals($jsonResponse->get('words'), $words);
    }

    public function testGetWhenResponseIsParsed()
    {
        $words = $this->faker->words;
        $jsonResponse = new JSendResponse(json_encode(['status' => 'success', 'data' => compact('words')]), false);
        $this->assertEquals($jsonResponse->get('words'), $words);
    }

    public function testGetWhenNotSetAndResponseIsNotParsed()
    {
        $jsonResponse = new JSendResponse(['status' => 'success']);
        $this->assertEquals($jsonResponse->get('words', []), []);
    }

    public function testGetWhenNotSetAndResponseIsParsed()
    {
        $jsonResponse = new JSendResponse(json_encode(['status' => 'success']), false);
        $this->assertEquals($jsonResponse->get('words', []), []);
    }
}
