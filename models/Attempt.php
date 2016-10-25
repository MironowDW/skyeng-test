<?php

namespace app\models;

use yii\db\ActiveRecord;

class Attempt extends ActiveRecord
{

    public function attributes()
    {
        return ['id', 'stepId', 'status'];
    }

    public function rules()
    {
        return [
            [['stepId'], 'required']
        ];
    }
}
