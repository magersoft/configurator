<?php
/**
 * Created by PhpStorm.
 * User: mager
 * Date: 07.03.2019
 * Time: 21:30
 */
?>

<div>Всего итераций - <strong><?= $data[0]['count'] ?></strong></div>
<div>Блокировок <strong><?= $data[0]['if429'] ?></strong></div>
<div>Не найдено товаров <strong><?= $data[0]['if301'] ?></strong></div>
<div>Сохранено товаров <strong><?= $data[0]['saved'] ?></strong></div>
<div>Время выполнения скрипта <strong><?= number_format($data[0]['time']) ?> ms</strong></div>