<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи' . ((isset($organization)) ? ' ' . $organization->name : '');
$this->params['breadcrumbs'][] = $this->title;
    
    
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?= Html::a('Добавить', ((isset($organization))
                ? ['create', 'organization_id' => $organization->id]
                :['create']
            ), ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'organization_id',
/*            [
                'attribute' => 'organization_name',
                'label' => 'Организация',
                'value' => 'organization.name'
            ],*/
            'fullname',
            'email:email',
//            'first_name',
//            'last_name',            
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            [
                'attribute' => 'status',
                'value' => function ($model){
                    $attr = [];
                    !($model->status & User::USER_BANNED) ?: $attr[] = 'Заблокирован';
                    ($model->status & User::USER_ACTIVE) ?: $attr[] = 'Неактивен';
                    !($model->status & User::USER_ADMIN) ?: $attr[] = 'Администратор';

                    return implode('; ', $attr);
                }
            ],
//            'created_at',
            //'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {permission}',
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
