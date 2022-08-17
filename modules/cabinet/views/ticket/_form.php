<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ticket;
use app\models\Category;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */
/* @var $form yii\widgets\ActiveForm */

//$allowCategories = Category::find()->all();
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map(Category::find()->all(), 'id', 'name')
//        $allowCategories
    )->label('Категория') ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>


    <?php /*= $form->field($model, 'status')->dropDownList([
        Ticket::TICKET_STATUS_OPEN  => 'Открыто',
        Ticket::TICKET_STATUS_CLOSE  => 'Закрыто',
    ])->label('Статус') */ ?>

    <?= $form->field($model, 'text')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
