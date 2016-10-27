<?php

namespace app\controllers;

use app\models\Step;
use app\services\PermissionService;
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
                'class' => 'yii\rest\ViewAction',
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
        $permissionService = \Yii::$app->get(PermissionService::class);
        $request = \Yii::$app->getRequest();

        $accessToken = $request->getIsPost() ? $request->getBodyParam('accessToken') : $request->getQueryParam('accessToken');
        $testId = $model ? $model->testId : $request->getBodyParam('testId');

        // Проверяем права на тест
        $permissionService->checkTest($accessToken, $testId);

        if ($action == 'create') {
            // Не даем создавать шаг, если есть не завершенный
            $uncompletedStepCount = Step::find()
                ->andWhere(['testId' => $testId])
                ->andWhere(['status' => Step::STATUS_NEW])
                ->count();

            if ($uncompletedStepCount > 0) {
                throw new ForbiddenHttpException('Есть незавершенные шаги');
            }
        }
    }
}