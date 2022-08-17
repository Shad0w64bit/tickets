<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Установка пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="site-login" class="col-sm-4 col-sm-offset-4">
    <div class="blur">
        <div class="background"></div>
    </div>

    <h1><?= Html::encode($this->title) ?></h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type ?>" role="alert"><?= $message ?></div>
    <?php endforeach; ?>
        
    <p>Задайте новый пароль:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'set-password-form',
    ]); ?>

        <?= $form->field($model, 'newPassword')->passwordInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'reNewPassword')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Задать', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>