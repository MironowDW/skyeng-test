<?php

namespace app\models;

use yii\db\ActiveRecord;

class Step extends ActiveRecord
{

    const DIRECTION_RUS_TO_ENG = 'rus_to_eng';
    const DIRECTION_ENG_TO_RUS = 'eng_to_rus';

    const STATUS_NEW = 0;
    const STATUS_CLOSE = 1;

    public function attributes()
    {
        return ['id', 'testId', 'wordId', 'direction', 'status'];
    }

    public function fields()
    {
        return ['id', 'testId', 'direction', 'status'];
    }

    public function rules()
    {
        return [
            [['testId', 'wordId', 'direction'], 'required']
        ];
    }

    /**
     * Для базового слова оставляем оригинал
     */
    public function getBaseWordValue()
    {
        return ($this->direction == self::DIRECTION_RUS_TO_ENG)
            ? $this->word->rus
            : $this->word->eng;
    }

    /**
     * Для варианта слова оставляем перевоод
     */
    public function getOptionWordValue(Word $word)
    {
        return ($this->direction == self::DIRECTION_RUS_TO_ENG)
            ? $word->eng
            : $word->rus;
    }

    public function getWord()
    {
        return $this->hasOne(Word::className(), ['id' => 'wordId']);
    }

    public function getStepWords()
    {
        return $this->hasMany(StepWord::className(), ['stepId' => 'id']);
    }
}
