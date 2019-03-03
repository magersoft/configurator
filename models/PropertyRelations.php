<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property_relations".
 *
 * @property int $id
 * @property int $property_id
 * @property int $product_id
 * @property string $value
 * @property string $desc
 *
 * @property Product $product
 * @property Property $property
 */
class PropertyRelations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property_relations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['property_id', 'product_id'], 'integer'],
            [['desc'], 'string'],
            [['value'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'property_id' => Yii::t('app', 'Property ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'value' => Yii::t('app', 'Value'),
            'desc' => Yii::t('app', 'Desc'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['id' => 'property_id']);
    }

    /**
     * {@inheritdoc}
     * @return PropertyRelationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PropertyRelationsQuery(get_called_class());
    }
}
