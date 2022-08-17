<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Требуется активация';
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

    <p>Ваша учетная запись не активирована, для активации учетной записи проследуйте по ссылке отправленной на ваш email:</p>
    <p><strong><?= $email ?></strong></p>
    <p>
    <?= Html::a('Отправить снова', ['need-activate', 'email' => $email], [
        'class' => 'btn btn-primary',
        'data' => [
//            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
    </p>

</div>

<?php
