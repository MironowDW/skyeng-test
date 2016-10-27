<?php

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Выводит angular приложение
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderContent(null);
    }
}
