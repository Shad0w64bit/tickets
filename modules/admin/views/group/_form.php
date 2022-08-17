<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Organization;
use app\models\User;
use app\models\Email;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $form yii\widgets\ActiveForm */

$default = [ 0 => 'По умолчанию'];
$users = $default + ArrayHelper::map(User::find()->all(), 'id', 'fullname');
$email = $default + ArrayHelper::map(Email::find()->all(), 'id', 'mail');
?>

<div class="group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'organization_id')->dropDownList(
        ArrayHelper::map(Organization::find()->all(), 'id', 'name')
    )->label('Организация') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
    
    <?= $form->field($model, 'manager')->dropDownList($users) ?>
    
    <?= $form->field($model, 'email')->dropDownList($email) ?>

    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord) ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
