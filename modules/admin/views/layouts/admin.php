<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

/* @var $this \yii\web\View */
/* @var $generators \yii\gii\Generator[] */
/* @var $activeGenerator \yii\gii\Generator */
/* @var $content string */

$menu = [ 
    [
        'name' => 'Категории',
        'url' => 'category'
    ],
    [
        'name' => 'Группы',
        'url' => 'group'
    ],
];

$menuCompany = [
    [
        'name' => 'Филиалы',
        'url' => 'organization'
    ],
    [
        'name' => 'Сотрудники',
        'url' => 'user'
    ],    
];

$menuEmail = [
    [
        'name' => 'Адреса электронной почты',
        'url' => 'email'
    ],
    [
        'name' => 'Проверка Email',
        'url' => 'email/test'
    ],
    [
        'name' => 'Шаблоны сообщений',
        'url' => 'templates'
    ],
];

function printMenu ($menu) {
    foreach ($menu as $item) {
        if (!isset($item['visible']) || $item['visible'] === true)
        {
            $label = '<i class="glyphicon glyphicon-chevron-right"></i>' . Html::encode($item['name']);
            $link = ($item['url'][0] == '/') ? $item['url'] : '/admin/' . $item['url'];
            $currentUrl = explode('/', explode('?', Url::to())[0] )[2];
            echo Html::a($label, $link, [
                'class' => ($item['url'] === $currentUrl)
                    || (($item['url'] === 'user') && (!isset($currentUrl)))
                ? 'list-group-item active'
                : 'list-group-item',
            ]);
        }
    }
};

//var_dump(Yii::$app->controller->action->id);
//var_dump(Yii::$app->urlManager->parseUrl(Yii::$app->request));

?>
<?php $this->beginContent('@app/views/layouts/cabinet.php'); ?>
<div class="row">
    <div class="col-md-3 col-sm-4">
        <div class="list-group">
            <h2>Общие</h2>
            <?php printMenu($menu) ?>
        </div>
        <div class="list-group">
            <h2>Компания</h2>
            <?php printMenu($menuCompany) ?>
        </div>
        <div class="list-group">
            <h2>Оповещения</h2>
            <?php printMenu($menuEmail) ?>
        </div>
    </div>
    <div class="col-md-9 col-sm-8">
        <?= $content ?>
    </div>
</div>
<?php $this->endContent(); ?>
