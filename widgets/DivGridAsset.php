<?php

namespace app\widgets;

use yii\web\AssetBundle;

class DivGridAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/assets/divgrid';
    public $css = [
        'divGrid.css',
    ];
    public $js = [
        'divGrid.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
