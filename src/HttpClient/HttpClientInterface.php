<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\HttpClient;

interface HttpClientInterface
{
    public function get($url, $params);

    public function post($url, $params);

    public function put($url, $params);

    public function delete($url, $params);
}
