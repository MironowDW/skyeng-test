<?php

namespace app\controllers;

use app\models\Step;
use app\services\PermissionService;
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
        $permissionService = \Yii::$app->get(PermissionService::class);
        $request = \Yii::$app->getRequest();

        $accessToken = $request->getBodyParam('accessToken');
        $stepId = $request->getBodyParam('stepId');
        $step = Step::findOne($stepId);

        // Проверяем права на тест
        $permissionService->checkTest($accessToken, $step->testId);

        // Нельзя проходить завершенный шаг
        if ($step->status != Step::STATUS_NEW) {
            throw new ForbiddenHttpException('Нельзя проходить завершенный шаг');
        }
    }
}