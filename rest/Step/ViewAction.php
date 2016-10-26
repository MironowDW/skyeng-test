<?php

namespace app\rest\Step;

use app\models\Step;

class ViewAction extends \yii\rest\ViewAction
{

    /**
     * {@inheritdoc}
     */
    public function run($id)
    {
        /** @var Step $step */
        $step = parent::run($id);

        $response = $step->toArray();

        // Слово, которое нужно перевести
        $response['word'] = $step->getBaseWordValue();

        // Варианты ответа
        $response['options'] = [];
        foreach ($step->stepWords as $stepWord) {
            $response['options'][] = [
                'step_word_id' => $stepWord->id, // Не указываем id слова, что бы значение нельзя было получить через api
                'value' => $step->getOptionWordValue($stepWord->word),
            ];
        }

        return $response;
    }
}