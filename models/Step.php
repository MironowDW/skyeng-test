<?php

namespace app\models;

use yii\db\ActiveRecord;

class Step extends ActiveRecord
{

    const DIRECTION_RUS_TO_ENG = 'rus_to_eng';
    const DIRECTION_ENG_TO_RUS = 'eng_to_rus';

    public function attributes()
    {
        return ['id', 'testId', 'wordId', 'direction', 'baseWord'];
    }

    public function fields()
    {
        return ['id', 'testId', 'direction', 'baseWord'];
    }

    public function rules()
    {
        return [
            [['testId', 'wordId', 'direction'], 'required']
        ];
    }

    public function getWord()
    {
        return $this->hasOne(Word::className(), ['id' => 'wordId']);
    }

    /**
     * Устанавливаем id слова
     *
     * @param array $data
     * @param null $formName
     *
     * @return bool
     */
    public function load($data, $formName = null)
    {
        $testId = $data['testId'];
        $this->wordId = Word::findWordIdForNextStep($testId);
        $this->direction = rand(0, 1) ? self::DIRECTION_ENG_TO_RUS : self::DIRECTION_RUS_TO_ENG;

        if (!$this->wordId) {
            throw new \LogicException('Попытка создать шаг без слова ' . $testId);
        }

        parent::load($data, $formName);
    }
}
