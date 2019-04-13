<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%configuration}}`.
 */
class m190413_210223_create_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('configuration', [
            'id' => $this->primaryKey(),
            'token' => $this->string(60)->notNull()->unique(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('configuration');
    }
}
