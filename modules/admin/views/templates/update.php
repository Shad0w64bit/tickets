<?php

use yii\helpers\Html;
use app\models\MessageTemplate;

/* @var $this yii\web\View */
/* @var $model app\models\EmailTemplate */

$events = app\models\Event::getAllEvents();

switch($model->type)
{
    case MessageTemplate::TEMPLATE_EMAIL: 
        $protocol = 'Email'; break;
    case MessageTemplate::TEMPLATE_VIBER: 
        $protocol = 'Viber'; break;
    default :
        $protocol = 'Неизвестен';
}

$this->title = $protocol . ': ' . $events[ $model->event ];
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="email-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'formData' => $formData,
    ]) ?>

</div>
