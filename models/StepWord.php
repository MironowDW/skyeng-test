<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Query;

class StepWord extends ActiveRecord
{

    public function attributes()
    {
        return ['id', 'stepId', 'wordId', 'isBase'];
    }

    public function fields()
    {
        return ['id', 'stepId', 'wordId'];
    }

    public function rules()
    {
        return [
            [['stepId', 'wordId'], 'required']
        ];
    }

    public function getWord()
    {
        return $this->hasOne(Word::className(), ['id' => 'wordId']);
    }
}
