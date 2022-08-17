<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\Group;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'group_id')->dropDownList(
        ArrayHelper::map(Group::find()->all(), 'id', 'name')
    )->label('Группа') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'autocomplete'=>'off']) ?>

    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord) ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
