<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Изменить';
$this->params['breadcrumbs'][] = ['label' => 'Сотрудники', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $model->fullname;
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'changePasswordForm' => $changePasswordForm,
    ]) ?>

</div>
