<?php
/**
 * Created by PhpStorm.
 * User: mager
 * Date: 16.02.2019
 * Time: 20:43
 */

namespace app\controllers\parser;


use app\components\NotifyEmail;
use app\models\Category;
use app\models\Product;
use app\models\ProductRelations;
use app\models\ProductStock;
use app\models\Property;
use app\models\PropertyGroup;
use app\models\PropertyRelations;
use app\models\Stock;
use app\models\Store;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleTor\Middleware;
use yii\filters\VerbFilter;
use yii\web\Controller;
use GuzzleHttp\Client;
use yii\web\ForbiddenHttpException;

class CitilinkController extends Controller
{
    const STORE_ID = Store::CITILINK;

    private $url = 'https://www.citilink.ru';

    private $api = 'https://api.citilink.ru/v1/product';

    private $region = 'msk_cl:';

    private $torIp = '127.0.0.1:9150';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'store' => ['post'],
                    'category' => ['post'],
                    'catalog' => ['post'],
                    'product' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $category_count = Category::find()->count();
        $product_count = Product::find()->count();
        $product_relations_count = ProductRelations::find()->count();
        $stock_count = Stock::find()->count();

        return $this->render('citilink', [
            'product_count' => $product_count,
            'category_count' => $category_count,
            'product_relations_count' => $product_relations_count,
            'stock_count' => $stock_count
        ]);
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function actionStore()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::tor($this->torIp));

        $client = new Client([
            'handler' => $stack
        ]);

        $res = $client->request('GET', $this->url, ['http_errors' => false]);

        if ($res->getStatusCode() === 200) {
            $exist = Store::findOne(['id' => self::STORE_ID]);

            if (!$exist) {
                $model = new Store();
                $model->name = 'Ситилинк';
                $model->url = $this->url;
                $model->save();
            }
        } else {
            \Yii::error($res->getStatusCode());
        }

        return $this->render('citilink', ['store' => $res->getStatusCode()]);

        // todo: Save more info about store
    }

    /**
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionCategory()
    {
        if (\Yii::$app->request->isPost) {
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

            return $this->render('citilink', ['categories' => $categories]);
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionCatalog()
    {
        if (\Yii::$app->request->isPost) {

            $request = \Yii::$app->request->post();

            $start = microtime(true);

            $stack = new HandlerStack();
            $stack->setHandler(new CurlHandler());
            $stack->push(Middleware::tor($this->torIp));

            if (!empty($request['category_id'])) {
                $categories = Category::find()->where(['id' => (int)$request['category_id']])->all();
            } else {
                $categories = Category::find()->all();
            }

            $client = new Client([
                'base_uri' => "{$this->api}s/{$this->region}/",
                'handler' => $stack
            ]);

            /**
             * get Debug info
             */
            $count_iteration = 0;
            $if429 = 0;
            $if301 = 0;
            $saved_product = 0;

            foreach ($categories as $category) {
                $count_iteration++;
                $generate_url = str_replace('https://www.citilink.ru/catalog/', '', $category->slug);

                $i = 1;
                while (true) {
                    $i++;

                    $api = $client->get("{$generate_url}?page={$i}", [
                        'http_errors' => false
                    ]);

                    $response = json_decode($api->getBody(), true);

                    if ($response['code'] === 404 || $response['code'] === 4001) {
                        break;
                    }

                    if ($api->getStatusCode() === 429) {
                        $if429++;
                        self::changeIpCatalog($category, $client, 60, $generate_url, $i);
                    } else if ($api->getStatusCode() === 301) {
                        $if301++;
                    } else if ($api->getStatusCode() === 200) {
                        $saved_product++;
                        self::saveCatalog($category, $response);
                    } else {
                        \Yii::error($api->getStatusCode());
                    }
                }
            }

            NotifyEmail::sendParserInfo([
                'count' => $count_iteration,
                'if429' => $if429,
                'if301' => $if301,
                'saved' => $saved_product,
                'time' => microtime(true) - $start
            ]);

            return $this->render('citilink', [
                'count' => $count_iteration,
                'if429' => $if429,
                'if301' => $if301,
                'saved' => $saved_product,
                'time' => microtime(true) - $start
            ]);
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @return string
     * @use Middleware/Tor
     */
    public function actionProduct()
    {
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request->post();

            $start = microtime(true);

            $stack = new HandlerStack();
            $stack->setHandler(new CurlHandler());
            $stack->push(Middleware::tor($this->torIp));

            $client = new Client([
                'base_uri' => "{$this->api}/{$this->region}/",
                'handler' => $stack,
            ]);

            $sleeping_time = 60;

            if (!empty($request['limit']) && empty($request['begining_id'])) {
                $products = Product::find()->limit((int)$request['limit'])->all();
            } else if (!empty($request['unique_id'])) {
                $products = Product::find()->where(['unique_id' => (int)$request['unique_id']])->all();
            } else if (!empty($request['begining_id'] && empty($request['limit']))) {
                $products = Product::find()->where(['>', 'id', (int)$request['begining_id']])->orderBy('id')->all();
            } else if (!empty($request['begining_id']) && !empty($request['limit'])) {
                $products = Product::find()->where(['>', 'id', (int)$request['begining_id']])->limit((int)$request['limit'])->orderBy('id')->all();
            } else {
                $products = Product::find()->all();
            }

            /**
             * get Debug info
             */
            $count_iteration = 0;
            $if429 = 0;
            $if301 = 0;
            $saved_product = 0;

            while ($products) {

                $product = array_shift($products);

                $api = $client->get("{$product->unique_id}/", [
                    'http_errors' => false
                ]);

                $count_iteration++;

                if ($api->getStatusCode() === 429) {
                    $if429++;
                    self::changeIpProduct($product, $client, $sleeping_time);
                } else if ($api->getStatusCode() === 301) {
                    $if301++;
                    self::notFoundProduct($product);
                } else if ($api->getStatusCode() === 200) {
                    $saved_product++;
                    self::saveProduct($product, $api);
                } else {
                    \Yii::error($api->getStatusCode());
                }
            }

            NotifyEmail::sendParserInfo([
                'count' => $count_iteration,
                'if429' => $if429,
                'if301' => $if301,
                'saved' => $saved_product,
                'time' => microtime(true) - $start
            ]);

            return $this->render('citilink', [
                'count' => $count_iteration,
                'if429' => $if429,
                'if301' => $if301,
                'saved' => $saved_product,
                'time' => microtime(true) - $start
            ]);
        } else {
            throw new ForbiddenHttpException();
        }
    }



    /**
     * Static methods for CRUD
     *
     */
    private static function changeIpCatalog(Category $category, Client $client, int $seconds, $generated_url, int $i)
    {
        sleep($seconds);

        $sleeping_time = $seconds * 2;

        if ($sleeping_time > 900) {
            $sleeping_time = 900;
        }

        $api = $client->get("{$generated_url}?page={$i}", [
            'http_errors' => false,
            'tor_new_identity' => true
        ]);

        if ($api->getStatusCode() === 429) {
            self::changeIpCatalog($category, $client, $sleeping_time, $generated_url, $i);
        } else if ($api->getStatusCode() === 301) {

        } else if ($api->getStatusCode() === 200) {
            $response = json_decode($api->getBody(), true);
            self::saveCatalog($category, $response);
        } else {
            \Yii::error($api->getStatusCode());
        }
    }

    private static function changeIpProduct(Product $product, Client $client, int $seconds)
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
            self::changeIpProduct($product, $client, $sleeping_time);
        } else if ($api->getStatusCode() === 301) {
            self::notFoundProduct($product);
        } else if ($api->getStatusCode() === 200) {
            self::saveProduct($product, $api);
        } else {
            \Yii::error($api->getStatusCode());
        }
    }

    private static function saveCatalog(Category $category, array $response)
    {
        $data = $response['data'];
        $products = $data['items'];

        if (!empty($products)) {
            foreach ($products as $product) {
                $exist = Product::findOne(['unique_id' => (int)$product['id']]);

                if (!$exist) {
                    $model = new Product();
                    $model->unique_id = (int)$product['id'];
                    $model->short_title = $product['shortName'];
                    $model->link = 'https://www.citilink.ru/catalog/' . $product['categoryPath'] . '/' . $product['id'];
                    $model->thumbnail = $product['imageName'];
                    $model->category_id = $category->id;
                    $model->store_id = self::STORE_ID;
                    $model->status = Product::PRODUCT_STATUS_VALUE_PUBLIC;
                    $model->save();
                } else {
                    $exist->thumbnail = $product['imageName'];
                    $exist->short_title = $product['shortName'];
                    $exist->link = 'https://www.citilink.ru/catalog/' . $product['categoryPath'] . '/' . $exist->unique_id;
                    $exist->save();
                }
            }

        }
    }

    private static function saveProduct(Product $product, $api) {
        $response = json_decode($api->getBody(), true);

        $existsProduct = ProductRelations::findOne(['product_id' => $product->id]);

        self::saveStock($response);

        if ($existsProduct) {
            self::updateProduct($product, $response);
            self::updateProductRelations($existsProduct, $response);
            self::updateStockSummary($product, $response);
        } else {
            self::saveProductRelations($product, $response);
            self::savePropertyGroup($product, $response);
            self::saveStockSummary($product, $response);
        }
    }

    private static function saveProductRelations(Product $product, array $response)
    {
        $data = $response['data'];
        $card = $data['card'];
        // todo: for future private methods
        $photos = $data['photos'];
        $deliveryInfo = $data['deliveryInfo'];
        $mainProperties = $data['mainProperties'];

        if ($card) {
            $model = new ProductRelations();
            $model->product_id = $product->id;
            $model->store_id = $product->store_id;
            $model->regular_price = $card['price'];
            $model->sale_price = $card['fakeOldPrice'];
            $model->club_price = $card['clubPrice'];
            $model->link('product', $product);

            $product->title = $card['name'];
            $product->short_description = $card['shortCard'];
            $product->brand = $card['brandName'];
            $product->status = Product::PRODUCT_STATUS_VALUE_PUBLIC;
            $product->save();
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

    private static function saveStock(array $response)
    {
        $data = $response['data'];
        $stocks = $data['stockList'];

        if ($stocks) {
            foreach ($stocks as $stock) {
                $exist = Stock::findOne(['name' => $stock['name']]);

                if (!$exist) {
                    $model = new Stock();
                    $model->name = $stock['name'];
                    $model->type = $stock['stock']['type'];
                    $model->index = $stock['stock']['index'];
                    $model->store_id = self::STORE_ID;
                    $model->save();
                }
            }
        }

    }

    private static function saveStockSummary(Product $product, array $response)
    {
        $data = $response['data'];
        $stocksSummary = $data['stockSummary'];

        if ($stocksSummary) {
            foreach ($stocksSummary as $stock) {
                $findStock = Stock::findOne(['name' => $stock['name']]);
                $exist = ProductStock::findOne([
                    'product_id' => $product->id,
                    'stock_id' => $findStock->id,
                    'count' => $stock['count']
                ]);

                if (!$exist) {
                    $model = new ProductStock();
                    $model->product_id = $product->id;
                    $model->stock_id = $findStock->id;
                    $model->count = $stock['count'];
                    $model->save();
                }
            }
        }
    }

    private static function updateProduct(Product $model, array $response)
    {
        $data = $response['data'];
        $card = $data['card'];

        if ($card) {
            $model->title = $card['name'];
            $model->short_description = $card['shortCard'];
            $model->brand = $card['brandName'];
            $model->status = Product::PRODUCT_STATUS_VALUE_PUBLIC;
            $model->save();
        }
    }

    private static function updateProductRelations(ProductRelations $model, array $response)
    {
        $data = $response['data'];
        $card = $data['card'];

        if ($card) {
            $model->regular_price = $card['price'];
            $model->sale_price = $card['fakeOldPrice'];
            $model->club_price = $card['clubPrice'];
            $model->save();
        }
    }

    private static function updateStockSummary(Product $model, array $response)
    {
        $data = $response['data'];
        $stocksSummary = $data['stockSummary'];

        if ($stocksSummary) {
            foreach ($stocksSummary as $stock) {
                $findStock = Stock::findOne(['name' => $stock['name']]);
                $productStock = ProductStock::findOne(['product_id' => $model->id, 'stock_id' => $findStock]);
                if ($productStock) {
                    $productStock->count = $stock['count'];
                    $productStock->save();
                } else {
                    self::saveStockSummary($model, $response);
                }
            }
        }
    }

    private static function notFoundProduct(Product $product)
    {
        \Yii::error(['lost' => $product->id]);
        $product->status = Product::PRODUCT_STATUS_VALUE_ARCHIVE;
        $product->save();
    }
}