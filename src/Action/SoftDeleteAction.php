<?php

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Constants\Messages;
use CottaCush\Yii2\Controller\BaseController;
use CottaCush\Yii2\Model\BaseModel;
use yii\base\ExitException;
use yii\db\IntegrityException;
use yii\web\Response;

class SoftDeleteAction extends DeleteAction
{
    const FIELD_NAME = 'name';
    public $relatedRecords = [];
    public $modelId;

    public $fieldToModify;
    public $integrityViolationMessage = Messages::RECORD_USED_ALREADY;

    /**
     * @return Response
     * @throws ExitException
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function run(): Response
    {
        /** @var BaseController $controller */
        $controller = $this->controller;

        $referrerUrl = $controller->getRequest()->referrer;
        $controller->isPostCheck($referrerUrl);

        /** @var BaseModel model */
        $model = new $this->model;
        $this->model = $model::fetchWithRelatedRecords($this->modelId, $this->relatedRecords);

        if (!$this->model) {
            return $controller->returnError($this->errorMessage, $this->returnUrl);
        }

        foreach ($this->model->relatedRecords as $record) {
            if (count($record)) {
                return $controller->returnError($this->integrityViolationMessage, $this->returnUrl);
            }
        }
        $this->fieldToModify = $this->fieldToModify ?: self::FIELD_NAME;
        $this->model->{$this->deleteAttribute} = $this->deleteStatus;
        $this->model->{$this->fieldToModify} = $this->model->{$this->fieldToModify} . '-' . time();

        if ($this->extraFields) {
            $this->model->load($this->extraFields, $this->formName);
        }

        try {
            if (!$this->model->save()) {
                return $controller->returnError($this->model->getErrors(), $this->returnUrl);
            }
        } catch (IntegrityException $ex) {
            return $controller->returnError($this->integrityViolationMessage);
        }

        return $controller->returnSuccess($this->successMessage, $this->returnUrl);
    }
}
