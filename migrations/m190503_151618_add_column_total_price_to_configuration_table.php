<?php

use yii\db\Migration;

/**
 * Class m190503_151618_add_column_total_price_to_configuration_table
 */
class m190503_151618_add_column_total_price_to_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('configuration', 'total_price', $this->integer()->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('configuration', 'total_price');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190503_151618_add_column_total_price_to_configuration_table cannot be reverted.\n";

        return false;
    }
    */
}
