<?php

namespace app\controllers;

use app\models\Step;
use app\models\Test;
use app\services\AccessTokenService;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class AttemptController extends ActiveController
{

    public $modelClass = 'app\models\Attempt';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'create' => [
                'class' => \app\rest\Attempt\CreateAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        $request = \Yii::$app->getRequest();

        $accessToken = $request->getBodyParam('accessToken');
        $stepId = $request->getBodyParam('stepId');
        $step = Step::findOne($stepId);

        // Нельзя проходить завершенный шаг
        if ($step->status != Step::STATUS_NEW) {
            throw new ForbiddenHttpException('Нельзя проходить завершенный шаг');
        }

        $accessTokenService = \Yii::$app->get(AccessTokenService::class);
        $user = $accessTokenService->getUserByAccessToken($accessToken);
        if (!$user) {
            throw new ForbiddenHttpException('Не верный acess token');
        }

        // У пользователя есть переданный курс?
        $test = Test::findOne(['userId' => $user->id, 'id' => $step->testId]);
        if (!$test) {
            throw new ForbiddenHttpException('У пользователя нет теста');
        }
    }
}