<?php
/**
 * Created by PhpStorm.
 * User: mager
 * Date: 16.02.2019
 * Time: 20:43
 */

namespace app\controllers\parser;


use app\models\Category;
use app\models\Product;
use app\models\ProductRelations;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use yii\web\Controller;
use GuzzleHttp\Client;

class CitilinkController extends Controller
{

    private $url = 'https://www.citilink.ru';

    public function actionCategory()
    {
        $client = new Client();

        $res = $client->request('GET', "{$this->url}/catalog/computers_and_notebooks/parts/");

        $body = $res->getBody();

        $document = \phpQuery::newDocumentHTML($body);

        $categories = $document->find('#content > div > div > div.category-content > span.category-content__link-title');

        foreach ($categories as $category) { // todo: delete pc_platform category
            $pq = pq($category);

            preg_match_all('!\d+!', $pq->attr('data-category-id'), $matches);

            $id = (int)$matches[0][0];
            $title = trim($pq->find('a')->text());
            $slug = $pq->find('a')->attr('href');

            $exist_category = Category::findOne(['unique_id' => $id]);

            if ($exist_category) {
                return false;
            } else {
                $model = new Category();
                $model->unique_id = $id;
                $model->title = $title;
                $model->status = 1;
                $model->slug = $slug;
                $model->save();
            }
        }

        return $this->render('citilink');
    }

    public function actionCatalog()
    {

        $categories = Category::find()->all();

        $client = new Client();

        foreach ($categories as $category) {

            $res = $client->request('GET', $category->slug);

            $body = $res->getBody();

            $document = \phpQuery::newDocumentHTML($body);

            $pages_count = (int)$document->find('.page_listing > section > ul > li.last')->text();

            for ($i = 1; $i <= $pages_count; $i++) {
                self::parseCatalogPage($i, $category->id, $category->slug);
            }
        }

        return $this->render('citilink');
    }


    private static function parseCatalogPage($page_number, $category_id, $category_slug)
    {
        $client = new Client();

        $res = $client->request('GET', "{$category_slug}?p={$page_number}");

        $body = $res->getBody();

        $document = \phpQuery::newDocumentHTML($body);

        $products = $document->find('#subcategoryList > div.product_category_list > div > div.subcategory-product-item');

        foreach ($products as $product) {
            $pq = pq($product);

            $data = json_decode($pq->attr('data-params'), true);

            $id = (int)$data['id'];
            $title = $data['shortName'];
            $link = $pq->find('a')->attr('href');
            $thumbnail = ($pq->find('img')->attr('src')) ? $pq->find('img')->attr('src') : $pq->find('img')->attr('data-src');

            $exists_product = Product::findOne(['unique_id' => $id]);

            if ($exists_product) {
                ($exists_product->title === $title) ?: $exists_product->title  = $title;
                ($exists_product->thumbnail === $thumbnail) ?: $exists_product->thumbnail = $thumbnail;
                $exists_product->store_id = 1;
                $exists_product->save();
            } else {
                $model = new Product();
                $model->unique_id = $id;
                $model->title = $title;
                $model->link = $link;
                $model->thumbnail = $thumbnail;
                $model->category_id = $category_id;
                $model->store_id = 1;
                $model->status = 1;
                $model->save();
                $model->validate();
                \Yii::error($model->errors);
            }

        }
    }

    public function actionPrice()
    {
        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory());

        $client = new Client([
            'base_uri' => 'https://api.citilink.ru/v1/product/msk_cl:/',
            'handler' => $stack,
            'headers' => "{'User-agent': 'your bot 0.1'}"
        ]);

        // todo: короче тут можно сказать гузлу, не агриться на 429 ответ
        // сделать while и заставить скрипт спать, пока 429-ая

        $products = Product::find()->all();

        foreach ($products as $product) {
            $api = $client->get("{$product->unique_id}", [
                'default_retry_multiplier' => 2.5,
                'retry_on_timeout' => true,
                'connect_timeout' => 120,
                'timeout' => 120,
            ]);

            $response = json_decode($api->getBody(), true);

            $data = $response['data'];

            $card = $data['card'];

            $regular_price = $card['price'];
            $sale_price = $card['fakeOldPrice'];
            $club_price = $card['clubPrice'];

            $exists_product_relations = ProductRelations::findOne(['store_id' => $product->store_id, 'regular_price' => $regular_price, 'sale_price' => $sale_price, 'club_price' => $club_price]);

            if ($exists_product_relations) {
                continue;
            } else {
                $model = new ProductRelations();
                $model->product_id = $product->id;
                $model->store_id = $product->store_id;
                $model->regular_price = $regular_price;
                $model->sale_price = $sale_price;
                $model->club_price = $club_price;
                $model->link('product', $product);
            }

        }

        return $this->render('citilink');
    }

    public function actionTest()
    {
        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory());

        $client = new Client([
            'base_uri' => 'https://api.citilink.ru/v1/product/msk_cl:/',
            'handler' => $stack
        ]);

        $product = Product::findOne(['id' => 2175]);

        $api = $client->get("1031618", [
            'default_retry_multiplier' => 2.5,
            'retry_on_timeout' => true,
            'connect_timeout' => 120,
            'timeout' => 120,
        ]);

        $response = json_decode($api->getBody(), true);

        $data = $response['data'];

        $card = $data['card'];

        $regular_price = $card['price'];
        $sale_price = $card['fakeOldPrice'];
        $club_price = $card['clubPrice'];

        $exists_product_relations = ProductRelations::findOne(['store_id' => $product->store_id, 'regular_price' => $regular_price, 'sale_price' => $sale_price, 'club_price' => $club_price]);

        if ($exists_product_relations) {

        } else {
            $model = new ProductRelations();
            $model->product_id = $product->id;
            $model->store_id = $product->store_id;
            $model->regular_price = $regular_price;
            $model->sale_price = $sale_price;
            $model->club_price = $club_price;
            $model->link('product', $product);
        }

//        $model = new Product();
//        $model->unique_id = 1488;
//        $model->title = 'test';
//        $model->thumbnail = 'thumb';
//        $model->category_id = 20;
//        $model->store_id = 1;
//        $model->status = 1;
//        $model->save();
//        $model->validate();
//        \Yii::error($model->errors);
//        $relations = new ProductRelations();
//        $relations->product_id = $model->id;
//        $relations->regular_price = '123';
//        $relations->store_id = 1;
//        $relations->save();
//        $relations->validate();
//        \Yii::error($relations->errors);


        return $this->render('citilink');

    }
}