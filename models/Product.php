<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $short_title
 * @property string $thumbnail
 * @property string $regular_price
 * @property string $sale_price
 * @property string $club_price
 * @property int $category_id
 * @property int $store_id
 * @property int $status
 * @property int $unique_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CategoryProduct[] $categoryProducts
 * @property Category[] $categories
 * @property Category $category
 * @property Store $store
 * @property ProductStore[] $productStores
 * @property Store[] $stores
 */
class Product extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'category_id', 'store_id', 'unique_id'], 'required'],
            [['description'], 'string'],
            [['regular_price', 'sale_price', 'club_price'], 'number'],
            [['category_id', 'store_id', 'status', 'unique_id', 'created_at', 'updated_at'], 'integer'],
            [['title', 'short_title', 'thumbnail'], 'string', 'max' => 255],
            [['unique_id'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'short_title' => Yii::t('app', 'Short Title'),
            'thumbnail' => Yii::t('app', 'Thumbnail'),
            'regular_price' => Yii::t('app', 'Regular Price'),
            'sale_price' => Yii::t('app', 'Sale Price'),
            'club_price' => Yii::t('app', 'Club Price'),
            'category_id' => Yii::t('app', 'Category ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'status' => Yii::t('app', 'Status'),
            'unique_id' => Yii::t('app', 'Unique ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryProducts()
    {
        return $this->hasMany(CategoryProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable('category_product', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStores()
    {
        return $this->hasMany(ProductStore::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(Store::className(), ['id' => 'store_id'])->viaTable('product_store', ['product_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }
}
