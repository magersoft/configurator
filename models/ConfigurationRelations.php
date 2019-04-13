<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuration_relations".
 *
 * @property int $id
 * @property int $product_id
 * @property int $configuration_id
 *
 * @property Configuration $configuration
 * @property Product $product
 */
class ConfigurationRelations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuration_relations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'configuration_id'], 'required'],
            [['product_id', 'configuration_id'], 'integer'],
            [['configuration_id'], 'exist', 'skipOnError' => true, 'targetClass' => Configuration::className(), 'targetAttribute' => ['configuration_id' => 'id']],
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
            'product_id' => Yii::t('app', 'Product ID'),
            'configuration_id' => Yii::t('app', 'Configuration ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfiguration()
    {
        return $this->hasOne(Configuration::className(), ['id' => 'configuration_id']);
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
     * @return ConfigurationRelationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConfigurationRelationsQuery(get_called_class());
    }
}
