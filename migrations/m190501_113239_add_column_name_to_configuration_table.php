<?php

use yii\db\Migration;

/**
 * Class m190501_113239_add_column_name_to_configuration_table
 */
class m190501_113239_add_column_name_to_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('configuration', 'name', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('configuration', 'name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190501_113239_add_column_name_to_configuration_table cannot be reverted.\n";

        return false;
    }
    */
}
