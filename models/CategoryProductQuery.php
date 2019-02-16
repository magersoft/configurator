<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CategoryProduct]].
 *
 * @see CategoryProduct
 */
class CategoryProductQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CategoryProduct[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CategoryProduct|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
