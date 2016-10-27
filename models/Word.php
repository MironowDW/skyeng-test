<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Query;

class Word extends ActiveRecord
{

    /**
     * Вариантов на выбыр, включая правильный
     */
    const VARIANT_COUNT = 4;

    public function attributes()
    {
        return ['id', 'rus', 'eng', 'value'];
    }

    public function fields()
    {
        return ['id', 'value'];
    }

    public function rules()
    {
        return [
            [['rus', 'eng'], 'required']
        ];
    }

    /**
     * Ищет первое слово, которое еще не участвовало в тесте
     *
     * @param $testId
     *
     * @return array|bool
     */
    public static function findWordIdForNextStep($testId)
    {
        // TODO Надо брать рандомное

        $query = new Query();
        $query->addSelect(['w.id'])
            ->from ([Word::tableName() . ' w'])
            ->leftJoin(Step::tableName().' s', 'w.id = s.wordId AND s.testId = :testId', [':testId' => $testId])
            ->where('s.id IS NULL')
            ->limit(1);

        return $query->scalar();
    }
}
