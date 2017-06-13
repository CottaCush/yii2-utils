<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use CottaCush\Yii2\Model\BaseModel;
use yii\base\Action;

/**
 * Class DeleteAction
 * @package CottaCush\Yii2\Action
 * @author Adegoke Obasa <goke@cottacush.com>
 * @codeCoverageIgnore
 */
class DeleteAction extends Action
{
    public $returnUrl = '';
    public $successMessage = '';
    /** @var  BaseModel $model */
    public $model;
    public $deleteStatus;
    public $errorMessage = 'Record not found';

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

        if (!$this->model) {
            $controller->flashError($this->errorMessage);
        } else {
            $this->model->is_active = $this->deleteStatus;
            if (!$this->model->save()) {
                $controller->flashError($this->model->getErrors());
            } else {
                $controller->flashSuccess($this->successMessage);
            }
        }

        return $controller->redirect($this->returnUrl);
    }
}
