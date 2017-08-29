<?php

namespace CottaCush\Yii2\File;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\web\UploadedFile;

/**
 * Class ObjectStorageComponent
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package CottaCush\Yii2\File
 */
class ObjectStorageComponent extends Component
{

    /** @var  ObjectStorageClientInterface */
    protected $objectStorageClient;

    public $objectStorageClass;

    public function init()
    {
        if (!($this->objectStorageClass instanceof ObjectStorageClientInterface)) {
            throw new InvalidConfigException(
                'Object Storage Class must be an instance of ' . ObjectStorageClientInterface::class
            );
        }

        $this->objectStorageClient = new $this->objectStorageClass();
    }

    /**
     * Store resource and get URL
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $resource
     * @param null $fileName
     * @param null $path
     * @return mixed
     */
    public function storeResource($resource, $fileName = null, $path = null)
    {
        if (!$fileName) {
            $fileName = uniqid() . time();
        }

        $fileName = $this->normalizeFileName($fileName, $path);
        return $this->objectStorageClient->put($resource, $fileName);
    }

    /**
     * Store Yii2 UploadedFile and get URL
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param UploadedFile $file
     * @param null $fileName
     * @param null $path
     * @return mixed
     */
    public function storeUploadedFile(UploadedFile $file, $fileName = null, $path = null)
    {
        if (!$fileName) {
            $fileName = uniqid() . time() . '.' . $file->extension;
        }

        $basename = basename($fileName);
        return $this->storeFile($file->tempName, $basename, $path);
    }

    /**
     * Store File and get URL
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $file
     * @param $fileName
     * @param null $path
     * @return mixed
     */
    public function storeFile($file, $fileName, $path = null)
    {
        $resource = fopen($file, 'r+');
        return $this->storeResource($resource, $fileName, $path);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $fileName
     * @param $path
     * @return string
     */
    private function normalizeFileName($fileName, $path)
    {
        return $fileName = ($path) ? $path . '/' . $fileName : $fileName;
    }
}
