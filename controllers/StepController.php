<?php

namespace app\controllers;

use app\models\Test;
use app\services\AccessTokenService;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class StepController extends ActiveController
{

    public $modelClass = 'app\models\Step';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'create' => [
                'class' => 'app\rest\Step\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'view' => [
                'class' => 'app\rest\Step\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];
    }

    /**
     * Даем доступ, только при валидном токине в запросе, который соответствуем тесту
     *
     * @param string $action
     * @param null $model
     * @param array $params
     *
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $request = \Yii::$app->getRequest();

        $accessToken = $request->getIsPost() ? $request->getBodyParam('accessToken') : $request->getQueryParam('accessToken');
        $testId = $model ? $model->testId : $request->getBodyParam('testId');

        $accessTokenService = \Yii::$app->get(AccessTokenService::class);
        $user = $accessTokenService->getUserByAccessToken($accessToken);
        if (!$user) {
            throw new ForbiddenHttpException();
        }

        // У пользователя есть переданный курс?
        $test = Test::findOne(['userId' => $user->id, 'id' => $testId]);
        if (!$test) {
            throw new ForbiddenHttpException();
        }
    }
}