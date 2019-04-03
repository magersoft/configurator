<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_media}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%product}}`
 */
class m190402_190748_create_product_media_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('product_media', [
            'id' => $this->primaryKey(),
            'url' => $this->string(50),
            'sm_image' => $this->string(30),
            'md_image' => $this->string(30),
            'bg_image' => $this->string(30),
            'product_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `product_id`
        $this->createIndex(
            'idx-product_media-product_id',
            'product_media',
            'product_id'
        );

        // add foreign key for table `{{%product}}`
        $this->addForeignKey(
            'fk-product_media-product_id',
            'product_media',
            'product_id',
            'product',
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
            'fk-product_media-product_id',
            'product_media'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            'idx-product_media-product_id',
            'product_media'
        );

        $this->dropTable('product_media');
    }
}
