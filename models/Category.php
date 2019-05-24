<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $thumbnail
 * @property int $parent_id
 * @property int $status
 * @property int $menu_index
 * @property int $unique_id
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property CategoryProduct[] $categoryProducts
 * @property Product[] $products
 * @property Product[] $products0
 * @property PropertyCategory[] $propertyCategories
 */
class Category extends \yii\db\ActiveRecord
{

    public $property_ids;

    const STATUS_ARCHIVED = 0;
    const STATUS_PUBLIC = 1;

    const STATUSES = [
        self::STATUS_ARCHIVED => 'Неопубликовано',
        self::STATUS_PUBLIC => 'Опубликовано'
    ];

    const CONFIG_CATEGORY = [67,71,72,73,74,75];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug', 'unique_id'], 'required'],
            [['parent_id', 'status', 'menu_index', 'unique_id'], 'integer'],
            [['title', 'slug', 'thumbnail'], 'string', 'max' => 255],
            [['unique_id'], 'unique'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],
            ['property_ids', 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => 'id']],
            ['property_ids', 'default', 'value' => []],
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
            'slug' => Yii::t('app', 'Slug'),
            'thumbnail' => Yii::t('app', 'Thumbnail'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'status' => Yii::t('app', 'Status'),
            'menu_index' => Yii::t('app', 'Menu Index'),
            'unique_id' => Yii::t('app', 'Unique ID'),
            'property_ids' => 'Property Ids'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryProducts()
    {
        return $this->hasMany(CategoryProduct::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('category_product', ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts0()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCategories()
    {
        return $this->hasMany(PropertyCategory::className(), ['category_id' => 'id']);
    }

    public function getCategoryApi()
    {
        return [
            'id' => $this->id,
            'short_title' => $this->title,
            'thumbnail' => $this->getThumbnail()
        ];
    }

    public function getThumbnail()
    {
        $path = './uploads/'.$this->thumbnail;

        if (!$this->thumbnail || !file_exists($path) || !filesize($path)) {
            return '/images/placeholder.jpg';
        }
        return '/uploads/'.$this->thumbnail;
    }

    public function loadPropertyIds()
    {
        $this->property_ids = array_column($this->propertyCategories, 'property_id');
    }

    /**
     * {@inheritdoc}
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
}
