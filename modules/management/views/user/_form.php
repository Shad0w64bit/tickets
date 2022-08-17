<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;
use yii\helpers\ArrayHelper;
use app\models\Organization;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'organization_id', [
        'template' => "{label}\n<div class=\"input-group\">{input}\n"
        . "<span class=\"input-group-btn\"><button id=\"unlock-organization-field\" "
        . "class=\"btn btn-default\" type=\"button\" "
        . "><span class=\"glyphicon glyphicon-lock\"></span></button></span></div>\n{hint}\n{error}"])
        ->dropDownList(
            ArrayHelper::map(Organization::find()->where(
                ['NOT IN', 'id', Yii::$app->params['main_organizations']]
            )->all(), 'id', 'name')
        , [
            'id' => 'organization-field',
            'disabled' => true
        ])->label('Организация') ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

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

$js = <<<JS
jQuery.fn.toggleAttr = function (attr) {
    return this.each(function(){
        var self = $(this);
        (self.attr(attr)) ? self.removeAttr(attr) : self.attr(attr,attr);
    });
};
        
$("#unlock-organization-field").on("click", function(){
    $("#organization-field").toggleAttr("disabled");
});
JS;

$this->registerJs($js);