<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%property_category}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%category}}`
 * - `{{%property}}`
 */
class m190523_130548_create_property_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%property_category}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'property_id' => $this->integer(),
            'menuindex' => $this->integer()->defaultValue(0),
        ]);

        // creates index for column `category_id`
        $this->createIndex(
            '{{%idx-property_category-category_id}}',
            '{{%property_category}}',
            'category_id'
        );

        // add foreign key for table `{{%category}}`
        $this->addForeignKey(
            '{{%fk-property_category-category_id}}',
            '{{%property_category}}',
            'category_id',
            '{{%category}}',
            'id',
            'CASCADE'
        );

        // creates index for column `property_id`
        $this->createIndex(
            '{{%idx-property_category-property_id}}',
            '{{%property_category}}',
            'property_id'
        );

        // add foreign key for table `{{%property}}`
        $this->addForeignKey(
            '{{%fk-property_category-property_id}}',
            '{{%property_category}}',
            'property_id',
            '{{%property}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%category}}`
        $this->dropForeignKey(
            '{{%fk-property_category-category_id}}',
            '{{%property_category}}'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            '{{%idx-property_category-category_id}}',
            '{{%property_category}}'
        );

        // drops foreign key for table `{{%property}}`
        $this->dropForeignKey(
            '{{%fk-property_category-property_id}}',
            '{{%property_category}}'
        );

        // drops index for column `property_id`
        $this->dropIndex(
            '{{%idx-property_category-property_id}}',
            '{{%property_category}}'
        );

        $this->dropTable('{{%property_category}}');
    }
}
