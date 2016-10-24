<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AngularAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $js = [
        '//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js',
        '//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-route.js',
        '//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-resource.js',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
