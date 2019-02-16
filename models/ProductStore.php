<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_store".
 *
 * @property int $id
 * @property int $product_id
 * @property int $store_id
 *
 * @property Product $product
 * @property Store $store
 */
class ProductStore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'store_id'], 'required'],
            [['product_id', 'store_id'], 'integer'],
            [['product_id', 'store_id'], 'unique', 'targetAttribute' => ['product_id', 'store_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'store_id' => Yii::t('app', 'Store ID'),
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
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProductStoreQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductStoreQuery(get_called_class());
    }
}
