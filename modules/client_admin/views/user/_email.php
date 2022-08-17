<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'change-email-form',
    'action' => ['change-email', 'id' => $model->id],
//    'validationUrl' => ['change-email-validate'],
//    'enableAjaxValidation' => true,
]);// 'action' => Url::to(['change-title', 'id' => $model->id]), 'method' => 'post']); ?>

<?= $form->field($model, 'email')->textInput(); ?>

<div class="form-group">
    <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']); ?>
    <?= Html::button('Отмена', ['class'=>'btn btn-default', 'data-dismiss'=>'modal']); ?>
</div>

<?php ActiveForm::end();