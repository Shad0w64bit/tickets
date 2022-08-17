<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Group;
use app\models\User;
use app\models\Permission;

/* @var $this yii\web\View */
/* @var $model app\models\Permission */

$this->title = 'Добавить разрешения';
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="permission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="permission-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'group_id')->dropDownList(
            ArrayHelper::map($availableGroups, 'id', 'name')
        )->label('Группа') ?>

        <?= $form->field($model, 'permission')->checkboxList([
/*            Permission::ACCESS_ASSIGN_GROUP => 'Назначение группе',
            Permission::ACCESS_ASSIGN_USER => 'Назначение сотруднику', */
            Permission::ACCESS_NOTIFY => 'Получать уведомления',
            Permission::ACCESS_CREATE => 'Создание', 
            Permission::ACCESS_READ => 'Чтение',
            Permission::ACCESS_WRITE => 'Запись', 
            Permission::ACCESS_DELETE => 'Удаление',
            Permission::ACCESS_ONLY_THEIR => 'Только свои заявки',
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
