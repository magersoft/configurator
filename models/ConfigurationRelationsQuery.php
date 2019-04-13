<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ConfigurationRelations]].
 *
 * @see ConfigurationRelations
 */
class ConfigurationRelationsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ConfigurationRelations[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ConfigurationRelations|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
