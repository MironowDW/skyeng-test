<?php

namespace app\models;

use app\services\AccessTokenService;
use yii\db\ActiveRecord;

class Test extends ActiveRecord
{

    public function attributes()
    {
        return ['id', 'userId'];
    }

    public function rules()
    {
        return [
            [['userId'], 'required']
        ];
    }

    /**
     * Юерем id пользователя по токену
     *
     * @param array $data
     * @param null $formName
     *
     * @return bool
     */
    public function load($data, $formName = null)
    {
        $accessToken = isset($data['accessToken']) ? $data['accessToken'] : null;

        $accessTokenService = \Yii::$app->get(AccessTokenService::class);
        $user = $accessTokenService->getUserByAccessToken($accessToken);

        $this->userId = $user->id;

        return true;
    }
}
