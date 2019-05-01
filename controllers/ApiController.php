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
use dektrium\user\controllers\RegistrationController;
use dektrium\user\controllers\SecurityController;
use dektrium\user\models\RegistrationForm;
use dektrium\user\models\User;
use dektrium\user\traits\AjaxValidationTrait;
use dektrium\user\traits\EventTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\widgets\ActiveForm;

class ApiController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;
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

    public function actionLogged()
    {
        if (Yii::$app->request->isGet) {
            if (Yii::$app->user->isGuest) {
                return ['logged' => false, 'user' => false];
            } else {
                return ['logged' => true, 'user' => Yii::$app->user->identity];
            }
        } else {
            throw new ForbiddenHttpException();
        }

    }

    /**
     * Login action.
     *
     * @return Response|array
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        $model = Yii::createObject(\dektrium\user\models\LoginForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(SecurityController::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            $this->trigger(SecurityController::EVENT_AFTER_LOGIN, $event);

            return ['result' => 'success', 'user' => Yii::$app->user->identity];
        } else {
            return ['result' => ActiveForm::validate($model)];
        }
    }

    public function actionLogout()
    {
        try {
            $event = $this->getUserEvent(Yii::$app->user->identity);

            $this->trigger(SecurityController::EVENT_BEFORE_LOGOUT, $event);

            Yii::$app->getUser()->logout();

            $this->trigger(SecurityController::EVENT_AFTER_LOGOUT, $event);

            return ['logout' => true];
        } catch (\Exception $e) {
            Yii::error($e);
            return ['logout' => false];
        }
    }

    public function actionRegister()
    {
        $model = \Yii::createObject(RegistrationForm::className());
        $event = $this->getFormEvent($model);

        $this->trigger(RegistrationController::EVENT_BEFORE_REGISTER, $event);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post(), '') && $model->register()) {
            $this->trigger(RegistrationController::EVENT_AFTER_REGISTER, $event);

            return ['result' => 'success'];
        } else {
            return ['result' => ActiveForm::validate($model)];
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
            $result[] = $product->getProductApi();
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

    public function actionGetConfigurations()
    {
        if (!Yii::$app->user->isGuest) {
            $configurations = Configuration::find()->where(['user_id' => Yii::$app->user->identity->getId()])->all();
        }

        return ['configurations' => $configurations];
    }

    public function actionGetConfiguration()
    {
        if (Yii::$app->user->isGuest) {
            $configuration = Configuration::findOne(['token' => Yii::$app->session->getId(), 'status' => Configuration::STATUS_DONE]);
        } else {
            $configuration = Configuration::findOne(['user_id' => Yii::$app->user->identity->getId(), 'status' => Configuration::STATUS_DONE]);
        }
        if (!$configuration) {
            throw new NotFoundHttpException();
        }

        $products = [];

        foreach ($configuration->configurationRelations as $relation) {
            $products[] = $relation->getProduct();
        }

        return ['products' => $products];
    }

    public function actionCreateConfiguration()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException();
        }

        $request = Yii::$app->request->post();

        $config_category = Category::CONFIG_CATEGORY;

        $result = [];

        foreach (Category::find()->where(['status' => Category::STATUS_PUBLIC])->all() as $category) {
            if (in_array($category->id, $config_category)) {
                $result[] = $category->getCategoryApi();
            }
        }

        if (Yii::$app->user->isGuest) {
            $configuration = Configuration::findOne(['token' => Yii::$app->session->getId(), 'status' => Configuration::STATUS_PROCESS]);
        } else {
            $configuration = Configuration::findOne(['user_id' => Yii::$app->user->identity->getId(), 'status' => Configuration::STATUS_PROCESS]);
        }

        if (Yii::$app->request->isGet) {
            if (!$configuration) {
                $configuration = new Configuration();
                $configuration->token = Yii::$app->session->getId();
                $configuration->name = 'New configuration';
                    if (!Yii::$app->user->isGuest) {
                      $configuration->user_id = Yii::$app->user->identity->getId();
                    }
                $configuration->save();
            } else {
                foreach ($result as $key => $value) {
                    foreach ($configuration->configurationRelations as $relation) {
                        $product = Product::findOne(['id' => $relation->product_id]);
                        if ($value['id'] === $product->category_id) {
                          $result[$key] = $product->getProductApi();
                        }
                    }
                }
            }
            return ['result' => $result, 'configuration' => $configuration];
        }
        if (Yii::$app->request->isPost) {
            if ($request['id']) {
              if (!$configuration) {
                  throw new NotFoundHttpException();
              }
              $addProduct = new ConfigurationRelations();
              $addProduct->configuration_id = $configuration->id;
              $addProduct->product_id = $request['id'];
              $addProduct->save();
            }
        }
        if (Yii::$app->request->isDelete) {
            if (!$configuration) {
                throw new NotFoundHttpException();
            }
            $removeProduct = ConfigurationRelations::findOne([
                'configuration_id' => $configuration->id,
                'product_id' => Yii::$app->request->get()['id']
            ]);
            $removeProduct->delete();

            foreach ($result as $key => $value) {
                foreach ($configuration->configurationRelations as $relation) {
                    $product = Product::findOne(['id' => $relation->product_id]);
                    if ($value['id'] === $product->category_id) {
                        $result[$key] = $product->getProductApi();
                    }
                }
            }
            return ['result' => $result];
        }
    }

    /**
     * @throws ForbiddenHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionSaveConfiguration()
    {
        if (!Yii::$app->request->isPost) {
            throw new ForbiddenHttpException();
        }

        $request = Yii::$app->request->post();

        if ($request) {
            $configuration = Configuration::findOne(['id' => $request['id']]);
            $configuration->name = $request['name'];
            $configuration->status = Configuration::STATUS_DONE;
            $configuration->update();
        }
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteConfiguration()
    {
        if (Yii::$app->request->isDelete) {
            $configuration = Configuration::findOne(['id' => Yii::$app->request->get('id')]);
            $configuration->delete();
        }
    }
}
