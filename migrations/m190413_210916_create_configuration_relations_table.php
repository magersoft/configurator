<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%configuration_relations}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%product}}`
 * - `{{%configuration}}`
 */
class m190413_210916_create_configuration_relations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('configuration_relations', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'configuration_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `product_id`
        $this->createIndex(
            'idx-configuration_relations-product_id',
            'configuration_relations',
            'product_id'
        );

        // add foreign key for table `{{%product}}`
        $this->addForeignKey(
            'fk-configuration_relations-product_id',
            'configuration_relations',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        // creates index for column `configuration_id`
        $this->createIndex(
            'idx-configuration_relations-configuration_id',
            'configuration_relations',
            'configuration_id'
        );

        // add foreign key for table `{{%configuration}}`
        $this->addForeignKey(
            'fk-configuration_relations-configuration_id',
            'configuration_relations',
            'configuration_id',
            'configuration',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%product}}`
        $this->dropForeignKey(
            'fk-configuration_relations-product_id',
            'configuration_relations'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            'idx-configuration_relations-product_id',
            'configuration_relations'
        );

        // drops foreign key for table `{{%configuration}}`
        $this->dropForeignKey(
            'fk-configuration_relations-configuration_id',
            'configuration_relations'
        );

        // drops index for column `configuration_id`
        $this->dropIndex(
            'idx-configuration_relations-configuration_id',
            'configuration_relations'
        );

        $this->dropTable('configuration_relations');
    }
}
