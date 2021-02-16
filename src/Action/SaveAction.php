<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use CottaCush\Yii2\Model\BaseModel;
use Yii;
use yii\helpers\Json;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class SaveAction
 * @package app\actions
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class SaveAction extends BaseAction
{
    public $enableAjaxValidation = false;

    /**
     * @return string|Response
     * @author Akinwunmi Taiwo <taiwo@cottacush.com>
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function run(): Response|string
    {
        /** @var BaseController $controller */
        $controller = $this->controller;

        $referrerUrl = $controller->getRequest()->referrer;
        $controller->isPostCheck($referrerUrl);

        if (is_null($this->postData)) {
            $this->postData = $controller->getRequest()->post();
        }

        /** @var BaseModel $model */
        $model = new $this->model;
        $model->load($this->postData);

        if (Yii::$app->request->isAjax && $this->enableAjaxValidation) {
            return Json::encode(ActiveForm::validate($model));
        }

        if (!$model->save()) {
            return $controller->returnError($model->getErrors());
        }
        $this->processMessage($this->successMessage);
        return $controller->returnSuccess($this->successMessage, $this->returnUrl);
    }
}
