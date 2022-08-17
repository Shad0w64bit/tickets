<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

$menu = [ 
    [
        'name' => 'Организации',
        'url' => 'organization',
    ],
    [
        'name' => 'Пользователи',
        'url' => 'user',
    ],
/*    [
        'name' => 'Панель пользователя',
        'url' => '/staff'
    ],    
    [
        'name' => 'Сотрудники',
        'url' => 'user'
    ],
    [
        'name' => 'Категории',
        'url' => 'category'
    ],
    [
        'name' => 'Группы',
        'url' => 'group'
    ],*/
];

function printMenu ($menu) {
    foreach ($menu as $item) {
        if (!isset($item['visible']) || $item['visible'] === true)
        {
            $label = '<i class="glyphicon glyphicon-chevron-right"></i>' . Html::encode($item['name']);
            $link = ($item['url'][0] == '/') ? $item['url'] : '/management/' . $item['url'];
            $currentUrl = explode('/', explode('?', Url::to())[0] )[2];
            echo Html::a($label, $link, [
                'class' => ($item['url'] === $currentUrl)
                    || (($item['url'] === 'organization') && (!isset($currentUrl)))
                ? 'list-group-item active'
                : 'list-group-item',
            ]);
        }
    }
};

?>
<?php $this->beginContent('@app/views/layouts/cabinet.php'); ?>
<div class="row">
    <div class="col-md-3 col-sm-4">
        <div class="list-group">
            <h2>Меню</h2>
            <?php printMenu($menu) ?>
        </div>
    </div>
    <div class="col-md-9 col-sm-8">
        <?= $content ?>
    </div>
</div>
<?php $this->endContent(); ?>
