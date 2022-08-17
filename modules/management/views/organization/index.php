<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrganizationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Организации';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-index">

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
            'inn',
            'name',            

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {user}',
                'buttons' => [
                    'user' => function ($url, $model)
                    {                        
                        return Html::a('<span class="glyphicon glyphicon-user"></span>', 
                            Url::toRoute(['user/', 'organization_id'=>$model->id]));
                    }
                ],
            ],
        ],
    ]); ?>
</div>
