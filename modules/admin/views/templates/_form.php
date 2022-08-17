<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\MessageTemplate;
use app\models\Event;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\EmailTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-template-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?php /*= $form->field($model, 'type')->dropDownList([
        MessageTemplate::TEMPLATE_EMAIL => 'Email',
        MessageTemplate::TEMPLATE_VIBER => 'Viber',
    ], ['readOnly'=>true]) ?>

    <?php //= $form->field($model, 'event')->dropDownList(Event::getAllEvents()) ?>
    
    <?= $form->field($model, 'event')->widget(Select2::classname(), [
        'data' => Event::getAllEvents(),
        'pluginOptions' => [
            'templateSelection' => new yii\web\JsExpression('function (model){ var group = $("option[value=" + model.id + "]").closest("optgroup").attr("label");  return group + " > " + model.text;}'),
        ]
    ], ['readOnly'=>true]) */?>
    
    <?= $formData->getFields($form); ?>
    <?php /*
    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'plain')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'html')->textarea(['maxlength' => true]) ?>
?>*/
    ?> 
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
