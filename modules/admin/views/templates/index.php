<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\MessageTemplate;
use app\models\Event;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EmailTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шаблоны сообщений';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="email-template-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //= Html::a('Create Email Template', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Sync', ['sync'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'type',
                'filter' => [
                    MessageTemplate::TEMPLATE_EMAIL => 'Email',
                    MessageTemplate::TEMPLATE_VIBER => 'Viber',
                ],
                'value' => function ($model) {
                    switch ($model->type)
                    {
                        case MessageTemplate::TEMPLATE_EMAIL:
                            return 'Email';
                        case MessageTemplate::TEMPLATE_VIBER:
                            return 'Viber';
                    }
                    return 'Unknown';
                }
            ],
            [
                'attribute' => 'event',
                'filter' => Event::getAllEvents(),
                'value' => function ($model) {
                    return Event::getAllEvents()[$model->event];
                }
            ],
//            'event',
//            'subject',
//            'plain',
//            'html',

            [
                'class' => 'yii\grid\ActionColumn',
/*                'buttons' => [
                    'duplicate' => function($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-duplicate"></span>', $url, [
                            'title' => 'Duplicate', 
                            'data-pjax' => 0,
                        ] );
                    }
                ],                */
                //'template' => '{view} {update} {delete} {duplicate}',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>
