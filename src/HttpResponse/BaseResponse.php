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
    protected $responseParsed = true;

    /**
     * BaseResponse constructor.
     * @param $response
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function __construct($response)
    {
        $this->rawResponse = $this->parsedResponse = $response;
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
     * @return \SimpleXMLElement
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
     * @param boolean $responseParsed
     * @return BaseResponse
     */
    public function setResponseParsed($responseParsed)
    {
        $this->responseParsed = $responseParsed;
        return $this;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return boolean
     */
    public function isResponseParsed()
    {
        return $this->responseParsed;
    }
}
