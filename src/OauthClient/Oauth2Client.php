<?php

namespace CottaCush\Yii2\OauthClient;

use CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException;
use linslin\yii2\curl\Curl;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
use yii\helpers\ArrayHelper;

/**
 * Class Oauth2Client
 * @property Curl $curl
 * @package app\libs
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class Oauth2Client
{
    const DEFAULT_ERROR = 'An error occurred';
    const CODE_NOT_SET = 'Code not set';
    const INVALID_AUTH_URL = 'Invalid auth url %s';
    const INVALID_TOKEN_URL = 'Invalid token url %s';
    const INVALID_CLIENT_ID = 'Invalid client ID';
    const INVALID_CLIENT_SECRET = 'Invalid client secret';
    protected $clientId;
    protected $clientSecret;
    protected $authUrl;
    protected $tokenUrl;
    /**
     * @var OAuth2 $oauth2
     */
    protected $oauth2;
    /**
     * @var Curl $curl
     */
    protected $curl;

    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    const RESPONSE_TYPE_CODE = 'code';
    const STATE_ALIVE = 'alive';

    /**
     * Oauth2Client constructor.
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->setDefaultParams($params);
        $this->oauth2 = new OAuth2();
        $this->curl = new Curl();
    }


    /**
     * Handles authorize request response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $response
     * @return mixed
     * @throws Oauth2ClientException
     */
    private function handleAuthorizeResponse($response)
    {
        $status = ArrayHelper::getValue($response, 'status');

        if (!is_null($status) && $status == 'success') {
            $code = ArrayHelper::getValue($response, 'data.code');
            if (is_null($code)) {
                throw new Oauth2ClientException(self::CODE_NOT_SET);
            }
            return $code;
        } else {
            $message = ArrayHelper::getValue($response, 'message', self::DEFAULT_ERROR);
            throw new Oauth2ClientException($message);
        }
    }

    /**
     * Authorizes and returns authorization code
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed code
     * @throws Oauth2ClientException
     */
    public function authorize()
    {
        $this->validateAuthParams();
        $response = $this->curl->setOption(
            CURLOPT_POSTFIELDS,
            http_build_query(array(
                'grant_type' => self::GRANT_TYPE_AUTHORIZATION_CODE,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'response_type' => self::RESPONSE_TYPE_CODE,
                'state' => self::STATE_ALIVE
            ))
        )->post($this->authUrl, false);
        return $this->handleAuthorizeResponse($response);
    }


    /**
     * Handles token request response
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param OAuthToken $response
     * @return mixed
     * @throws Oauth2ClientException
     */
    private function handleTokenResponse($response)
    {
        $params = $response->getParams();
        $status = ArrayHelper::getValue($params, 'status');
        if (!is_null($status) && $status == 'success') {
            $token = ArrayHelper::getValue($params, 'data');
            if (is_null($token)) {
                throw new Oauth2ClientException(self::CODE_NOT_SET);
            }
            return $token;
        } else {
            $message = ArrayHelper::getValue($params, 'message', self::DEFAULT_ERROR);
            throw new Oauth2ClientException($message);
        }
    }

    /**
     * Fetches the access token using the authorization code
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $code
     * @return mixed Access token
     * @throws Oauth2ClientException
     */
    public function fetchAccessToken($code)
    {
        $this->validateTokenParams();

        $this->oauth2->tokenUrl = $this->tokenUrl;
        $this->oauth2->clientId = $this->clientId;
        $this->oauth2->clientSecret = $this->clientSecret;

        $response = $this->oauth2->fetchAccessToken($code);

        return $this->handleTokenResponse($response);
    }

    /**
     * Validates params for the authorization request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return bool
     * @throws Oauth2ClientException
     */
    protected function validateAuthParams()
    {
        if (empty($this->authUrl) || filter_var($this->authUrl, FILTER_VALIDATE_URL) === false) {
            throw new Oauth2ClientException(sprintf(self::INVALID_AUTH_URL, $this->authUrl));
        }

        if (empty($this->clientId)) {
            throw new Oauth2ClientException(self::INVALID_CLIENT_ID);
        }

        if (empty($this->clientSecret)) {
            throw new Oauth2ClientException(self::INVALID_CLIENT_SECRET);
        }

        return true;
    }

    /**
     * Validates params for the token request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return bool
     * @throws Oauth2ClientException
     */
    protected function validateTokenParams()
    {
        if (empty($this->tokenUrl) || filter_var($this->tokenUrl, FILTER_VALIDATE_URL) === false) {
            throw new Oauth2ClientException(sprintf(self::INVALID_TOKEN_URL, $this->tokenUrl));
        }

        if (empty($this->clientId)) {
            throw new Oauth2ClientException(self::INVALID_CLIENT_ID);
        }

        if (empty($this->clientSecret)) {
            throw new Oauth2ClientException(self::INVALID_CLIENT_SECRET);
        }

        return true;
    }

    /**
     *
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $params
     */
    private function setDefaultParams($params)
    {
        $this->authUrl = ArrayHelper::getValue($params, 'authUrl');
        $this->tokenUrl = ArrayHelper::getValue($params, 'tokenUrl');
        $this->clientId = ArrayHelper::getValue($params, 'clientId');
        $this->clientSecret = ArrayHelper::getValue($params, 'clientSecret');
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param mixed $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param mixed $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param mixed $authUrl
     */
    public function setAuthUrl($authUrl)
    {
        $this->authUrl = $authUrl;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getTokenUrl()
    {
        return $this->tokenUrl;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param mixed $tokenUrl
     */
    public function setTokenUrl($tokenUrl)
    {
        $this->tokenUrl = $tokenUrl;
    }
}
