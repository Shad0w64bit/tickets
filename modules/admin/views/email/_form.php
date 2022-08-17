<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Email */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'port')->textInput(['numeric'=>true, 'maxlength' => true]) ?>
    
    <?= $form->field($model, 'encryption')->dropDownList([
        0 => 'Отсутсвует',
        1 => 'SSL',
        2 => 'TLS'
    ]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'newPassword')->passwordInput(['placeholder'=>'Не изменять','maxlength' => true]) ?>

    <?= $form->field($model, 'mail')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
