<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property".
 *
 * @property int $id
 * @property string $name
 * @property int $group_id
 *
 * @property PropertyGroup $group
 * @property PropertyRelations[] $propertyRelations
 */
class Property extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'group_id'], 'required'],
            [['group_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
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
            'group_id' => Yii::t('app', 'Group ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(PropertyGroup::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyRelations()
    {
        return $this->hasMany(PropertyRelations::className(), ['property_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return PropertyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PropertyQuery(get_called_class());
    }
}
