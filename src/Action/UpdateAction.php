<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\db\IntegrityException;

/**
 * Class UpdateAction
 * @package CottaCush\Yii2\Action
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class UpdateAction extends Action
{
    public $returnUrl = '';
    public $successMessage = '';
    public $notFoundMessage = 'Record not found';
    /** @var ActiveRecord $model */
    public $model;
    public $postData;
    public $integrityExceptionMessage = 'Record cannot be updated as it is in use elsewhere';

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

        try {
            if (!$this->model) {
                return $controller->returnError($this->notFoundMessage);
            }
            $this->model->load($this->postData);

            if (!$this->model->save()) {
                return $controller->returnError($this->model->getErrors());
            }
        } catch (IntegrityException $ex) {
            return $controller->returnError($this->integrityExceptionMessage);
        }
        return $controller->returnSuccess($this->successMessage, $this->returnUrl);
    }
}
