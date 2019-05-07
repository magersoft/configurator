<?php

use yii\db\Migration;

/**
 * Class m190507_143512_alter_total_price_column_to_configuration_table
 */
class m190507_143512_alter_total_price_column_to_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('configuration', 'total_price');
        $this->addColumn('configuration', 'total_price', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('configuration', 'total_price');
        $this->addColumn('configuration', 'total_price', $this->integer()->defaultValue(NULL));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190507_143512_alter_total_price_column_to_configuration_table cannot be reverted.\n";

        return false;
    }
    */
}
