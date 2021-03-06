<?php

namespace CottaCush\Yii2\OauthClient;

use CottaCush\Yii2\OauthClient\Exceptions\Oauth2ClientException;
use linslin\yii2\curl\Curl;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
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
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    const RESPONSE_TYPE_CODE = 'code';
    const STATE_ALIVE = 'alive';
    const AUTH_URL = 'authUrl';
    const TOKEN_URL = 'tokenUrl';
    const CLIENT_ID = 'clientId';
    const CLIENT_SECRET = 'clientSecret';
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';

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

    /**
     * Oauth2Client constructor.
     * @param array $params
     * @throws \Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function __construct($params = [])
    {
        $this->setDefaultParams($params);
        $this->oauth2 = new OAuth2();
        $this->curl = new Curl();
    }

    /**
     * Handles authorize request response
     * @param $response
     * @return mixed
     * @throws Oauth2ClientException
     * @throws \Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    private function handleAuthorizeResponse($response): mixed
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
     * @return mixed code
     * @throws Oauth2ClientException
     * @throws InvalidArgumentException
     * @throws \Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function authorize(): mixed
    {
        $this->validateAuthParams();
        try {
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
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new Oauth2ClientException($invalidArgumentException->getMessage());
        }
        return $this->handleAuthorizeResponse($response);
    }

    /**
     * @return mixed
     * @throws Oauth2ClientException
     * @throws \Exception
     * @author Akinwunmi Taiwo <taiwo@cottacush.com>
     */
    public function fetchAccessTokenWithClientCredentials(): mixed
    {
        $this->validateTokenParams();
        try {
            $response = $this->curl->setOption(
                CURLOPT_POSTFIELDS,
                http_build_query(array(
                    'grant_type' => self::GRANT_TYPE_CLIENT_CREDENTIALS,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret
                ))
            )->post($this->tokenUrl, false);
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new Oauth2ClientException($invalidArgumentException->getMessage());
        }

        return $this->handleTokenResponse($response);
    }

    /**
     * Handles token request response
     * @param OAuthToken $response
     * @return mixed
     * @throws Oauth2ClientException
     * @throws \Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    private function handleTokenResponse($response): mixed
    {
        $params = ($response instanceof OAuthToken) ? $response->getParams() : $response;
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
    public function fetchAccessToken($code): mixed
    {
        $this->validateTokenParams();

        $this->oauth2->tokenUrl = $this->tokenUrl;
        $this->oauth2->clientId = $this->clientId;
        $this->oauth2->clientSecret = $this->clientSecret;

        try {
            $response = $this->oauth2->fetchAccessToken($code);
        } catch (Exception $ex) {
            throw new Oauth2ClientException($ex->getMessage());
        }
        return $this->handleTokenResponse($response);
    }

    /**
     * Validates params for the authorization request
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return bool
     * @throws Oauth2ClientException
     */
    protected function validateAuthParams(): bool
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
    protected function validateTokenParams(): bool
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
     * @param $params
     * @throws \Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    private function setDefaultParams($params)
    {
        $this->authUrl = ArrayHelper::getValue($params, self::AUTH_URL);
        $this->tokenUrl = ArrayHelper::getValue($params, self::TOKEN_URL);
        $this->clientId = ArrayHelper::getValue($params, self::CLIENT_ID);
        $this->clientSecret = ArrayHelper::getValue($params, self::CLIENT_SECRET);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getClientId(): mixed
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function setClientId(mixed $clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getClientSecret(): mixed
    {
        return $this->clientSecret;
    }

    /**
     * @param mixed $clientSecret
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function setClientSecret(mixed $clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getAuthUrl(): mixed
    {
        return $this->authUrl;
    }

    /**
     * @param mixed $authUrl
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function setAuthUrl(mixed $authUrl)
    {
        $this->authUrl = $authUrl;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getTokenUrl(): mixed
    {
        return $this->tokenUrl;
    }

    /**
     * @param mixed $tokenUrl
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function setTokenUrl(mixed $tokenUrl)
    {
        $this->tokenUrl = $tokenUrl;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return OAuth2
     */
    public function getOauth2(): OAuth2
    {
        return $this->oauth2;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return Curl
     */
    public function getCurl(): Curl
    {
        return $this->curl;
    }

    /**
     * Returns the access token
     * @return mixed
     * @throws Oauth2ClientException
     * @throws \Exception
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public static function getAccessToken(): mixed
    {
        $oauthClientParams = ArrayHelper::getValue(\Yii::$app->params, 'oauth');
        $oauthClient = new Oauth2Client($oauthClientParams);
        $code = $oauthClient->authorize();
        $token = $oauthClient->fetchAccessToken($code);
        return ArrayHelper::getValue($token, 'access_token');
    }
}
