<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\User;
use app\models\Organization;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Добавить сотрудника';
$this->params['breadcrumbs'][] = ['label' => 'Сотрудники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
        
    <?= $form->field($model, 'organization_id')->dropDownList(
        ArrayHelper::map(Organization::find()->where(
                ['IN', 'id', Yii::$app->params['main_organizations']]
            )->all(), 'id', 'name')
    )->label('Организация') ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="form-group col-xs-6">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="form-group col-xs-6">
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-6">
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="form-group col-xs-6">
            <?= $form->field($model, 'repassword')->passwordInput(['maxlength' => true]) ?>
        </div>
    </div>
        
    <?= $form->field($model, 'permission')->checkboxList([
        User::USER_ACTIVE => 'Активен',
        User::USER_ADMIN  => 'Администратор',
        User::USER_MODER  => 'Управляет пользователями',
        User::USER_BANNED => 'Заблокирован',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
