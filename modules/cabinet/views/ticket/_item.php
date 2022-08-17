<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
?>

<div class="check">
    <input value="<?= $model->id ?>" type="checkbox">
</div>
<div class="id"><?= $model->getID() ?></div>
<div class="title"><?= Html::encode($model->title) ?></div>
<div class="organization">
    <span class="mobile-view">Организация: </span>
    <?=$model->organization->name ?>
</div>
<div class="assign">
    <span class="mobile-view">Назначено: </span>
    <?= (isset($model->assign_to)) ? $model->assigned->fullname : 'Не назначено' ?>
</div>
<div class="status">
    <?= ($model->opened) ? 'Открыто' : 'Закрыто' ?>
</div>
<div class="updated">
    <span class="mobile-view">Обновлено: </span>
    <?= Yii::$app->formatter->asDatetime($model->updated_at) ?>
</div>
<div class="link">
    <a href="<?= Url::to(['view', 'id'=>$model->id]) ?>">Просмотр</a>
</div>