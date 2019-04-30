<?php

use yii\db\Migration;

/**
 * Handles adding user_id to table `{{%configuration}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m190430_111806_add_user_id_column_to_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%configuration}}', 'user_id', $this->integer());

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-configuration-user_id}}',
            '{{%configuration}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-configuration-user_id}}',
            '{{%configuration}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-configuration-user_id}}',
            '{{%configuration}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-configuration-user_id}}',
            '{{%configuration}}'
        );

        $this->dropColumn('{{%configuration}}', 'user_id');
    }
}
