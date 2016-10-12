<?php

namespace CottaCush\Yii2\HttpResponse;

use yii\helpers\ArrayHelper;

/**
 * Class BaseResponse
 * @package app\libs
 * @author Adegoke Obasa <goke@cottacush.com>
 */
abstract class BaseResponse
{
    protected $rawResponse;
    protected $parsedResponse;
    /**
     * @var bool Whether response has already been parsed or not
     */
    protected $responseParsed;

    /**
     * BaseResponse constructor.
     * @param $response
     * @param bool $responseParsed
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function __construct($response, $responseParsed = true)
    {
        $this->rawResponse = $this->parsedResponse = $response;
        $this->responseParsed = $responseParsed;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getParsedResponse()
    {
        return $this->parsedResponse;
    }

    /**
     * Returns true if response is a success response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function isSuccess()
    {
        return ArrayHelper::getValue($this->parsedResponse, $this->getStatusParam()) == $this->getSuccessValue();
    }

    /**
     * Gets the value of key if key exists, default value otherwise
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $key
     * @param null $default
     * @return mixed
     */
    abstract public function get($key, $default = null);

    /**
     * Returns the Status parameter
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    abstract public function getStatusParam();

    /**
     * Returns the value for a success status
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    abstract public function getSuccessValue();

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return boolean
     */
    public function isResponseParsed()
    {
        return $this->responseParsed;
    }
}
