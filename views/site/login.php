<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
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

    <p>Пожалуйста, заполните следующие поля:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            <?= Html::a('Забыли пароль?', 'recovery-password', ['class' => 'btn btn-link', 'id'=>'recovery-btn']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$('#recovery-btn').on('click', function(){
    //
});
JS;

$this->registerJs($js);