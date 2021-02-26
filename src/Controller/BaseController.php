<?php

namespace CottaCush\Yii2\Controller;

use CottaCush\Yii2\Action\SaveAction;
use CottaCush\Yii2\Action\SoftDeleteAction;
use CottaCush\Yii2\Action\UpdateAction;
use CottaCush\Yii2\Constants\Messages;
use CottaCush\Yii2\Model\BaseModel;
use Exception;
use Lukasoppermann\Httpstatus\Httpstatus;
use Yii;
use yii\base\Action;
use yii\base\ExitException;
use yii\base\InvalidConfigException;
use yii\base\Security;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;
use yii\web\User;

/**
 * Class BaseController
 * @package app\controllers
 * @author Adegoke Obasa <goke@cottacush.com>
 * @codeCoverageIgnore
 */
class BaseController extends Controller
{
    const ACTION_ACTIVATE = 'activate';
    const ACTION_DEACTIVATE = 'deactivate';

    const ACTION_CREATE = 'create';
    const ACTION_DELETE = 'delete';
    const ACTION_UPDATE = 'update';

    /**
     * @var Httpstatus $httpStatuses
     */
    protected $httpStatuses;

    const FLASH_SUCCESS_KEY = 'success';
    const FLASH_ERROR_KEY = 'error';

    public function init()
    {
        parent::init();
        $this->httpStatuses = new Httpstatus();
    }

