<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

/* @var $this \yii\web\View */
/* @var $generators \yii\gii\Generator[] */
/* @var $activeGenerator \yii\gii\Generator */
/* @var $content string */
?>
<?php $this->beginContent('@app/views/layouts/cabinet.php'); ?>
<div class="row">
    <div class="col-md-12 col-sm-8">
        <?= $content ?>
    </div>
</div>
<?php $this->endContent(); ?>
