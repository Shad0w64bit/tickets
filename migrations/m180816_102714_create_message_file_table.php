<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%message_file}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%message}}`
 * - `{{%file}}`
 */
class m180816_102714_create_message_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%message_file}}', [
            'id' => $this->primaryKey()->unsigned(),
            'message_id' => $this->integer()->unsigned()->notNull(),
            'file_id' => $this->integer()->unsigned()->notNull(),
        ]);

        // creates index for column `message_id`
        $this->createIndex(
            '{{%idx-message_file-message_id}}',
            '{{%message_file}}',
            'message_id'
        );

        // add foreign key for table `{{%message}}`
        $this->addForeignKey(
            '{{%fk-message_file-message_id}}',
            '{{%message_file}}',
            'message_id',
            '{{%message}}',
            'id',
            'CASCADE'
        );

        // creates index for column `file_id`
        $this->createIndex(
            '{{%idx-message_file-file_id}}',
            '{{%message_file}}',
            'file_id'
        );

        // add foreign key for table `{{%file}}`
        $this->addForeignKey(
            '{{%fk-message_file-file_id}}',
            '{{%message_file}}',
            'file_id',
            '{{%file}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%message}}`
        $this->dropForeignKey(
            '{{%fk-message_file-message_id}}',
            '{{%message_file}}'
        );

        // drops index for column `message_id`
        $this->dropIndex(
            '{{%idx-message_file-message_id}}',
            '{{%message_file}}'
        );

        // drops foreign key for table `{{%file}}`
        $this->dropForeignKey(
            '{{%fk-message_file-file_id}}',
            '{{%message_file}}'
        );

        // drops index for column `file_id`
        $this->dropIndex(
            '{{%idx-message_file-file_id}}',
            '{{%message_file}}'
        );

        $this->dropTable('{{%message_file}}');
    }
}
