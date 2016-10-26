<?php

namespace app\rest\Attempt;

use app\models\Attempt;
use app\models\Step;
use app\models\StepWord;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class CreateAction extends \yii\rest\CreateAction
{

    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $attempt = new Attempt();
        $attempt->stepId = \Yii::$app->getRequest()->getBodyParam('stepId');
        $attempt->stepWordId = \Yii::$app->getRequest()->getBodyParam('stepWordId');
        $attempt->status = Attempt::STATUS_SUCCESS;

        $stepWord = StepWord::findOne($attempt->stepWordId);
        $step = Step::findOne($attempt->stepId);

        $attempt->status = ($step->wordId == $stepWord->wordId)
            ? Attempt::STATUS_SUCCESS
            : Attempt::STATUS_FAIL;

        if (!$attempt->save()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        $response = \Yii::$app->getResponse();
        $response->setStatusCode(201);
        $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $step->id], true));

        return $attempt;
    }
}