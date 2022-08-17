<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Сброс пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="site-login" class="col-sm-4 col-sm-offset-4">
    <div class="blur">
        <div class="background"></div>
    </div>

    <h1><?= Html::encode($this->title) ?></h1>

      <?php
        foreach (Yii::$app->session->getAllFlashes() as $type => $message)
        {
            if (is_array($message))
            {
                foreach ($message as $text)
                {
                    echo "<div class=\"alert alert-$type\" role=\"alert\">$text</div>\n";
                }
            } else {
                echo "<div class=\"alert alert-$type\" role=\"alert\">$message</div>\n";
            }
        }
    ?>
        
    <p>Укажите ваш Email:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'recovery-form',
    ]); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Восстановить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>