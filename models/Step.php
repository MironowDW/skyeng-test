<?php

namespace app\models;

use yii\db\ActiveRecord;

class Step extends ActiveRecord
{

    const DIRECTION_RUS_TO_ENG = 'rus_to_eng';
    const DIRECTION_ENG_TO_RUS = 'eng_to_rus';

    const STATUS_NEW = 0;
    const STATUS_FAIL = 1;
    const STATUS_SUCCESS = 2;

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
     * Добавляем логику для отображения всего шага
     *
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     *
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        // Слово, которое нужно перевести
        $array['word'] = $this->getBaseWordValue();
        $array['test'] = $this->test->toArray();

        // Варианты ответа
        $array['options'] = [];
        foreach ($this->stepWords as $stepWord) {
            $array['options'][] = [
                'step_word_id' => $stepWord->id, // Не указываем id слова, что бы значение нельзя было получить через api
                    'value' => $this->getOptionWordValue($stepWord->word),
            ];
        }

        return $array;
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
     * Для варианта слова оставляем перевод
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

    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'testId']);
    }

    public function getStepWords()
    {
        return $this->hasMany(StepWord::className(), ['stepId' => 'id']);
    }
}
