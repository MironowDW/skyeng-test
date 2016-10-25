<?php

namespace app\controllers;

use app\services\AccessTokenService;
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
        $accessToken = \Yii::$app->getRequest()->getBodyParam('accessToken');

        $accessTokenService = \Yii::$app->get(AccessTokenService::class);
        $user = $accessTokenService->getUserByAccessToken($accessToken);
        if (!$user) {
            throw new ForbiddenHttpException();
        }
    }
}