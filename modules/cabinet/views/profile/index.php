<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;

$this->title = 'Профиль';

//$this->params['breadcrumbs'][] = ['label' => 'Заявки', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Профиль';

?>
<div class="profile-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="profile-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'email', [
            'template' => "{label}\n<div class=\"input-group\">{input}\n"
                . "<span class=\"input-group-btn\"><button id=\"change-email\" "
                . "class=\"btn btn-primary\" type=\"button\" data-toggle=\"modal\" data-target=\"#change-email-modal\" "
                . "><span class=\"glyphicon glyphicon-pencil\"></span>&nbsp;Изменить</button></span></div>\n{hint}\n{error}"
        ])->textInput([ 'readonly' => true, 'maxlength' => true ]) ?>

        <div class="form-group field-user-password">
            <label class="control-label" for="user-password" style="display: block;">Пароль</label>
            <?= Html::button('Изменить', [
                'class' => 'form-control btn btn-primary',
                'data-toggle' => 'modal',
                'data-target' => '#change-password-modal',
                'style' => 'width:auto;',
            ]) ?>
        </div>

        <div class="row">
            <div class="form-group col-xs-6">
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="form-group col-xs-6">
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <?php /*= $form->field($model, 'status')->dropDownList([
            Ticket::TICKET_STATUS_OPEN  => 'Открыто',
            Ticket::TICKET_STATUS_CLOSE  => 'Закрыто',
        ])->label('Статус') */?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <!-- FORM-CHANGE-PASSWORD-BEGIN -->
    
    <?php
        Modal::begin([
            'header' => '<h4>Смена пароля</h4>',
//                    'toggleButton' => ['label' => 'Изменить', 'class'=>'form-control btn btn-primary', 'style'=>'width:auto;', 'id'=>'user-password'],
            'options' => [
                'id' => 'change-password-modal',
                'tabindex' => false,
            ],
        ]); 
        ?>          

        <?= $this->render('_password', [
            'model' => $changePasswordForm,
        ]) ?>            

    <?php Modal::end(); ?>        
    
    <!-- FORM-CHANGE-PASSWORD-END -->

    <!-- FORM-CHANGE-EMAIL-BEGIN -->

    <?php
        Modal::begin([
            'header' => '<h4>Смена Email</h4>',
//                    'toggleButton' => ['label' => 'Изменить', 'class'=>'form-control btn btn-primary', 'style'=>'width:auto;', 'id'=>'user-password'],
            'options' => [
                'id' => 'change-email-modal',
                'tabindex' => false,
            ],
        ]);
        ?>

        <?= $this->render('_email', [
            'model' => $changeEmailForm,
        ]) ?>

    <?php Modal::end(); ?>

    <!-- FORM-CHANGE-EMAIL-END -->
    
    <?php if ((Yii::$app->user->id === $model->id) 
        || ( Yii::$app->user->identity->isAdmin() 
            && (Yii::$app->user->identity->isStaff() 
            || (Yii::$app->user->identity->isClient() && $model->isClient()) // Test Me
            ))) : ?>            
    
    <div id="user-permission">
    
    <h2>Права доступа</h2>
        
    <?= GridView::widget([
        'dataProvider' => $permissions,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'group_name',
                'label' => 'Группа',
                'value' => 'group.name'
            ],
            [
                'attribute' => 'access',
                'label' => 'Доступ',
                'value' => function ($model) {
                    return $model->getTextPermission();
                }
            ],
        ],
    ]); ?>
    </div>
    <?php endif; ?>

</div>