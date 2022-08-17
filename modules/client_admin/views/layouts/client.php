<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $generators \yii\gii\Generator[] */
/* @var $activeGenerator \yii\gii\Generator */
/* @var $content string */
/*
$menu = [
    [
        'name' => 'Заявки',
        'url' => 'ticket'
    ],
];

    <div class="col-md-3 col-sm-4">
        <div class="list-group">
            <?php
            foreach ($menu as $item) {
                $label = '<i class="glyphicon glyphicon-chevron-right"></i>' . Html::encode($item['name']);
                echo Html::a($label, '/client/' . $item['url'], [
                    'class' => $item['url'] === explode('/', Url::to())[2] ? 'list-group-item active' : 'list-group-item',
                ]);
            }
            ?>
        </div>
    </div>
    */
?>
<?php $this->beginContent('@app/views/layouts/client.php'); ?>
<div class="row">
    <div class="col-md-12 col-sm-8">
        <?= $content ?>
    </div>
</div>
<?php $this->endContent(); ?>
