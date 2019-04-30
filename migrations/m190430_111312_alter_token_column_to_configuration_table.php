<?php

use yii\db\Migration;

/**
 * Class m190430_111312_alter_token_column_to_configuration_table
 */
class m190430_111312_alter_token_column_to_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('configuration', 'token');
        $this->addColumn('configuration', 'token', $this->string(60)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('configuration', 'token');
        $this->addColumn('configuration', 'token', $this->string(60)->notNull()->unique());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190430_111312_alter_token_column_to_configuration_table cannot be reverted.\n";

        return false;
    }
    */
}
