<?php

namespace app\controllers;

use app\models\Step;
use app\models\Word;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class WordController extends ActiveController
{

    public $modelClass = 'app\models\Word';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'prepareDataProvider' => [$this, 'prepareDataProvider'],
            ],
        ];
    }

    /**
     * @return Word[]
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function prepareDataProvider()
    {
        $stepId = \Yii::$app->getRequest()->getQueryParam('stepId');
        if (!$stepId) {
            throw new \LogicException('При запросе списка слов нужно передать id шага');
        }

        $step = Step::findOne($stepId);
        if (!$step) {
            throw new NotFoundHttpException();
        }

        // Считаем, что id в таблице слов упорядочены и идут без пробелов
        $minId = Word::find()->min('id');
        $maxId = Word::find()->max('id');

        $wordIds = [$step->wordId];

        if (($maxId - $minId) < Word::VARIANT_COUNT) {
            throw new \Exception('Не хватает слов для вариантов ответов');
        }

        while (count($wordIds) < Word::VARIANT_COUNT) {
            $randId = rand($minId, $maxId);
            if (in_array($randId, $wordIds)) {
                continue;
            }

            $wordIds[] = $randId;
        }

        // Перемешиваем, что бы сбить порядок правильного слова
        $words = Word::findAll(['id' => $wordIds]);
        shuffle($words);

        foreach ($words as $word) {
            ($word->id == $step->wordId)
                ? $this->prepareBaseWord($word, $step->direction)
                : $this->prepareNoBaseWord($word, $step->direction);
        }

        return $words;
    }

    private function prepareBaseWord(Word $word, $direction)
    {
        $word->isBase = true;

        $word->value = ($direction == Step::DIRECTION_RUS_TO_ENG)
            ? $word->rus
            : $word->eng;
    }

    private function prepareNoBaseWord(Word $word, $direction)
    {
        $word->isBase = false;

        $word->value = ($direction == Step::DIRECTION_RUS_TO_ENG)
            ? $word->eng
            : $word->rus;
    }
}