<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use dektrium\user\models\User;

/**
 * This is the model class for table "configuration".
 *
 * @property int $id
 * @property string $token
 * @property string $name
 * @property int $total_price
 * @property int $status
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ConfigurationRelations[] $configurationRelations
 * @property User $user
 */
class Configuration extends \yii\db\ActiveRecord
{
    const STATUS_PROCESS = 0;
    const STATUS_DONE = 1;

    const STATUSES = [
        self::STATUS_PROCESS => 'In process',
        self::STATUS_DONE => 'Well done'
    ];
    
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
            [['token', 'name'], 'required'],
            [['created_at', 'updated_at', 'user_id', 'status', 'total_price'], 'integer'],
            [['token'], 'string', 'max' => 60],
            [['name'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'token' => Yii::t('app', 'Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
            'user_id' => 'User ID',
            'total_price' => Yii::t('app', 'Total price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
