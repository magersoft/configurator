<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property_group".
 *
 * @property int $id
 * @property string $name
 *
 * @property Property[] $properties
 */
class PropertyGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::className(), ['group_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return PropertyGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PropertyGroupQuery(get_called_class());
    }
}
