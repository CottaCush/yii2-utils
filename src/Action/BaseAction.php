<?php

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class BaseAction extends Action
{
    public ActiveRecord $model;

    public $postData;
    public string $returnUrl = '';

    public string $recordNotFound = 'Record not found';
    public string $errorMessage = '';
    public string $successMessage = '';

    /** @var bool Checks if the login is required before action is executed */
    public bool $requireLogin = true;

    /**
     * @return bool|Response
     * @throws ForbiddenHttpException
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function beforeRun(): Response|bool
    {
        if (!$this->requireLogin) {
            return true;
        }

        /** @var BaseController $controller */
        $controller = $this->controller;
        /** @var /yii/web/Response $response */
        $response = $controller->loginRequireBeforeAction();

        if ($response instanceof Response) {
            $response->send();
            return false;
        }

        return true;
    }

    /**
     * Process message that contains callback
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $message
     */
    protected function processMessage(&$message)
    {
        if (!$message instanceof \Closure) {
            return;
        }

        $callback = $message;
        $message = $callback($this->model);
    }
}
