<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use yii\base\Action;

class DeleteAction extends Action
{
    public $returnUrl = '';
    public $successMessage = '';
    public $model;
    public $deleteStatus;

    /**
     * @author Akinwunmi Taiwo <taiwo@cottacush.com>
     * @return \yii\web\Response
     */
    public function run()
    {
        /** @var BaseController $controller */
        $controller = $this->controller;

        $referrerUrl = $controller->getRequest()->referrer;
        $controller->isPostCheck($referrerUrl);
        $modelToDelete = $this->model;

        if (!$modelToDelete) {
            $controller->flashError('Record not found');
        } else {
            $modelToDelete->is_active = $this->deleteStatus;
            if (!$modelToDelete->update()) {
                $controller->flashError($modelToDelete->getErrors());
            } else {
                $controller->flashSuccess($this->successMessage);
            }
        }

        return $controller->redirect($this->returnUrl);
    }
}
