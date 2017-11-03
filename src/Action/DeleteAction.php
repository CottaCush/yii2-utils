<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;

/**
 * Class DeleteAction
 * @package CottaCush\Yii2\Action
 * @author Adegoke Obasa <goke@cottacush.com>
 * @codeCoverageIgnore
 */
class DeleteAction extends BaseAction
{
    public $deleteAttribute = 'is_active';
    public $deleteStatus = 0;
    public $errorMessage = 'Record not found';

    public $extraFields = null;
    public $formName = '';

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
            return $controller->returnError($this->errorMessage, $this->returnUrl);
        }

        $this->model->{$this->deleteAttribute} = $this->deleteStatus;
        if ($this->extraFields) {
            $this->model->load($this->extraFields, $this->formName);
        }

        if (!$this->model->save()) {
            return $controller->returnError($this->model->getErrors(), $this->returnUrl);
        }

        return $controller->returnSuccess($this->successMessage, $this->returnUrl);
    }
}
