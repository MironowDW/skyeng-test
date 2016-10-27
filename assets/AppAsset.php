<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $js = [
        'js/skyeng-test/Module.js',
        'js/skyeng-test/constants.js',
        'js/skyeng-test/resourses.js',
        'js/skyeng-test/page-step/StepController.js',
        'js/skyeng-test/page-test-start/TestStartController.js',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\AngularAsset',
    ];
}
