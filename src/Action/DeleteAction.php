<?php
/**
 * @author Adegoke Obasa <goke@cottacush.com>
 */

namespace CottaCush\Yii2\Action;

use CottaCush\Yii2\Controller\BaseController;
use yii\base\ExitException;
use yii\web\Response;

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
    public string $errorMessage = 'Record not found';

    public $extraFields = null;
    public $formName = '';

    /**
     * @return Response
     * @throws ExitException
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Akinwunmi Taiwo <taiwo@cottacush.com>
     */
    public function run(): Response
    {
        /** @var BaseController $controller */
        $controller = $this->controller;

        $referrerUrl = $controller->getRequest()->referrer;
        $controller->isPostCheck($referrerUrl);

        if (!$this->model) {
            return $controller->returnError($this->errorMessage, $this->returnUrl);
        }

        $this->processMessage($this->errorMessage);
        $this->processMessage($this->successMessage);

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
