<?php
namespace CottaCush\Yii2\HttpResponse;

use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class JSendResponse
 * @package app\libs
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class JSendResponse extends BaseResponse
{
    const RESPONSE_STATUS_PARAM = 'status';
    const RESPONSE_DATA_PARAM = 'data';
    const RESPONSE_CODE_PARAM = 'code';
    const RESPONSE_MESSAGE_PARAM = 'message';
    const RESPONSE_STATUS_SUCCESS = 'success';
    const RESPONSE_STATUS_OK = 'OK';
    const CODE_NO_CODE = '000';
    const ERROR_MESSAGE_AN_UNEXPECTED_ERROR_OCCURRED = 'An unexpected error occurred';

    protected $rawResponse;
    protected $responseParsed = false;

    public function __construct($response, $responseParsed = true)
    {
        parent::__construct($response, $responseParsed);
        if (!$this->responseParsed) {
            $this->parsedResponse = Json::decode($response);
        }
    }

    /**
     * Gets the data from the response
     * @param null $defaultValue
     * @return mixed
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function getData($defaultValue = null): mixed
    {
        return ArrayHelper::getValue($this->parsedResponse, self::RESPONSE_DATA_PARAM, $defaultValue);
    }

    /**
     * Gets the error message from the response
     * @return mixed
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function getErrorMessage(): mixed
    {
        return ArrayHelper::getValue(
            $this->parsedResponse,
            self::RESPONSE_MESSAGE_PARAM,
            self::ERROR_MESSAGE_AN_UNEXPECTED_ERROR_OCCURRED
        );
    }

    /**
     * Gets the response code from the response
     * @return mixed
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function getCode(): mixed
    {
        return ArrayHelper::getValue($this->parsedResponse, self::RESPONSE_CODE_PARAM, self::CODE_NO_CODE);
    }

    /**
     * Gets the value of key if key exists, default value otherwise
     * @param $key
     * @param null $default
     * @return mixed
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function get($key, $default = null): mixed
    {
        return ArrayHelper::getValue(
            $this->parsedResponse,
            sprintf('%s.%s', self::RESPONSE_DATA_PARAM, $key),
            $default
        );
    }

    /**
     * Returns the Status parameter
     * @return string
     *@author Adegoke Obasa <goke@cottacush.com>
     */
    public function getStatusParam(): string
    {
        return self::RESPONSE_STATUS_PARAM;
    }

    /**
     * Returns the value for a success status
     * @return string
     *@author Adegoke Obasa <goke@cottacush.com>
     */
    public function getSuccessValue(): string
    {
        return self::RESPONSE_STATUS_SUCCESS;
    }

    /**
     * Gets the code parameter
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function getCodeParam(): string
    {
        return self::RESPONSE_CODE_PARAM;
    }

    /**
     * Gets the message parameter
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function getMessageParam(): string
    {
        return self::RESPONSE_MESSAGE_PARAM;
    }
}
