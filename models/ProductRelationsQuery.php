<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ProductRelations]].
 *
 * @see ProductRelations
 */
class ProductRelationsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductRelations[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductRelations|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
