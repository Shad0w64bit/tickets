<?php

namespace app\assets;

use yii\web\AssetBundle;

class MessageAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $sourcePath = '@app/assets/web/Message';
    
    public $css = [
        'css/chat.css',
    ];
    
    public $js = [
        'js/attach.files.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}