    /**
     * Executed after action
     * @param Action $action
     * @param mixed $result
     * @return mixed
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function afterAction($action, mixed $result): mixed
    {
        $result = parent::afterAction($action, $result);
        $this->setSecurityHeaders();

        /**
         * Set Current Transaction in New Relic
         * @author Adegoke Obasa <goke@cottacush.com>
         */
        if (extension_loaded('newrelic')) {
            newrelic_name_transaction($action->controller->id . '/' . $action->id);
        }
        return $result;
    }

    /**
     * Set Headers to prevent Click-jacking and XSS
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    private function setSecurityHeaders()
    {
        $headers = Yii::$app->response->headers;
        $headers->add('X-Frame-Options', 'DENY');
        $headers->add('X-XSS-Protection', '1');
    }

    /**
     * Allow sending success response
     * @author Oluwarotimi Akintewe <akintewe.rotimi@gmail.com>
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $data
     * @return array
     */
    public function sendSuccessResponse($data): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->setStatusCode(200, $this->httpStatuses->getReasonPhrase(200));

        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    /**
     * Allows sending error response
     * @author Oluwarotimi Akintewe <akintewe.rotimi@gmail.com>
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $message
     * @param $code
     * @param $httpStatusCode
     * @param null $data
     * @return array
     */
    public function sendErrorResponse($message, $code, $httpStatusCode, $data = null): array
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->setStatusCode($httpStatusCode, $this->httpStatuses->getReasonPhrase($httpStatusCode));

        $response = [
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ];

        if (!is_null($data)) {
            $response["data"] = $data;
        }

        return $response;
    }

    /**
     * Sends fail response
     * @param $data
     * @author Oluwarotimi Akintewe <akintewe.rotimi@gmail.com>
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param int $httpStatusCode
     * @return array
     */
    public function sendFailResponse($data, $httpStatusCode = 500): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->setStatusCode($httpStatusCode, $this->httpStatuses->getReasonPhrase($httpStatusCode));

        return [
            'status' => 'fail',
            'data' => $data
        ];
    }

    /**
     * This flashes error message and sends to the view
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $messages
     */
    public function flashError($messages)
    {
        $this->flash(self::FLASH_ERROR_KEY, $messages);
    }

    /**
     * This flashes success message and sends to the view
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $messages
     */
    public function flashSuccess($messages)
    {
        $this->flash(self::FLASH_SUCCESS_KEY, $messages);
    }

    /**
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $key
     * @param $messages
     */
    protected function flash($key, $messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                Yii::$app->session->addFlash($key, $message);
            }
        } else {
            Yii::$app->session->setFlash($key, $messages);
        }
    }

    /**
     * Get Yii2 request object
     * @return \yii\console\Request|Request
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function getRequest(): Request|\yii\console\Request
    {
        return Yii::$app->request;
    }

    /**
     * Get Yii2 response object
     * @return \yii\console\Request|Response
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function getResponse(): Response|\yii\console\Request
    {
        return Yii::$app->response;
    }

    /**
     * Get Yii2 session object
     * @return mixed
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function getSession(): mixed
    {
        return Yii::$app->session;
    }

    /**
     * Get Yii2 security object
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return Security
     */
    public function getSecurity(): Security
    {
        return Yii::$app->security;
    }

    /**
     * Get web user
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function getUser()
    {
        return Yii::$app->user;
    }

    /**
     * show flash messages
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param bool $sticky
     * @return string
     */
    public function showFlashMessages($sticky = false): string
    {
        $timeout = $sticky ? 0 : 5000;
        $flashMessages = [];
        $allMessages = $this->getSession()->getAllFlashes();
        foreach ($allMessages as $key => $message) {
            if (is_array($message)) {
                $message = $this->mergeFlashMessages($message);
            }
            $flashMessages[] = [
                'message' => $message,
                'type' => $key,
                'timeout' => $timeout
            ];
        }
        $this->getSession()->removeAllFlashes();
        return Html::script('var notifications =' . json_encode($flashMessages));
    }

    /**
     * merge flash messages
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $messageArray
     * @return string
     */
    protected function mergeFlashMessages($messageArray): string
    {
        $messages = array_values($messageArray);
        $flashMessage = '';
        $flashMessageArr = [];
        foreach ($messages as $message) {
            if (is_array($message)) {
                if (strlen($flashMessage) > 0) {
                    $flashMessage .= '<br/>';
                }
                $flashMessage .= $this->mergeFlashMessages($message);
            } else {
                $flashMessageArr[] = $message;
            }
        }

        return $flashMessage . implode('<br/>', $flashMessageArr);
    }

    /**
     * Returns the user for the current module
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return User null|object
     * @throws InvalidConfigException
     */
    public function getModuleUser(): User
    {
        return $this->module->get('user');
    }

    /**
     * @param $url
     * @throws ExitException
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function sendTerminalResponse($url)
    {
        $this->redirect($url)->send();
        Yii::$app->end();
    }

    /**
     * Checks if the current request is a POST and handles redirection
     * @param null $redirectUrl
     * @return bool
     * @throws ExitException
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function isPostCheck($redirectUrl = null): bool
    {
        if ($this->getRequest()->isPost) {
            return true;
        }
        if (is_null($redirectUrl)) {
            return false;
        }
        $this->sendTerminalResponse($redirectUrl);
    }

    /**
     * @return bool
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function loginRequireBeforeAction(): bool
    {
        $excludedPaths = ArrayHelper::getValue(Yii::$app->params, 'excludedPaths', []);
        $currentRoute = $this->getRoute();

        /**
         * Check if route is in the excluded path
         */
        if ($this->getUser()->isGuest) {
            if (!in_array($currentRoute, $excludedPaths)) {
                $this->getUser()->loginRequired();
                return false;
            }
        }

        return true;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $message
     * @param null $redirectUrl
     * @return Response
     */
    public function returnError($message, $redirectUrl = null): Response
    {
        $this->flashError($message);
        if (is_null($redirectUrl)) {
            $redirectUrl = $this->getRequest()->getReferrer();
        }
        return $this->redirect($redirectUrl);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $message
     * @param null $redirectUrl
     * @return Response
     */
    public function returnSuccess($message, $redirectUrl = null): Response
    {
        $this->flashSuccess($message);
        if (is_null($redirectUrl)) {
            $redirectUrl = $this->getRequest()->getReferrer();
        }
        return $this->redirect($redirectUrl);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $widget
     * @param $config
     * @param null $redirectToOnFail URL to redirect to on fail or if request is not an ajax request
     * @return string
     */
    public function renderWidgetAsAjax($widget, $config, $redirectToOnFail = null): string
    {
        $referrer = $this->getRequest()->getReferrer() ?: Yii::$app->homeUrl;

        if (!$this->getRequest()->isAjax) {
            if (is_null($redirectToOnFail)) {
                return $referrer;
            } else {
                return $this->redirect($redirectToOnFail);
            }
        }

        ob_start();
        ob_implicit_flush(false);

        $this->view->beginPage();
        $this->view->head();
        $this->view->beginBody();
        echo $widget::widget($config);
        $this->view->endBody();
        $this->view->endPage(true);

        return ob_get_clean();
    }

    /**
     * Sets a session variable and redirects to the URL
     * @param $key
     * @param $value
     * @param $redirectUrl
     * @return Response
     *@author Taiwo Ladipo <ladipotaiwo01@gmail.com>
     */
    public function setSessionAndRedirect($key, $value, $redirectUrl): Response
    {
        $this->getSession()->set($key, $value);
        return $this->redirect($redirectUrl);
    }

    /**
     * Get Permission Based Access Manager Configuration
     * @author Taiwo Ladipo <ladipotaiwo01@gmail.com>
     * @return mixed
     */
    public function getPermissionManager(): mixed
    {
        return Yii::$app->permissionManager;
    }

    /**
     * Handle access based on permissions
     * @author Taiwo Ladipo <ladipotaiwo01@gmail.com>
     * @param $permissionKeys
     * @param bool $redirect
     * @param $fullAccessKey
     * @param $errorMsg
     * @param $defaultUrl
     * @return bool|Response
     * @throws ExitException
     * @throws ForbiddenHttpException
     */
    public function canAccess($permissionKeys, $fullAccessKey, $errorMsg, $defaultUrl, $redirect = false): Response|bool
    {
        if ($this->getUser()->isGuest) {
            return $this->getUser()->loginRequired();
        }

        if ($this->getPermissionManager()->canAccess($fullAccessKey)) {
            return true;
        }

        if (!is_array($permissionKeys)) {
            $permissionKeys = [$permissionKeys];
        }

        foreach ($permissionKeys as $permissionKey) {
            if ($this->getPermissionManager()->canAccess($permissionKey)) {
                return true;
            }
        }

        if ($redirect) {
            $this->flashError($errorMsg);

            $request = $this->getRequest();
            $referrerUrl = $request->referrer;

            $redirectUrl = ($referrerUrl == $request->url || is_null($referrerUrl)) ?
                $defaultUrl : $referrerUrl;

            $this->redirect($redirectUrl)->send();
            Yii::$app->end(); //this enforces the redirect
        }
        return false;
    }

    /**
     * @param $model
     * @param $entityName
     * @param $actions
     * @param $returnUrl
     * @param null $formName
     * @return array
     * @throws InvalidConfigException
     * @throws Exception
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function generateLOVActions($model, $entityName, $actions, $returnUrl, $formName = null): array
    {
        $generatedActions = [];
        $postData = $this->getRequest()->post();
        /** @var BaseModel $modelObj */
        $modelObj = new $model;
        $formName = $formName ?: $modelObj->formName();

        foreach ($actions as $action) {
            $url = ArrayHelper::getValue($action, 'url', false);
            $type = ArrayHelper::getValue($action, 'type', false);

            if (!$url || !$type) {
                continue;
            }

            $extraModelData = ArrayHelper::getValue($action, 'extraModelData', []);

            switch ($type) {
                case self::ACTION_CREATE:
                    $task = ArrayHelper::getValue($action, 'task', Messages::TASK_ADD);
                    $class = SaveAction::class;

                    $generatedActions[$url] = [
                        'class' => $class,
                        'returnUrl' => $returnUrl,
                        'model' => $model,
                        'enableAjax' => ArrayHelper::getValue($action, 'enableAjaxValidation', false),
                        'postData' => array_merge_recursive($postData, [$formName => $extraModelData]),
                        'successMessage' => Messages::getSuccessMessage($entityName, $task),
                    ];
                    break;

                case self::ACTION_UPDATE:
                    $task = ArrayHelper::getValue($action, 'task', Messages::TASK_UPDATE);

                    $generatedActions[$url] = [
                        'class' => UpdateAction::class,
                        'returnUrl' => $returnUrl,
                        'model' => $modelObj::findOne(ArrayHelper::getValue($postData, $formName . '.id')),
                        'postData' => array_merge_recursive($postData, [$formName => $extraModelData]),
                        'successMessage' => Messages::getSuccessMessage($entityName, $task)
                    ];
                    break;

                case self::ACTION_DELETE:
                    $task = ArrayHelper::getValue($action, 'task', Messages::TASK_DELETE);

                    $generatedActions[$url] = [
                        'class' => SoftDeleteAction::class,
                        'returnUrl' => $returnUrl,
                        'model' => $model,
                        'modelId' => ArrayHelper::getValue($postData, 'id'),
                        'successMessage' => Messages::getSuccessMessage($entityName, $task),
                        'fieldToModify' => ArrayHelper::getValue($action, 'fieldToModify'),
                        'extraFields' => $extraModelData,
                        'relatedRecords' => ArrayHelper::getValue($action, 'relatedRecords', []),
                        'integrityViolationMessage' => Messages::getIntegrityViolationMsg($entityName, $task)
                    ];
                    break;

                default:
                    break;
            }
        }

        return $generatedActions;
    }
}
