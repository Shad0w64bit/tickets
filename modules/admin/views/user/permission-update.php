<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Permission;

/* @var $this yii\web\View */
/* @var $model app\models\Permission */

$this->title = 'Update Permission: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="permission-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="permission-form">

        <?php $form = ActiveForm::begin(); ?>

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

</div>
