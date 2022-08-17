<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%message}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%ticket}}`
 * - `{{%user}}`
 */
class m180701_170149_create_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%message}}', [
            'id' => $this->primaryKey()->unsigned(),
            'ticket_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'text' => $this->string()->notnull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `ticket_id`
        $this->createIndex(
            '{{%idx-message-ticket_id}}',
            '{{%message}}',
            'ticket_id'
        );

        // add foreign key for table `{{%ticket}}`
        $this->addForeignKey(
            '{{%fk-message-ticket_id}}',
            '{{%message}}',
            'ticket_id',
            '{{%ticket}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-message-user_id}}',
            '{{%message}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-message-user_id}}',
            '{{%message}}',
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
        // drops foreign key for table `{{%ticket}}`
        $this->dropForeignKey(
            '{{%fk-message-ticket_id}}',
            '{{%message}}'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            '{{%idx-message-ticket_id}}',
            '{{%message}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-message-user_id}}',
            '{{%message}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-message-user_id}}',
            '{{%message}}'
        );

        $this->dropTable('{{%message}}');
    }
}
