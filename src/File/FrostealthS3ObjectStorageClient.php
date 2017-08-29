<?php

namespace CottaCush\Yii2\File;

use Aws\S3\Exception\S3Exception;
use frostealth\yii2\aws\s3\Storage;
use Yii;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * Class S3ObjectStorageClient
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package CottaCush\Yii2\File
 */
class FrostealthS3ObjectStorageClient implements ObjectStorageClientInterface
{
    public function put($resource, $fileName)
    {
        try {
            /** @var Storage $s3Bucket */
            $s3Bucket = Yii::$app->get('s3bucket');
            $result = $s3Bucket->put($fileName, $resource);
            $mediaUrl = ArrayHelper::getValue($result->toArray(), 'ObjectURL');
            return $mediaUrl;
        } catch (S3Exception $ex) {
            Yii::$app->log->logger->log($ex->getMessage(), Logger::LEVEL_ERROR);
            throw new ObjectStorageException($ex->getMessage(), 0, $ex);
        }
    }
}
