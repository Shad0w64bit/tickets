<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'change-password-form',
    'action' => ['change-password'],
    'validationUrl' => ['change-password-validate'],
    'enableAjaxValidation' => true,
]);// 'action' => Url::to(['change-title', 'id' => $model->id]), 'method' => 'post']); ?>

<?= $form->field($model, 'newPassword')->passwordInput(); ?>
<?= $form->field($model, 'reNewPassword')->passwordInput(); ?>
<?= $form->field($model, 'uid')->hiddenInput()->label(false); ?>

<div class="form-group">
    <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']); ?>
    <?= Html::button('Отмена', ['class'=>'btn btn-default', 'data-dismiss'=>'modal']); ?>
</div>

<?php ActiveForm::end();
/*
$this->registerJs('
$("#change-password-form").on("beforeSubmit", function(){
    var data = $(this).serialize();
    $.ajax({
        url: "'.yii\helpers\Url::to(['']).'",
        type: POST,
        data: data,
        success: function(r){
            console.log(r);
        },
        error: function(){
            alert("Ошибка!");
        }
    });
    return false;
});
');
*/