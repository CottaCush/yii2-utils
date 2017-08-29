<?php

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\File\ObjectStorageComponent;
use CottaCush\Yii2\File\ObjectStorageException;
use Yii;
use yii\base\Action;
use yii\web\UploadedFile;

/**
 * Class ObjectStorageUploadAction
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package CottaCush\Yii2\Action
 */
class ObjectStorageUploadAction extends Action
{
    public $uploadedFileName;
    public $objectStorageComponentName = 'objectStorage';
    public $noImageUploadedMessage = 'No image uploaded';
    public $errorMessageCode = 'error';
    public $successUrlKey = 'url';
    public $objectStorageFileName = null;
    public $objectStoragePath = null;

    public function run()
    {
        $uploadedFile = UploadedFile::getInstanceByName($this->uploadedFileName);
        if (!$uploadedFile) {
            return $this->controller->sendErrorResponse($this->noImageUploadedMessage, $this->errorMessageCode, 400);
        }

        /** @var ObjectStorageComponent $objectStorage */
        $objectStorage = Yii::$app->get($this->objectStorageComponentName);

        try {
            $url = $objectStorage->storeUploadedFile(
                $uploadedFile,
                $this->objectStorageFileName,
                $this->objectStoragePath
            );
            return $this->controller->sendSuccessResponse([$this->successUrlKey => $url]);
        } catch (ObjectStorageException $objectStorageException) {
            return $this->controller->
            sendErrorResponse($objectStorageException->getMessage(), $this->errorMessageCode, 500);
        }
    }
}
