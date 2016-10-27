<?php

namespace app\services;

use app\models\Test;
use yii\web\ForbiddenHttpException;

class PermissionService
{

    /**
     * Проверяет что владелец токена является владельцем теста
     *
     * @param $accessToken
     * @param $testId
     *
     * @throws ForbiddenHttpException
     */
    public function checkTest($accessToken, $testId)
    {
        $accessTokenService = \Yii::$app->get(AccessTokenService::class);
        $user = $accessTokenService->getUserByAccessToken($accessToken);
        if (!$user) {
            throw new ForbiddenHttpException('Не верный access token');
        }

        $test = Test::findOne(['userId' => $user->id, 'id' => $testId]);
        if (!$test) {
            throw new ForbiddenHttpException('У пользователя нет теста');
        }
    }

    /**
     * Проверяет, что токен соответствует какому-то пользователю
     *
     * @param $accessToken
     *
     * @throws ForbiddenHttpException
     */
    public function checkAccessToken($accessToken)
    {
        $accessTokenService = \Yii::$app->get(AccessTokenService::class);
        $user = $accessTokenService->getUserByAccessToken($accessToken);
        if (!$user) {
            throw new ForbiddenHttpException('Не верный access token');
        }
    }

}