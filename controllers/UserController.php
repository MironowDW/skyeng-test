<?php

namespace app\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{

    public $modelClass = 'app\models\User';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
        ];
    }

}