<?php

use yii\db\Migration;

/**
 * Handles adding unique_id to table `{{%product}}`.
 */
class m190216_185910_add_unique_id_column_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product', 'unique_id', $this->integer()->unique()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'unique_id');
    }
}
