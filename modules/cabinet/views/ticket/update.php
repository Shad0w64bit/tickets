<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Group;
use app\models\Ticket;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */

$this->title = 'Редактирование: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="ticket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="ticket-form">

        <?php $form = ActiveForm::begin(); ?>

        <?php /*= $form->field($model, 'group_id')->dropDownList(
            ArrayHelper::map(Group::find()->all(), 'id', 'name')
        )->label('Отдел') */?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>


        <?php /*= $form->field($model, 'status')->dropDownList([
            Ticket::TICKET_STATUS_OPEN  => 'Открыто',
            Ticket::TICKET_STATUS_CLOSE  => 'Закрыто',
        ])->label('Статус') */?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


</div>
