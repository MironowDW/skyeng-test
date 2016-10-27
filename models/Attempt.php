<?php

namespace app\models;

use yii\db\ActiveRecord;

class Attempt extends ActiveRecord
{

    const STATUS_SUCCESS = 0;
    const STATUS_FAIL = 1;

    /**
     * Количество попыток за один шаг
     */
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

    /**
     * Добавляем логику для отображения
     *
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     *
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $step = $this->step;
        $test = $step->test;

        $array = parent::toArray($fields, $expand, $recursive);

        $array['step'] = $step->toArray();
        $array['test'] = $test->toArray();

        return $array;
    }

    public function getStep()
    {
        return $this->hasOne(Step::className(), ['id' => 'stepId']);
    }

    public function getStepWord()
    {
        return $this->hasOne(StepWord::className(), ['id' => 'stepWordId']);
    }
}
