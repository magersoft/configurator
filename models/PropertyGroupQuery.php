<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PropertyGroup]].
 *
 * @see PropertyGroup
 */
class PropertyGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PropertyGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PropertyGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
