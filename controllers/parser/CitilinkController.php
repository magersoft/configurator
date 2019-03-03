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
use app\models\Property;
use app\models\PropertyGroup;
use app\models\PropertyRelations;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use GuzzleTor\Middleware;
use phpDocumentor\Reflection\Types\Integer;
use yii\web\Controller;
use GuzzleHttp\Client;

class CitilinkController extends Controller
{
    const STORE_ID = 1;

    private $url = 'https://www.citilink.ru';

    private $api = 'https://api.citilink.ru/v1/product';

    private $region = 'msk_cl:';

    private $torIp = '127.0.0.1:9150';

    /**
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionCategory()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::tor($this->torIp));

        $client = new Client([
            'base_uri' => "{$this->url}/catalog/",
            'handler' => $stack
        ]);

        $res = $client->request('GET', "computers_and_notebooks/parts/", ['http_errors' => false]);

        if ($res->getStatusCode() === 429) {
           \Yii::error('Think about it');
        } else if ($res->getStatusCode() === 200) {
            $body = $res->getBody();

            $document = \phpQuery::newDocumentHTML($body);

            $categories = $document->find('#content > div > div > div.category-content > span.category-content__link-title');

            foreach ($categories as $category) {
                $pq = pq($category);

                preg_match_all('!\d+!', $pq->attr('data-category-id'), $matches);

                $id = (int)$matches[0][0];
                if ($id === 100127) {
                    continue; // skip pc_platform category
                }
                $title = trim($pq->find('a')->text());
                $slug = $pq->find('a')->attr('href');

                $exist_category = Category::findOne(['unique_id' => $id]);

                if ($exist_category) {
                    \Yii::warning(['exist' => $exist_category->id]);
                } else {
                    $model = new Category();
                    $model->unique_id = $id;
                    $model->title = $title;
                    $model->status = 1;
                    $model->slug = $slug;
                    $model->save();
                }
            }
        } else if ($res->getStatusCode() === 301) {
            \Yii::warning($res->getStatusCode());
        } else {
            \Yii::error($res->getStatusCode());
        }

        return $this->render('citilink');
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionCatalog()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::tor($this->torIp));

        $categories = Category::find()->all();

        $client = new Client([
            'handler' => $stack
        ]);

        foreach ($categories as $category) {

            $res = $client->request('GET', $category->slug);

            $body = $res->getBody();

            $document = \phpQuery::newDocumentHTML($body);

            $pages_count = (int)$document->find('.page_listing > section > ul > li.last')->text();

            for ($i = 1; $i <= $pages_count; $i++) {
                self::catalogPages($client, $i, $category->id, $category->slug);
            }
        }

        return $this->render('citilink');
    }

    /**
     * @return string
     * @use Middleware/Tor
     */
    public function actionProduct()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::tor($this->torIp));

        $client = new Client([
            'base_uri' => "{$this->api}/{$this->region}/",
            'handler' => $stack,
        ]);

        $sleeping_time = 60;

        $products = Product::find()->all();

        while ($products) {

            $product = array_shift($products);

            $api = $client->get("{$product->unique_id}/", [
                'http_errors' => false
            ]);

            if ($api->getStatusCode() === 429) {
                self::changeIp($product, $client, $sleeping_time);
            } else if ($api->getStatusCode() === 301) {
                self::notFoundProduct($product);
            } else if ($api->getStatusCode() === 200) {
                self::saveProduct($product, $api);
            } else {
                \Yii::error($api->getStatusCode());
            }
        }

        return $this->render('citilink');
    }

    private static function catalogPages(Client $client, $page_number, $category_id, $category_slug)
    {
        try {
            $res = $client->request('GET', "{$category_slug}?p={$page_number}");

            $body = $res->getBody();

            $document = \phpQuery::newDocumentHTML($body);

            $products = $document->find('#subcategoryList > div.product_category_list > div > div.subcategory-product-item');

            foreach ($products as $product) {
                $pq = pq($product);

                $data = json_decode($pq->attr('data-params'), true);

                $id = (int)$data['id'];
                $short_title = $data['shortName'];
                $link = $pq->find('a')->attr('href');
                $thumbnail = ($pq->find('img')->attr('src')) ? $pq->find('img')->attr('src') : $pq->find('img')->attr('data-src');

                $exists_product = Product::findOne(['unique_id' => $id]);

                if ($exists_product) {
                    ($exists_product->short_title === $short_title) ?: $exists_product->short_title  = $short_title;
                    ($exists_product->thumbnail === $thumbnail) ?: $exists_product->thumbnail = $thumbnail;
                    $exists_product->store_id = self::STORE_ID;
                    $exists_product->save();
                } else {
                    $model = new Product();
                    $model->unique_id = $id;
                    $model->short_title = $short_title;
                    $model->link = $link;
                    $model->thumbnail = $thumbnail;
                    $model->category_id = $category_id;
                    $model->store_id = self::STORE_ID;
                    $model->status = Product::PRODUCT_STATUS_VALUE_PUBLIC;
                    $model->save();
                    $model->validate();
                    \Yii::error($model->errors);
                }

            }
        } catch (GuzzleException $e) {
            \Yii::error($e);
        }
    }

    private static function changeIp(Product $product, Client $client, int $seconds)
    {
        sleep($seconds);

        $sleeping_time = $seconds * 2;

        if ($sleeping_time > 900) {
            $sleeping_time = 900;
        }

        $api = $client->get("{$product->unique_id}", [
            'http_errors' => false,
            'tor_new_identity' => true
        ]);

        if ($api->getStatusCode() === 429) {
            self::changeIp($product, $client, $sleeping_time);
        } else if ($api->getStatusCode() === 301) {
            self::notFoundProduct($product);
        } else if ($api->getStatusCode() === 200) {
            self::saveProduct($product, $api);
        } else {
            \Yii::error($api->getStatusCode());
        }
    }

    private static function saveProduct(Product $product, $api) {
        $response = json_decode($api->getBody(), true);

        $existsProduct = ProductRelations::findOne(['product_id' => $product->id]);

        if ($existsProduct) {
            \Yii::warning(['exist' => $product->id]);
            self::updateProductRelations($existsProduct, $response);
            self::savePropertyGroup($product, $response);
        } else {
            self::saveProductRelations($product, $response);
            self::savePropertyGroup($product, $response);
        }
    }

    private static function saveProductRelations(Product $product, array $response)
    {
        $data = $response['data'];
        $card = $data['card'];
        // todo: for future private methods
        $photos = $data['photos'];
        $stockList = $data['stockList'];
        $stockSummary = $data['stockSummary'];
        $deliveryInfo = $data['deliveryInfo'];
        $mainProperties = $data['mainProperties'];

        if ($card) {
            $model = new ProductRelations();
            $model->product_id = $product->id;
            $model->store_id = $product->store_id;
            $model->regular_price = $card['price'];
            $model->sale_price = $card['fakeOldPrice'];
            $model->club_price = $card['clubPrice'];
            $model->product->title = $card['name'];
            $model->product->short_description = $card['shortCard'];
            $model->product->brand = $card['brand'];
            $model->link('product', $product);
        }
    }

    private static function savePropertyGroup(Product $product, array $response)
    {
        $data = $response['data'];
        $properties = $data['properties'];

        if ($properties) {
            foreach ($properties as $property) {
                $exist = PropertyGroup::findOne(['name' => $property['groupName']]);

                if (!$exist) {
                    $model = new PropertyGroup();
                    $model->name = $property['groupName'];
                    $model->save();
                }

                foreach ($property['items'] as $propertyItem) {
                    $exist = Property::findOne(['name' => $propertyItem['name']]);

                    if (!$exist) {
                        $propertyGroup = PropertyGroup::findOne(['name' => $property['groupName']]);

                        $model = new Property();
                        $model->name = $propertyItem['name'];
                        $model->group_id = $propertyGroup->id;
                        $model->link('group', $propertyGroup);
                    }

                    $findProperty = Property::findOne(['name' => $propertyItem['name']]);

                    if (!PropertyRelations::findOne(['product_id' => $product->id, 'property_id' => $findProperty, 'value' => $propertyItem['value']])) {
                        $model = new PropertyRelations();
                        $model->product_id = $product->id;
                        $model->property_id = $findProperty->id;
                        $model->value = $propertyItem['value'];
                        $model->desc = $propertyItem['desc'];
                        $model->link('product', $product);
                    }
                }
            }
        }
    }

    private static function updateProductRelations(ProductRelations $model, array $response)
    {
        $data = $response['data'];
        $card = $data['card'];

        $model->regular_price = $card['price'];
        $model->sale_price = $card['fakeOldPrice'];
        $model->club_price = $card['clubPrice'];
        $model->save();
    }

    private static function notFoundProduct(Product $product)
    {
        \Yii::error(['lost' => $product->id]);
        $product->status = Product::PRODUCT_STATUS_VALUE_ARCHIVE;
        $product->save();
    }

    public function actionTest()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::tor('127.0.0.1:9150'));

        $client = new Client(['base_uri' => 'https://api.citilink.ru/v1/product/msk_cl:/', 'handler' => $stack]);

        $id = '1068556';

        $response = $client->get("{$id}/", [
            'tor_new_identity' => true
        ]);

        //var_dump($response->getStatusCode());
        echo $response->getBody();

        return $this->render('citilink');

    }
}