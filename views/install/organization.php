<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Создание карточки организации';
?>

 <?php $form = ActiveForm::begin(['id' => 'form-org']); ?>

    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'inn') ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>


<?php ActiveForm::end(); ?>