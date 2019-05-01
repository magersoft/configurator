<?php

use yii\db\Migration;

/**
 * Class m190501_114518_add_column_status_to_configuration_table
 */
class m190501_114518_add_column_status_to_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('configuration', 'status', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('configuration', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190501_114518_add_column_status_to_configuration_table cannot be reverted.\n";

        return false;
    }
    */
}
