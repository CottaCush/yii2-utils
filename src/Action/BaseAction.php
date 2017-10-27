<?php

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\web\Response;

class BaseAction extends Action
{
    /** @var ActiveRecord $model */
    public $model;

    public $postData;
    public $returnUrl = '';
    public $successMessage = '';

    /** @var bool Checks if the login is required before action is executed */
    public $requireLogin = true;

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @return bool|\yii\web\Response
     */
    public function beforeRun()
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
}