<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Email */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'host',
            'port',
            'username',
//            [
//                'attribute' => 'password',
//                'value' => function ($model){
//                    return Yii::$app->getSecurity()->decryptByPassword($model->password, Yii::$app->params['secretKey']);
//                }
//            ],
            'mail:email',
            [
                'attribute' => 'encryption',
                'value' => function ($model){
                    switch ($model->encryption) {
                        case 1: return 'SSL';
                        case 2: return 'TLS';
                    }
                    return 'Отсутсвует';
                }
            ],
        ],
    ]) ?>

</div>
