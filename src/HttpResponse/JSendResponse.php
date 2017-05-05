<?php
namespace CottaCush\Yii2\HttpResponse;

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
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param null $defaultValue
     * @return mixed
     */
    public function getData($defaultValue = null)
    {
        return ArrayHelper::getValue($this->parsedResponse, self::RESPONSE_DATA_PARAM, $defaultValue);
    }

    /**
     * Gets the error message from the response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getErrorMessage()
    {
        return ArrayHelper::getValue(
            $this->parsedResponse,
            self::RESPONSE_MESSAGE_PARAM,
            self::ERROR_MESSAGE_AN_UNEXPECTED_ERROR_OCCURRED
        );
    }

    /**
     * Gets the response code from the response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getCode()
    {
        return ArrayHelper::getValue($this->parsedResponse, self::RESPONSE_CODE_PARAM, self::CODE_NO_CODE);
    }

    /**
     * Gets the value of key if key exists, default value otherwise
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return ArrayHelper::getValue(
            $this->parsedResponse,
            sprintf('%s.%s', self::RESPONSE_DATA_PARAM, $key),
            $default
        );
    }

    /**
     * Returns the Status parameter
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getStatusParam()
    {
        return self::RESPONSE_STATUS_PARAM;
    }

    /**
     * Returns the value for a success status
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getSuccessValue()
    {
        return self::RESPONSE_STATUS_SUCCESS;
    }

    /**
     * Gets the code parameter
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function getCodeParam()
    {
        return self::RESPONSE_CODE_PARAM;
    }

    /**
     * Gets the message parameter
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function getMessageParam()
    {
        return self::RESPONSE_MESSAGE_PARAM;
    }

    /**
     * Checks if the response is a success response
     * @author Olawale Lawal <wale@cottacush.com>
     * @param string $successStatus
     * @return bool
     */
    public function isSuccess($successStatus = self::RESPONSE_STATUS_SUCCESS)
    {
        $status = ArrayHelper::getValue($this->rawResponse, self::RESPONSE_STATUS_PARAM);

        return $status === $successStatus;
    }
}
