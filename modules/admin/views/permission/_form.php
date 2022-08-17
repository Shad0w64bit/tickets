<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Group;
use app\models\User;
use app\models\Permission;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Permission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="permission-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'group_id')->dropDownList(
        ArrayHelper::map(Group::find()->all(), 'id', 'name')
    )->label('Группа') ?>

    <?= $form->field($model, 'user_id')->dropDownList(
        ArrayHelper::map(User::find()->all(), 'id', 'email')
    )->label('Пользователь') ?>

    <?= $form->field($model, 'permission')->checkboxList([
        Permission::ACCESS_ASSIGN_GROUP => 'Назначение группе', 
        Permission::ACCESS_ASSIGN_USER => 'Назначение сотруднику', 
        Permission::ACCESS_NOTIFY => 'Получать уведомления',
        Permission::ACCESS_CREATE => 'Создание', 
        Permission::ACCESS_READ => 'Чтение',
        Permission::ACCESS_WRITE => 'Запись', 
        Permission::ACCESS_DELETE => 'Удаление',
        Permission::ACCESS_ONLY_THEIR => 'Только свои заявки',
    ]) ?>
    
    <?php //= $form->field($model, 'access')->textInput(['type' => 'number']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
