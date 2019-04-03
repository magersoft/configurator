<?php
/**
 * @var $count int
 * @var $if429 int
 * @var $if301 int
 * @var $saved int
 * @var $time int
 * @var $product_count int
 * @var $store int
 */

$count = $count ?? null;
$if429 = $if429 ?? null;
$if301 = $if301 ?? null;
$saved = $saved ?? null;
$time = $time ?? null;

$category_count = $category_count ?? null;
$product_count = $product_count ?? null;
$product_relations_count = $product_relations_count ?? null;
$stock_count = $stock_count ?? null;
$store = $store ?? null;

$categories = $categories ?? null;
?>

<div class="row">
    <h1>Парсер</h1>
    <br>
    <a href="https://www.citilink.ru/">
        <img src="/uploads/citilink.png" alt="">
    </a>
    <br>
    <?php if ($product_count) : ?>
    <div class="col-sm-6">
        <h3>Сейчас в БД:</h3>
        <ul>
            <li>Всего категорий - <strong><?= $category_count ?></strong></li>
            <li>Всего продуктов - <strong><?= $product_count ?></strong></li>
            <li>Всего цен продуктов - <strong><?= $product_relations_count ?></strong></li>
            <li>Всего складов - <strong><?= $stock_count ?></strong></li>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ($store) : ?>
    <div class="col-sm-6">
        <h3>Статус: <?= $store ?></h3>
        Информация о магазине была обновлена
    </div>
    <?php endif; ?>

    <?php if ($categories) : ?>
        <div class="col-sm-6">
            Получены следующие категории:
            <div><?= $categories ?></div>
        </div>
    <?php endif;?>

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
        <form action="<?= \yii\helpers\Url::to(['parser/citilink/store']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-success">Проверить доступность</button>
        </form>
    </div>
    <div class="col-sm-3">
        <form action="<?= \yii\helpers\Url::to(['parser/citilink/category']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-default">Спарсить все категории</button>
        </form>
    </div>
    <div class="col-sm-3">
        <form action="<?= \yii\helpers\Url::to(['parser/citilink/catalog']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-default">Спарсить весь каталог</button>
        </form>
    </div>
    <div class="col-sm-3">
        <form action="<?= \yii\helpers\Url::to(['parser/citilink/product/']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-danger">Спарсить все данные товаров</button>
        </form>
    </div>
</div>
<br>
<h2>Парсинг товаров</h2>
<div class="row">
    <div class="col-sm-12">

        <form action="<?= \yii\helpers\Url::to(['parser/citilink/product/']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <div class="form-group">
                <label for="limit">Сколько товаров спарсить?</label>
                <input type="text" class="form-control" id="limit" name="limit" placeholder="Количество">
            </div>
            <div class="form-group">
                <label for="begining_id">С какого товара начать парсинг?</label>
                <input type="text" class="form-control" id="begining_id" name="begining_id" placeholder="ID товара в моей базе">
            </div>
            <div class="form-group">
                <label for="unique_id">Уникальный ID ситилнка</label>
                <input type="text" class="form-control" id="unique_id" name="unique_id" placeholder="ID товара в ситилнке">
            </div>
            <button type="submit" class="btn btn-default">Мне повезет</button>
        </form>
    </div>
</div>
<br>
<h2>Парсинг каталога</h2>
<div class="row">
    <div class="col-sm-12">
        <form action="<?= \yii\helpers\Url::to(['parser/citilink/catalog']) ?>" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <div class="form-group">
                <label for="category_id">Какую категорию спарсить?</label>
                <input type="text" class="form-control" id="category_id" name="category_id" placeholder="ID категории в моей базе">
            </div>
            <button type="submit" class="btn btn-default">Мне повезет</button>
        </form>
    </div>
</div>
<br>