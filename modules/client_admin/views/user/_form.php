<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //= $form->field($model, 'email')->textInput(['readonly' => true,'maxlength' => true]) ?>
    
    
    <?= $form->field($model, 'email', [
        'template' => "{label}\n<div class=\"input-group\">{input}\n"
        . "<span class=\"input-group-btn\"><button id=\"change-email-btn\" "
        . "class=\"btn btn-default\" type=\"button\" data-toggle=\"modal\" "
        . "data-target=\"#change-email-modal\">Изменить</button></span></div>\n{hint}\n{error}"])
        ->textInput(['readonly' => true,'maxlength' => true]) ?>
    
    <div class="row">
        <div class="form-group col-xs-6">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="form-group col-xs-6">
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>           

    <?= $form->field($model, 'permission')->checkboxList([
//        User::USER_ACTIVE => 'Активен',
        User::USER_ADMIN  => 'Администратор',
//        User::USER_MODER  => 'Управляет пользователями',
        User::USER_BANNED => 'Заблокирован',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
    Modal::begin([
        'header' => '<h4>Изменить Email</h4>',
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