<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%event}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%ticket}}`
 */
class m180815_050501_create_event_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%event}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'ticket_id' => $this->integer()->unsigned(),
            'type' => $this->integer()->unsigned()->notNull(),
            'description' => $this->string(200),
            'date' => $this->integer()->unsigned()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-event-user_id}}',
            '{{%event}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-event-user_id}}',
            '{{%event}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `ticket_id`
        $this->createIndex(
            '{{%idx-event-ticket_id}}',
            '{{%event}}',
            'ticket_id'
        );

        // add foreign key for table `{{%ticket}}`
        $this->addForeignKey(
            '{{%fk-event-ticket_id}}',
            '{{%event}}',
            'ticket_id',
            '{{%ticket}}',
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
            '{{%fk-event-user_id}}',
            '{{%event}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-event-user_id}}',
            '{{%event}}'
        );

        // drops foreign key for table `{{%ticket}}`
        $this->dropForeignKey(
            '{{%fk-event-ticket_id}}',
            '{{%event}}'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            '{{%idx-event-ticket_id}}',
            '{{%event}}'
        );

        $this->dropTable('{{%event}}');
    }
}
