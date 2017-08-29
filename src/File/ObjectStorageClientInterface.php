<?php

namespace CottaCush\Yii2\File;

/**
 * Class ObjectStorageClientInterface
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package CottaCush\Yii2\File
 */
interface ObjectStorageClientInterface
{
    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $resource
     * @param $fileName
     * @return mixed URL
     * @throws ObjectStorageException
     */
    public function put($resource, $fileName);
}
