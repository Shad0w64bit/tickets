<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Разрешения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="permission-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'group_id',
            [
                'attribute' => 'group_name',
                'label' => 'Группа',
                'value' => 'group.name'
            ],
//            'user_id',
            [
                'attribute' => 'user_email',
                'label' => 'Пользователь',
                'value' => 'user.email'
            ],
            [
                'attribute' => 'access',
                'label' => 'Доступ',
                'value' => function ($model) {
                    return $model->getTextPermission();
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                
            ],
        ],
    ]); ?>
</div>
