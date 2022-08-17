<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<section class="outer-wrapper">
    <div class="inner-wrapper">
        <div class="container">
            <div class="row">
                <?= $content ?>
            </div>
        </div>
    </div>
</section>

<?php 
$this->registerCss('
 html, body { height:100%;  }

.outer-wrapper { 
display: table;
width: 100%;
height: 100%;
  background-image: url("/img/background.png");
  background-size: cover;
}

.inner-wrapper {
  display:table-cell;
  vertical-align:middle;
  padding:15px;  
}

#site-login {
 background-color: rgba(255,255,255, 0.7);
  border-radius: 10px;
  padding-bottom: 10px;
    box-shadow: 0 0 50px 5px rgba(0,0,0,0.3), 0 0 5px -1px white;  
}

.blur {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: -1;
  filter: blur(7px);
}
');
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
