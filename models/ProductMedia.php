<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_media".
 *
 * @property int $id
 * @property string $url
 * @property string $sm_image
 * @property string $md_image
 * @property string $bg_image
 * @property int $product_id
 *
 * @property Product $product
 */
class ProductMedia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_media';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id'], 'integer'],
            [['url'], 'string', 'max' => 50],
            [['sm_image', 'md_image', 'bg_image'], 'string', 'max' => 30],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'sm_image' => Yii::t('app', 'Sm Image'),
            'md_image' => Yii::t('app', 'Md Image'),
            'bg_image' => Yii::t('app', 'Bg Image'),
            'product_id' => Yii::t('app', 'Product ID'),
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
     * {@inheritdoc}
     * @return ProductMediaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductMediaQuery(get_called_class());
    }
}
