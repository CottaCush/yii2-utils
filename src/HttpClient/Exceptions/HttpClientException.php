<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\HttpClient\Exceptions;

use yii\base\Exception;

/**
 * Class HttpClientException
 * @package CottaCush\Yii2\HttpClient\Exceptions
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class HttpClientException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName(): string
    {
        return 'HttpClientException';
    }
}
