<?php

namespace app\rest\Attempt;

use app\models\Attempt;
use app\models\Step;
use app\models\StepWord;
use app\models\Test;
use app\models\Word;
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
        $test = $step->test;

        $attempt->status = ($step->wordId == $stepWord->wordId)
            ? Attempt::STATUS_SUCCESS
            : Attempt::STATUS_FAIL;

        if (!$attempt->save()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        // Проверяем количество попыток
        if ($attempt->status == Attempt::STATUS_FAIL) {
            $failAttemptsCount = Attempt::find()
                ->andWhere(['status' => Attempt::STATUS_FAIL])
                ->andWhere(['stepId' => $attempt->stepId])
                ->count();

            if ($failAttemptsCount >= Attempt::MAX) {
                $step->status = Step::STATUS_FAIL;
                $step->update();

                $test->status = Test::STATUS_FAIL;
                $test->update();
            }
        } else {
            $step->status = Step::STATUS_SUCCESS;
            $step->update();

            // Проверяем, возможно слова закончились
            $wordId = Word::findWordIdForNextStep($step->testId);
            if (!$wordId) {
                $test->status = Test::STATUS_SUCCESS;
                $test->update();
            }
        }

        $response = \Yii::$app->getResponse();
        $response->setStatusCode(201);
        $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $step->id], true));

        $arrayResponse = $attempt->toArray();
        $arrayResponse['step'] = $step->toArray();
        $arrayResponse['test'] = $test->toArray();
        $arrayResponse['rating'] = Step::find()
            ->andWhere(['status' => Step::STATUS_SUCCESS])
            ->andWhere(['testId' => $test->id])
            ->count();

        return $arrayResponse;
    }
}