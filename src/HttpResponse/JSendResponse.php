<?php
namespace CottaCush\Yii2\HttpResponse;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class JSendResponseHandler
 * @package app\libs
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class JSendResponseHandler extends BaseResponse
{

    const RESPONSE_STATUS_PARAM = 'status';
    const RESPONSE_DATA_PARAM = 'data';
    const RESPONSE_STATUS_SUCCESS = 'success';
    const RESPONSE_STATUS_OK = 'OK';
    const CODE_NO_CODE = '000';
    const ERROR_MESSAGE_AN_UNEXPECTED_ERROR_OCCURRED = 'An unexpected error occurred';

    protected $rawResponse;
    protected $responseParsed = false;

    public function __construct($response)
    {
        parent::__construct($response);
        if ($this->responseParsed) {
            $this->parsedResponse = Json::decode($response);
        }
    }

    /**
     * Get's the data from the response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param null $field
     * @param string $dataKey
     * @return mixed
     */
    public function getData($field = null, $dataKey = 'data')
    {
        if (is_null($field)) {
            $field = $dataKey;
        } else {
            $field = $dataKey . '.' . $field;
        }

        return ArrayHelper::getValue($this->rawResponse, $field);
    }

    /**
     * Get's the error message from the response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getErrorMessage()
    {
        return ArrayHelper::getValue($this->rawResponse, 'message', self::ERROR_MESSAGE_AN_UNEXPECTED_ERROR_OCCURRED);
    }

    /**
     * Get's the response code from the response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getCode()
    {
        return ArrayHelper::getValue($this->rawResponse, 'code', self::CODE_NO_CODE);
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
}
