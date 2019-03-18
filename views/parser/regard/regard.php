<?php
/**
 * Created by PhpStorm.
 * User: mager
 * Date: 17.03.2019
 * Time: 11:48
 */

$count = $count ?? null;
$if429 = $if429 ?? null;
$if301 = $if301 ?? null;
$saved = $saved ?? null;
$time = $time ?? null;

$store = $store ?? null;
?>
<div class="row">
    <h1>Парсер</h1>
    <br>
    <a href="https://www.citilink.ru/">
        <img src="https://www.regard.ru/img/logo.png" alt="">
    </a>
    <br>

    <?php if ($store) : ?>
        <div class="col-sm-6">
            <h3>Статус: <?= $store ?></h3>
            Информация о магазине была обновлена
        </div>
    <?php endif; ?>

    <?php if ($count) : ?>
        <div class="col-sm-6">
            <h3>Отчет об парсинге:</h3>
            <ul>
                <li>Всего итераций - <strong><?= $count ?></strong></li>
                <li>Блокировок <strong><?= $if429 ?></strong></li>
                <li>Не найдено товаров <strong><?= $if301 ?></strong></li>
                <li>Сохранено товаров <strong><?= $saved ?></strong></li>
                <li>Время выполнения скрипта <strong><?= number_format($time) ?> ms</strong></li>
            </ul>
        </div>
    <?php endif; ?>
</div>
<br>
<div class="row">
    <div class="col-sm-3">
        <form action="<?= \yii\helpers\Url::to(['parser/regard/store']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-success">Проверить доступность</button>
        </form>
    </div>
    <div class="col-sm-3">
        <form action="<?= \yii\helpers\Url::to(['parser/regard/category']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-default">Спарсить все категории</button>
        </form>
    </div>
    <div class="col-sm-3">
        <form action="<?= \yii\helpers\Url::to(['parser/regard/catalog']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-default">Спарсить весь каталог</button>
        </form>
    </div>
    <div class="col-sm-3">
        <form action="<?= \yii\helpers\Url::to(['parser/regard/product/']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-danger">Спарсить все данные товаров</button>
        </form>
    </div>
</div>