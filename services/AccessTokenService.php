<?php

namespace app\services;

use app\models\User;

class AccessTokenService
{

    /**
     * @param $accessToken
     *
     * @return null|User
     */
    public function getUserByAccessToken($accessToken)
    {
        if (!$accessToken) {
            return null;
        }

        $user = User::findOne(['accessToken' => $accessToken]);
        if (!$user) {
            return null;
        }

        return $user;
    }
}