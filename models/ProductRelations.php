<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_relations".
 *
 * @property int $id
 * @property int $product_id
 * @property int $store_id
 * @property string $regular_price
 * @property string $sale_price
 * @property string $club_price
 *
 * @property Product $product
 * @property Store $store
 */
class ProductRelations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_relations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'store_id'], 'required'],
            [['product_id', 'store_id'], 'integer'],
            [['regular_price', 'sale_price', 'club_price'], 'number'],
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
            'regular_price' => Yii::t('app', 'Regular Price'),
            'sale_price' => Yii::t('app', 'Sale Price'),
            'club_price' => Yii::t('app', 'Club Price'),
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
     * @return ProductRelationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductRelationsQuery(get_called_class());
    }
}
