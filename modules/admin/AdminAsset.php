<?php

namespace app\modules\admin;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/admin/assets';
    public $css = [
        'main.css',
    ];
//    public $js = [
//        'gii.js',
//    ];
    public $depends = [
        'app\assets\AppAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\gii\TypeAheadAsset',
    ];
    
}