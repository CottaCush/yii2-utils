<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\HttpClient;

use CottaCush\Yii2\HttpClient\Exceptions\HttpClientException;
use CottaCush\Yii2\HttpClient\Messages\HttpClientErrorMessages;
use Exception;
use linslin\yii2\curl\Curl;

abstract class BaseHttpClient implements HttpClientInterface
{
    protected $baseUrl;
    protected $rawResponse = false;
    protected $lastRequestUrl;
    protected $lastRequestParams;
    /** @var $curlAgent Curl */
    protected $curlAgent;

    public function __construct($baseUrl)
    {
        $this->init($baseUrl);
    }

    abstract protected function init($baseUrl);

    abstract protected function buildUrl($url, $params = []);

    abstract protected function filterParams($params);

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function useRawResponse(): self
    {
        $this->rawResponse = true;
        return $this;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function useJsonResponse(): self
    {
        $this->rawResponse = false;
        return $this;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return boolean
     */
    public function isRawResponse(): bool
    {
        return $this->rawResponse;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getBaseUrl(): mixed
    {
        return $this->baseUrl;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return Curl
     */
    public function getCurlAgent(): Curl
    {
        return $this->curlAgent;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getLastRequestUrl(): mixed
    {
        return $this->lastRequestUrl;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getLastRequestParams(): mixed
    {
        return $this->lastRequestParams;
    }

    /**
     * Performs GET HTTP request
     * @param $url
     * @param array $params
     * @return mixed
     * @throws HttpClientException
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function get($url, $params = []): mixed
    {
        if (!is_array($params)) {
            throw new HttpClientException(HttpClientErrorMessages::INVALID_QUERY_PARAMS);
        }
        $params = $this->filterParams($params);
        $this->lastRequestParams = $params;
        $this->lastRequestUrl = $this->buildUrl($url, $params);
        return $this->curlAgent->get($this->lastRequestUrl, $this->rawResponse);
    }

    /**
     * Performs POST HTTP request
     * @param $url
     * @param $params
     * @return mixed
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function post($url, $params): mixed
    {
        if (is_array($params)) {
            $params = $this->filterParams($params);
        }
        $this->lastRequestParams = $params;
        $this->lastRequestUrl = $this->buildUrl($url);
        return $this->curlAgent->setOption(CURLOPT_POSTFIELDS, $params)
            ->post($this->lastRequestUrl, $this->rawResponse);
    }

    /**
     * Performs PUT HTTP request
     * @param $url
     * @param $params
     * @return mixed
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function put($url, $params): mixed
    {
        if (is_array($params)) {
            $params = $this->filterParams($params);
        }
        $this->lastRequestParams = $params;
        $this->lastRequestUrl = $this->buildUrl($url);
        return $this->curlAgent->setOption(CURLOPT_POSTFIELDS, $params)
            ->put($this->lastRequestUrl, $this->rawResponse);
    }

    /**
     * Performs DELETE HTTP request
     * @param $url
     * @param $params
     * @return mixed
     * @throws Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function delete($url, $params): mixed
    {
        if (is_array($params)) {
            $params = $this->filterParams($params);
        }
        $this->lastRequestParams = $params;
        $this->lastRequestUrl = $this->buildUrl($url);
        return $this->curlAgent->setOption(CURLOPT_POSTFIELDS, $params)
            ->delete($this->lastRequestUrl, $this->rawResponse);
    }
}
