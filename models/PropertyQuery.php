<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Property]].
 *
 * @see Property
 */
class PropertyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Property[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Property|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
