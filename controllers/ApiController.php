<?php

namespace app\controllers;

use app\models\Category;
use app\models\Product;
use app\models\ProductRelations;
use app\models\Property;
use app\models\PropertyGroup;
use app\models\PropertyRelations;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\rest\Controller;
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
}
