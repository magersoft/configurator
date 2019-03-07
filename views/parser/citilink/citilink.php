<?php
/**
 * @var $count int
 * @var $if429 int
 * @var $if301 int
 * @var $saved int
 * @var $time int
 */

$count = $count ?? null;
$if429 = $if429 ?? null;
$if301 = $if301 ?? null;
$saved = $saved ?? null;
$time = $time ?? null;
?>
<div>Всего итераций - <strong><?= $count ?></strong></div>
<div>Блокировок <strong><?= $if429 ?></strong></div>
<div>Не найдено товаров <strong><?= $if301 ?></strong></div>
<div>Сохранено товаров <strong><?= $saved ?></strong></div>
<div>Время выполнения скрипта <strong><?= number_format($time) ?> ms</strong></div>
