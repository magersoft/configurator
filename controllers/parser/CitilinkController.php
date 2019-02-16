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

        foreach ($categories as $category) {
            $client = new Client();

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
            $regular_price = (int)$data['price'];
            $thumbnail = ($pq->find('img')->attr('src')) ? $pq->find('img')->attr('src') : $pq->find('img')->attr('data-src');

            $exists_product = Product::findOne(['unique_id' => $id]);

            if ($exists_product) {
                ($exists_product->title === $title) ?: $exists_product->title  = $title;
                ($exists_product->regular_price === $regular_price) ?: $exists_product->regular_price =  $regular_price;
                ($exists_product->thumbnail === $thumbnail) ?: $exists_product->thumbnail = $thumbnail;
                $exists_product->store_id = 1;
                $exists_product->save();
            } else {
                $model = new Product();
                $model->unique_id = $id;
                $model->title = $title;
                $model->regular_price = $regular_price;
                $model->thumbnail = $thumbnail;
                $model->category_id = $category_id;
                $model->store_id = 1;
                $model->status = 1;
                $model->save();
            }

        }
    }
}