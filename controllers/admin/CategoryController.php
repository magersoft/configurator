<?php

namespace app\controllers\admin;

use app\models\PropertyCategory;
use Yii;
use app\models\Category;
use app\models\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $relations = [];
            foreach ($model->property_ids as $property_id) {
                $relation = PropertyCategory::findOne(['property_id' => $property_id, 'category_id' => $model->id]);
                if (!$relation) {
                    $relation = new PropertyCategory();
                    $relation->category_id = $model->id;
                    $relation->property_id = $property_id;
                    $relation->save();
                }
                $relations[] = $relation->id;
            }
            PropertyCategory::deleteAll(['and', ['category_id' => $model->id], ['not', ['id' => $relations]]]);
            Yii::error($model->errors);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadPropertyIds();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $relations = [];
            foreach ($model->property_ids as $property_id) {
                $relation = PropertyCategory::findOne(['property_id' => $property_id, 'category_id' => $model->id]);
                if (!$relation) {
                    $relation = new PropertyCategory();
                    $relation->category_id = $model->id;
                    $relation->property_id = $property_id;
                    $relation->save();
                }
                $relations[] = $relation->id;
            }
            PropertyCategory::deleteAll(['and', ['category_id' => $model->id], ['not', ['id' => $relations]]]);
            Yii::error($relation->errors);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
