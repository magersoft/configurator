<?php

namespace app\controllers;

use app\models\Category;
use app\models\Configuration;
use app\models\ConfigurationRelations;
use app\models\Product;
use app\models\ProductRelations;
use app\models\Property;
use app\models\PropertyGroup;
use app\models\PropertyRelations;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class ApiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'login' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        $session->open();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        $result['token'] = \Yii::$app->request->csrfToken;
        return $result;
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post(), '');
        if ($model->login()) {
            return ['result' => 'success', 'user_id' => Yii::$app->user->getId()];
        } else {
            return ['result' => 'error', 'messages' => $model->getFirstErrors()];
        }
    }

    public function actionCategories()
    {
        return Category::find()->all();
    }

    public function actionProducts()
    {
        $request = Yii::$app->request->get();

        unset($request['page']);

        $products = Product::find()
            ->with('productRelations');
            if (isset($request['property_id'])) {
                $products->andWhere(['in', 'id', (new Query())
                    ->select('product_id')
                    ->from('property_relations')
                    ->where($request)
                ]);
            } else {
                $products->where($request);
            }
        $products = new ActiveDataProvider(['query' => $products]);

        $result = [];
        foreach ($products->getModels() as $product) {
            $result[] = [
                'id' => $product->id,
                'short_title' => $product->short_title,
                'thumbnail' => $product->getThumbnail(),
                'regular_price' => $product->productRelations[0]->regular_price,
                'sale_price' => $product->productRelations[0]->sale_price,
                'club_price' => $product->productRelations[0]->club_price,
                'property' => []
            ];
        }
        return ['result' => $result, 'pagination' => $products->getPagination()->getLinks()];
    }

    public function actionProduct()
    {
        $request = Yii::$app->request->post();

        $product = Product::find()
                ->where($request)
                ->with('productRelations', 'propertyRelations')
                ->one();

        return ['product' => $product->getProductApi()];
    }

    public function actionVuex()
    {
        if ($request = Yii::$app->request->post()) {
            $configuration = Configuration::findOne(['token' => Yii::$app->session->getId()]);
            if (!$configuration) {
                $configuration = new Configuration();
                $configuration->token = Yii::$app->session->getId();
                $configuration->save();
            }
            $saveProduct = new ConfigurationRelations();
            $saveProduct->configuration_id = $configuration->id;
            $saveProduct->product_id = $request['id'];
            $saveProduct->save();
        } else if (Yii::$app->request->isGet) {
            $configuration = Configuration::findOne(['token' => Yii::$app->session->getId()]);
            if (!$configuration) {
                return false;
            }
            $products = [];
            $configRelations = ConfigurationRelations::find()->where(['configuration_id' => $configuration->id])->all();
            foreach ($configRelations as $relation) {
                $products[] = [
                    'id' => $relation->product->id,
                    'short_title' => $relation->product->short_title,
                    'thumbnail' => $relation->product->getThumbnail(),
                    'regular_price' => $relation->product->productRelations[0]->regular_price,
                    'sale_price' => $relation->product->productRelations[0]->sale_price,
                    'club_price' => $relation->product->productRelations[0]->club_price,
                ];
            }
            return ['products' => $products];
        } else if (Yii::$app->request->isDelete) {
            $configuration = Configuration::findOne(['token' => Yii::$app->session->getId()]);
            if (!$configuration) {
                return false;
            }
            $removeProduct = ConfigurationRelations::findOne([
                'configuration_id' => $configuration->id,
                'product_id' => Yii::$app->request->get()['id']
            ]);
            $removeProduct->delete();
        } else {
            throw new ForbiddenHttpException();
        }
    }
}
