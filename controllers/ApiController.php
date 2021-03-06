<?php

namespace app\controllers;

use app\components\SelectionComponents;
use app\models\Category;
use app\models\Configuration;
use app\models\ConfigurationRelations;
use app\models\Product;
use app\models\ProductRelations;
use app\models\Property;
use app\models\PropertyCategory;
use app\models\PropertyGroup;
use app\models\PropertyRelations;
use dektrium\user\controllers\RegistrationController;
use dektrium\user\controllers\SecurityController;
use dektrium\user\models\LoginForm;
use dektrium\user\models\RegistrationForm;
use dektrium\user\traits\AjaxValidationTrait;
use dektrium\user\traits\EventTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\Query;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Link;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
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

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
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
     * @throws ForbiddenHttpException
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        $model = Yii::createObject(LoginForm::className());
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

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionLogout()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

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

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionProducts()
    {
        /** @var $product Product */
        $request = Yii::$app->request->get();

        $request['status'] = Product::PRODUCT_STATUS_VALUE_PUBLIC;
        $page = (int)$request['page'];
        $category_id = $request['category_id'];

        unset($request['page']);

        if ($configuration_id = $request['configuration_id']) {
            $configuration = Configuration::findOne(['id' => $configuration_id]);
            $products = SelectionComponents::productsProcessing($configuration, $request);
        }

        $products = new ActiveDataProvider(['query' => $products]);
        $allProducts = $products->query->all();

        $result = [];
        $properties = [];
        $allowedProperties = PropertyCategory::find()->select(['property_id', 'menuindex'])->where(['category_id' => $category_id])->asArray()->all();
        $allowedPropertiesIds = array_column($allowedProperties, 'property_id');


        foreach ($products->getModels() as $product) {
            $result[] = $product->getProductApi();
        }

        if ($page === 1) {
            foreach ($allProducts as $product) {
                // todo: maybe price?
                $i = 0;
                foreach ($product->propertyRelations as $propertyRelation) {
                    $property = $propertyRelation->property;
                    $value = $propertyRelation->value;
                    if ($allowedPropertiesIds) {
                        if (!in_array($property->id, $allowedPropertiesIds)) {
                            continue;
                        }
                    }

                    if (!isset($properties[$property->id])) {
                        $properties[$property->id] = [
                            'title' => $property->name,
                            'values' => [],
                        ];
                    }
                    if ($properties[$property->id] && !in_array($value, $properties[$property->id]['values'])) {
                        $properties[$property->id]['values'][] = $value;
                    }
                }
            }
        }

        $currentPage = $products->getPagination()->getPage(true);
        $pageCount = $products->getPagination()->getPageCount();

        $pagination = [
            Link::REL_SELF => $currentPage + 1,
        ];

        if ($currentPage < $pageCount - 1) {
            $pagination[Pagination::LINK_NEXT] = $currentPage + 2;
            $pagination[Pagination::LINK_LAST] = $pageCount - 1;
        }
        return ['products' => $result, 'pagination' => $pagination, 'properties' => $properties];
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

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionGetConfigurations()
    {
        if (!Yii::$app->request->isGet) {
            throw new ForbiddenHttpException();
        }

        if (!Yii::$app->user->isGuest) {
            $configurations = Configuration::find()->where(['user_id' => Yii::$app->user->identity->getId(), 'status' => Configuration::STATUS_DONE])->all();
            $current_configuration = Configuration::find()->where(['user_id' => Yii::$app->user->identity->getId(), 'status' => Configuration::STATUS_PROCESS])->one();
        } else {
            $configurations = Configuration::find()->where(['token' => Yii::$app->session->getId(), 'status' => Configuration::STATUS_DONE])->all();
            $current_configuration = Configuration::find()->where(['token' => Yii::$app->session->getId(), 'status' => Configuration::STATUS_PROCESS])->one();
        }

        return ['configurations' => $configurations, 'current_configuration' => $current_configuration];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionGetConfiguration()
    {
        if (!Yii::$app->request->isGet) {
            throw new ForbiddenHttpException();
        }

        $request = Yii::$app->request->get();

        if (!$request) {
            throw new BadRequestHttpException();
        }

        $result = [];

        $configuration = Configuration::findOne(['id' => $request['id']]);

        $config_category = Category::CONFIG_CATEGORY;

        foreach (Category::find()->where(['status' => Category::STATUS_PUBLIC])->all() as $category) {
            if (in_array($category->id, $config_category)) {
                $result[] = $category->getCategoryApi();
            }
        }

        if (!$configuration) {
            throw new NotFoundHttpException();
        }

        foreach ($result as $key => $value) {
            foreach ($configuration->configurationRelations as $relation) {
                $product = Product::findOne(['id' => $relation->product_id]);
                if ($value['id'] === $product->category_id) {
                    $result[$key] = $product->getProductApi();
                }
            }
        }

        return ['result' => $result, 'configuration' => $configuration];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
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

        if (Yii::$app->request->isPost && !$request) {
            if (!$configuration) {
                $configuration = new Configuration();
                $configuration->token = Yii::$app->session->getId();
                $configuration->status = 0;
                $configuration->total_price = 0;
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
        }
        return ['result' => $result, 'configuration' => $configuration];
    }

    /**
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionAddProduct()
    {
        $request = Yii::$app->request->post();
        if (!$request) {
            throw new BadRequestHttpException();
        }

        $configuration = Configuration::findOne(['id' => $request['id']]);
        if (!$configuration) {
            throw new NotFoundHttpException();
        }

        $transaction = ConfigurationRelations::getDb()->beginTransaction();

        try {
            $configurationRelations = new ConfigurationRelations();
            $configurationRelations->configuration_id = $configuration->id;
            $configurationRelations->product_id = $request['product_id'];
            if (!$configurationRelations->save()) {
                throw new Exception('Error save configuration relation');
            }

            $configuration->total_price = $request['total_price'];
            if (!$configuration->save()) {
                throw new Exception('Error save configuration');
            }

            $transaction->commit();
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemoveProduct()
    {
        if (!Yii::$app->request->isDelete) {
            throw new ForbiddenHttpException();
        }
        $configuration = Configuration::findOne(['id' => Yii::$app->request->get('id')]);
        if (!$configuration) {
            throw new NotFoundHttpException();
        }

        $transaction = ConfigurationRelations::getDb()->beginTransaction();

        try {
            $configuration->total_price = Yii::$app->request->get('total_price');
            if (!$configuration->save()) {
                throw new Exception('Error update configuration');
            }

            $configurationRelations = ConfigurationRelations::findOne(['configuration_id' => $configuration->id, 'product_id' => Yii::$app->request->get('product_id')]);
            if (!$configurationRelations) {
                throw new NotFoundHttpException();
            }
            if (!$configurationRelations->delete()) {
                throw new Exception('Error delete configuration relation');
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $config_category = Category::CONFIG_CATEGORY;
        $result = [];
        foreach (Category::find()->where(['status' => Category::STATUS_PUBLIC])->all() as $category) {
            if (in_array($category->id, $config_category)) {
                $result[] = $category->getCategoryApi();
            }
        }
        foreach ($result as $key => $value) {
            foreach ($configuration->configurationRelations as $relation) {
                $product = Product::findOne(['id' => $relation->product_id]);
                if ($value['id'] === $product->category_id) {
                    $result[$key] = $product->getProductApi();
                }
            }
        }

        return ['result' => $result, 'configuration' => $configuration];
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

        if (!$request) {
            throw new BadRequestHttpException();
        }

        if ($request) {
            $configuration = Configuration::findOne(['id' => $request['id']]);
            $configuration->name = $request['name'];
            $configuration->status = Configuration::STATUS_DONE;
            $configuration->update();
        }
    }

    /**
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteConfiguration()
    {
        if (!Yii::$app->request->isDelete) {
            throw new ForbiddenHttpException();
        }
        if (!Yii::$app->request->get()) {
            throw new BadRequestHttpException();
        }

        if (Yii::$app->request->isDelete) {
            $configuration = Configuration::findOne(['id' => Yii::$app->request->get('id')]);
            $configuration->delete();
        }
    }
}
