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

        $step = $attempt->step;
        $test = $step->test;

        // Определяем статус попытки
        $attempt->status = ($step->wordId == $attempt->stepWord->wordId)
            ? Attempt::STATUS_SUCCESS
            : Attempt::STATUS_FAIL;

        if (!$attempt->save()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        // Обновялем статус шага и теста при провале
        if ($this->isFailStep($attempt)) {
            $step->status = Step::STATUS_FAIL;
            $step->update();

            $test->status = Test::STATUS_FAIL;
            $test->update();
        }

        // Обновляем статус теста в случае успеха
        if ($this->isSuccessTest($attempt)) {
            $test->status = Test::STATUS_SUCCESS;
            $test->update();
        }

        // Обновляем статус попытки на успешную
        if ($attempt->status == Attempt::STATUS_SUCCESS) {
            $step->status = Step::STATUS_SUCCESS;
            $step->update();
        }

        $response = \Yii::$app->getResponse();
        $response->setStatusCode(201);
        $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $step->id], true));

        return $attempt;
    }

    /**
     * Шаг завален?
     *
     * @param $attempt
     *
     * @return bool
     */
    private function isFailStep(Attempt $attempt)
    {
        if ($attempt->status != Attempt::STATUS_FAIL) {
            return false;
        }

        // Количество неудачных попыток в шаге
        $failAttemptsCount = Attempt::find()
            ->andWhere(['status' => Attempt::STATUS_FAIL])
            ->andWhere(['stepId' => $attempt->stepId])
            ->count();

        return $failAttemptsCount >= Attempt::MAX;
    }

    /**
     * Тест успешно завершается в случае завершения слов
     *
     * @param Attempt $attempt
     *
     * @return bool
     */
    private function isSuccessTest(Attempt $attempt)
    {
        return !Word::findWordIdForNextStep($attempt->step->testId);
    }
}