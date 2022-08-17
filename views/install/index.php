<?php

use yii\helpers\Html;

$this->title = 'Установка системы заявок';

if (isset($errors))
{
    foreach ($errors as $error)
        echo "<div class=\"alert alert-danger\">$error</div>";
	
	exit();
}

if (!isset($owners))
{
	echo "<div class=\"alert alert-danger\">Организация не найдена в БД</div>";
	exit();
}

?>
<h1>Система заявок установлена!</h1>
<p>Осталось выполнить первоначальную настройку <b>Системы заявок</b> добавив
вашу организацию и первого пользователя.</p>
<?= Html::a('Начать настройку', ['install/add-organization'], ['class'=>'btn btn-primary']) ?>
