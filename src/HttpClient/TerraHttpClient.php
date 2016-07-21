<?php
namespace CottaCush\Yii2\HttpClient;

use CottaCush\Yii2\HttpClient\Exceptions\HttpClientException;
use CottaCush\Yii2\HttpClient\Messages\HttpClientErrorMessages;
use linslin\yii2\curl\Curl;

/**
 * Class HttpClient
 * @package app\libs
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class TerraHttpClient extends BaseHttpClient
{
    protected $accessToken;
    protected $useOauth = true;

    protected function init($baseUrl)
    {
        if (filter_var($baseUrl, FILTER_VALIDATE_URL) === false) {
            throw new HttpClientException(HttpClientErrorMessages::INVALID_BASE_URL);
        }

        $this->baseUrl = $baseUrl;
        $slash = substr($this->baseUrl, (strlen($this->baseUrl) - 1), 1);
        if ($slash !== '/') { // Add slash to base url
            $this->baseUrl = $baseUrl . '/';
        }
        $this->curlAgent = new Curl();
    }

    /**
     * Builds request URL
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $url
     * @param array $params
     * @return string
     * @throws HttpClientException
     */
    protected function buildUrl($url, $params = [])
    {
        if ($this->useOauth) {
            $params['access_token'] = $this->getAccessToken();
        }
        $params = http_build_query($params);
        return $this->baseUrl . $url . '?' . $params;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param mixed $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return boolean
     */
    public function isUseOauth()
    {
        return $this->useOauth;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param boolean $useOauth
     * @return $this
     */
    public function setUseOauth($useOauth)
    {
        $this->useOauth = $useOauth;
        return $this;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $params
     * @return mixed
     */
    protected function filterParams($params)
    {
        return array_filter($params, 'strlen');
    }
}
