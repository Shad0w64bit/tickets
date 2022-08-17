<?php

$this->title = 'Готово!';
?>
<h1>Установка успешно завершена!</h1>
<?php foreach($owners as $owner): ?>
	<li><?= $owner->inn ?> - <?= $owner->name ?></li>
<?php endforeach; ?>