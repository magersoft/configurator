<?php

namespace app\models;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $short_description
 * @property string $short_title
 * @property string $thumbnail
 * @property string $link
 * @property string $brand
 * @property int $category_id
 * @property int $store_id
 * @property int $status
 * @property int $unique_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CategoryProduct[] $categoryProducts
 * @property Category[] $categories
 * @property Store $store
 * @property Category $category
 * @property ProductRelations[] $productRelations
 * @property PropertyRelations[] $propertyRelations
 */
class Product extends \yii\db\ActiveRecord
{
    const PRODUCT_STATUS_VALUE_DRAFT = 0;
    const PRODUCT_STATUS_VALUE_PUBLIC = 1;
    const PRODUCT_STATUS_VALUE_ARCHIVE = 2;

    const PRODUCT_STATUS_VALUE_DISPLAY = [
        self::PRODUCT_STATUS_VALUE_PUBLIC => 'Опубликован',
        self::PRODUCT_STATUS_VALUE_DRAFT => 'Не опубликован',
        self::PRODUCT_STATUS_VALUE_ARCHIVE => 'В архиве',
    ];

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
            [['description', 'short_description'], 'string'],
            [['category_id', 'store_id', 'status', 'unique_id', 'created_at', 'updated_at'], 'integer'],
            [['title', 'short_title', 'thumbnail', 'link'], 'string', 'max' => 255],
            [['brand'], 'string', 'max' => 100],
            [['unique_id'], 'unique'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::className(), 'targetAttribute' => ['store_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            'short_description' => Yii::t('app', 'Short Description'),
            'short_title' => Yii::t('app', 'Short Title'),
            'thumbnail' => Yii::t('app', 'Thumbnail'),
            'link' => Yii::t('app', 'Link'),
            'brand' => Yii::t('app', 'Brand'),
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
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
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
    public function getProductRelations()
    {
        return $this->hasMany(ProductRelations::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyRelations()
    {
        return $this->hasMany(PropertyRelations::className(), ['product_id' => 'id']);
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
