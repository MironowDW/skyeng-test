<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{

    public function attributes()
    {
        return ['id', 'username', 'accessToken'];
    }

    public function rules()
    {
        return [
            [['username', 'accessToken'], 'required']
        ];
    }

    /**
     * Перед определением полей вставим accessToken
     *
     * @param array $data
     * @param null $formName
     *
     * @return bool
     */
    public function load($data, $formName = null)
    {
        $this->generateAccessToken();

        return parent::load($data, $formName);
    }

    /**
     * Рандомный accessToken
     */
    private function generateAccessToken()
    {
        $this->accessToken = md5(uniqid());
    }
}
