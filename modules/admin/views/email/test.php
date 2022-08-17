<?php

use yii\helpers\ArrayHelper;
use app\models\Email;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Проверка электронной почты';
$this->params['breadcrumbs'][] = ['label' => 'Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="email-form">

        <?php $form = ActiveForm::begin(['method'=>'POST']); ?>

        <?= $form->field($model, 'sendFrom')->dropDownList(
                ArrayHelper::map(Email::find()->all(), 'id', 'mail')
        ); ?>
        
        <?= $form->field($model, 'sendTo')->textInput(['autofocus'=>'true', 'autocomplete' => 'off', 'placeholder' => 'user@example.com', 'maxlength' => true]) ?>
        
        <?= $form->field($model, 'subject')->textInput(['autocomplete' => 'off', 'maxlength' => true]) ?>

        <?= $form->field($model, 'body')->textArea(['rows'=>5]) ?>

        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>