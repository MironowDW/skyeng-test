<?php

namespace app\rest\Step;

use app\models\Step;

class CreateAction extends \yii\rest\CreateAction
{

    public function run()
    {
        /** @var Step $step */
        $step = parent::run();
        $word = $step->word;

        // Устанавливаем значение базового слова
        $step->baseWord = $word;
        $step->baseWord->value = ($step->direction == Step::DIRECTION_RUS_TO_ENG)
            ? $word->rus
            : $word->eng;

        return $step;
    }
}