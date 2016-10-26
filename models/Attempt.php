<?php

namespace app\models;

use yii\db\ActiveRecord;

class Attempt extends ActiveRecord
{

    const STATUS_SUCCESS = 0;
    const STATUS_FAIL = 1;

    const MAX = 3;

    public function attributes()
    {
        return ['id', 'stepId', 'stepWordId', 'status'];
    }

    public function rules()
    {
        return [
            [['stepId', 'stepWordId'], 'required']
        ];
    }
}
