<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\OauthClient\Exceptions;

use yii\base\Exception;

/**
 * Class Oauth2ClientException
 * @package app\libs
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class Oauth2ClientException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName(): string
    {
        return 'Oauth2ClientException';
    }
}
