<?php

namespace app\controllers;

use app\services\PermissionService;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class TestController extends ActiveController
{

    public $modelClass = 'app\models\Test';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
        ];
    }

    /**
     * Даем доступ, только при валидном токине в запросе
     *
     * @param string $action
     * @param null $model
     * @param array $params
     *
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $permissionService = \Yii::$app->get(PermissionService::class);
        $accessToken = \Yii::$app->getRequest()->getBodyParam('accessToken');

        $permissionService->checkAccessToken($accessToken);
    }
}