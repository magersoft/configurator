<?php

use app\models\Property;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'property_ids')->widget(
        \kartik\select2\Select2::className(), [
            'data' => ArrayHelper::map(Property::find()->all(), 'id',
                function (Property $property) {
                    return $property->name;
                }),
            'options' => ['placeholder' => 'Select properties', 'multiple' => true],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]
    ) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnail')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'menu_index')->textInput() ?>

    <?= $form->field($model, 'unique_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
