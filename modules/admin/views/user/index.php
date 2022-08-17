<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сотрудники';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

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
            [
                'attribute' => 'organization_name',
                'label' => 'Организация',
                'value' => 'organization.name'
            ],
            'fullname',
            'email:email',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete} {permission}',
                'buttons' => [
                    'permission' => function ($url, $model)
                    {
                        return Html::a('<span class="glyphicon glyphicon-lock"></span>', $url);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
