<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены что хотите удалить данного пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Организация',
                'value' => function ($model){
                    return $model->organization->name;
                }
            ],
            'email:email',
            'first_name',
            'last_name',
            [
                'attribute' => 'status',
                'value' => function ($model){
                    $attr = [];                    
                    $attr[] = ($model->status & User::ROLE_ADMIN) ? 'Администратор' : 'Пользователь';
                    if ($model->status & User::ROLE_ONLY_THEIR)
                    {
                        $attr[] = 'Просмотр только своих заявок';
                    }
                    return implode('; ', $attr);
                }
            ],        
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
