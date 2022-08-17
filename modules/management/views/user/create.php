<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use app\models\User;
use app\models\Organization;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Добавить пользователя' . ((isset($organization)) ? ' ' . $organization->name : '');
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
        
    <?php if (isset($organization)): ?>    
    
        <?= $form->field($model, 'organization_id')->hiddenInput()->label(false); ?>

    <?php else: ?>
        <?= $form->field($model, 'organization_id')->dropDownList(
            ArrayHelper::map(Organization::find()->where(
                ['NOT IN', 'id', Yii::$app->params['main_organizations']]
            )->all(), 'id', 'name')
        )->label('Организация') ?>
        
    <?php endif; ?>

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
        
    <?php /*= $form->field($model, 'status')->dropDownList(
            [
                User::ROLE_USER => 'Пользователь', 
                User::ROLE_ADMIN => 'Администратор'
            ]
    ) */?>
        
    <?= $form->field($model, 'permission')->checkboxList([
        User::USER_ACTIVE => 'Активен',
        User::USER_ADMIN  => 'Администратор',
    ]) ?>
        
        

    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
