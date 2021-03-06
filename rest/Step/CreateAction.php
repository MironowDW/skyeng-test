<?php

namespace app\rest\Step;

use app\models\Step;
use app\models\StepWord;
use app\models\Word;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class CreateAction extends \yii\rest\CreateAction
{

    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $step = new Step();
        $step->testId = \Yii::$app->getRequest()->getBodyParam('testId');
        $step->wordId = Word::findWordIdForNextStep($step->testId);
        $step->direction = rand(0, 1) ? Step::DIRECTION_ENG_TO_RUS : Step::DIRECTION_RUS_TO_ENG;
        $step->status = Step::STATUS_NEW;

        if (!$step->wordId) {
            throw new \LogicException('Попытка создать шаг без слова ' . $step->testId);
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$step->save()) {
                throw new Exception('Ошибка при сохранение шага ' . $step->testId);
            };

            // Сохранить список слов в шаге
            $wordIds = $this->generateWordIds($step->wordId, $step);
            $this->saveWords($wordIds, $step);

            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $step->id], true));

            $transaction->commit();
        } catch (Exception $e) {
            throw new ServerErrorHttpException($e->getMessage());
        } catch (\Exception $e) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } finally {
            $transaction->rollBack();
        }

        return $step;
    }

    /**
     * Сохранить список слов в шаге
     *
     * @param array $wordIds
     * @param Step $step
     *
     * @throws Exception
     */
    private function saveWords(array $wordIds, Step $step)
    {
        foreach ($wordIds as $wordId) {
            $stepWord = new StepWord();
            $stepWord->wordId = $wordId;
            $stepWord->stepId = $step->id;
            $stepWord->isBase = $wordId == $step->wordId;

            if (!$stepWord->save()) {
                throw new Exception('Ошибка при сохранение слова в шаг ' . $wordId . ' ' . $step->id);
            };
        }
    }

    /**
     * Генери массив из вариантов ответов слов, в который входит базовое слово + три рандомных
     *
     * @param Step $step
     * @param $baseWordId
     *
     * @return array
     *
     * @throws \Exception
     */
    private function generateWordIds($baseWordId, Step $step)
    {
        // Считаем, что id в таблице слов упорядочены и идут без пробелов
        $minId = Word::find()->min('id');
        $maxId = Word::find()->max('id');

        // Храним значения переводов, что бы в тесте не было одинаковых переводов
        $wordValues = [];
        $baseWord = Word::findOne($baseWordId);
        $wordValues[] = $step->getOptionWordValue($baseWord);

        // В вариантах всегда должен быть правильный ответ
        $wordIds = [$baseWordId];

        while (count($wordIds) < Word::VARIANT_COUNT) {
            $randId = rand($minId, $maxId);
            if (in_array($randId, $wordIds)) {
                continue;
            }

            $word = Word::findOne($randId);
            $wordValue = $step->getOptionWordValue($word);

            // Пропускаем пвторы
            if (in_array($wordValue, $wordValues)) {
                continue;
            }

            $wordValues[] = $wordValue;
            $wordIds[] = $randId;
        }

        // Перемешиваем, что бы сбить порядок правильного слова
        shuffle($wordIds);

        return $wordIds;
    }
}