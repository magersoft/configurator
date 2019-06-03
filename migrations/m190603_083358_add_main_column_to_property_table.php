<?php

use yii\db\Migration;

/**
 * Handles adding main to table `{{%property}}`.
 */
class m190603_083358_add_main_column_to_property_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%property}}', 'main', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%property}}', 'main');
    }
}
