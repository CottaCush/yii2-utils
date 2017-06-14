<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use CottaCush\Yii2\Model\BaseModel;
use yii\base\Action;

/**
 * Class SaveAction
 * @package app\actions
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class SaveAction extends Action
{
    public $returnUrl = '';
    public $successMessage = '';
    public $model;

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Akinwunmi Taiwo <taiwo@cottacush.com>
     * @return \yii\web\Response
     */
    public function run()
    {
        /** @var BaseController $controller */
        $controller = $this->controller;

        $referrerUrl = $controller->getRequest()->referrer;
        $controller->isPostCheck($referrerUrl);

        $postData = $controller->getRequest()->post();

        $model = new  $this->model;
        /** @var  BaseModel $model */
        $model->load($postData);

        if (!$model->save()) {
            $controller->flashError($model->getErrors());
            return $controller->redirect($referrerUrl);
        }

        $controller->flashSuccess($this->successMessage);
        return $controller->redirect($this->returnUrl);
    }
}
