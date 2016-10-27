<?php

namespace app\models;

use app\services\AccessTokenService;
use yii\db\ActiveRecord;

class Test extends ActiveRecord
{

    const STATUS_NEW = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;

    public function attributes()
    {
        return ['id', 'userId', 'status'];
    }

    public function rules()
    {
        return [
            [['userId', 'status'], 'required']
        ];
    }

    /**
     * Берем id пользователя по токену
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
        $this->status = Test::STATUS_NEW;

        return true;
    }

    /**
     * Рейтинг равняется количеству удачных шагов
     */
    public function getRating()
    {
        return Step::find()
            ->andWhere(['status' => Step::STATUS_SUCCESS])
            ->andWhere(['testId' => $this->id])
            ->count();
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
        $array = parent::toArray($fields, $expand, $recursive);
        $array['rating'] = $this->getRating();

        return $array;
    }
}
