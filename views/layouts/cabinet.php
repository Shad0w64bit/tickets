<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\modules\admin\AdminAsset;
use app\models\User;

AdminAsset::register($this);
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

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
	
	$nickname = (isset(Yii::$app->user->identity->first_name))
		? Yii::$app->user->identity->first_name
		: substr( Yii::$app->user->identity->email, 0, 
			strpos( Yii::$app->user->identity->email, '@' ) );
	
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => [            
            Yii::$app->user->identity->isAdmin() ? (
                ['label' => 'Администрирование', 'url' => [
                    Yii::$app->user->identity->isUser() ? '/client-admin' : '/admin'
                ]]
            ) : (''),            
            (Yii::$app->user->identity->isModerator() && Yii::$app->user->identity->isStaff()) ? (
                ['label' => 'Управление', 'url' => ['/management']]
            ) : (''),
            ['label' => 'Кабинет', 'url' => ['/cabinet']],
            ['label' => 'Профиль', 'url' => ['/cabinet/profile']],
            '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Выйти (' . $nickname . ')',
                ['class' => 'dropdown-item logout']
            )
            . Html::endForm()
            . '</li>',
        ],
    ]);
    NavBar::end();
    ?>
    
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; IT-Connect <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
