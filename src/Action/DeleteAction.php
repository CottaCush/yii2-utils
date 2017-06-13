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

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

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
            $modelToDelete->is_active = self::STATUS_INACTIVE;
            if (!$modelToDelete->update()) {
                $controller->flashError($modelToDelete->getErrors());
            } else {
                $controller->flashSuccess($this->successMessage);
            }
        }

        return $controller->redirect($this->returnUrl);
    }
}
