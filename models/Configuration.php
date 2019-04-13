<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "configuration".
 *
 * @property int $id
 * @property string $token
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ConfigurationRelations[] $configurationRelations
 */
class Configuration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuration';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['token'], 'string', 'max' => 60],
            [['token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'token' => Yii::t('app', 'Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurationRelations()
    {
        return $this->hasMany(ConfigurationRelations::className(), ['configuration_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ConfigurationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConfigurationQuery(get_called_class());
    }
}
