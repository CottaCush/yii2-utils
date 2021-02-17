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
    protected bool $useOauth = true;

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
     * @param $url
     * @param array $params
     * @return string
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    protected function buildUrl($url, $params = []): string
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
    public function getAccessToken(): mixed
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     * @return $this
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function setAccessToken(mixed $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return boolean
     */
    public function isUseOauth(): bool
    {
        return $this->useOauth;
    }

    /**
     * @param boolean $useOauth
     * @return $this
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function setUseOauth(bool $useOauth): self
    {
        $this->useOauth = $useOauth;
        return $this;
    }

    /**
     * @param $params
     * @return array
     *@author Adegoke Obasa <goke@cottacush.com>
     */
    protected function filterParams($params): array
    {
        return array_filter($params, function ($item) {
            return is_array($item) || is_object($item) || strlen($item);
        });
    }
}
