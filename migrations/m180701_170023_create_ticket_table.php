<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ticket}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%category}}`
 * - `{{%user}}`
 */
class m180701_170023_create_ticket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket}}', [
            'id' => $this->primaryKey()->unsigned(),
            'organization_id' => $this->integer()->notNull()->unsigned(),
            'group_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'title' => $this->string()->notnull(),
            'assign_to' => $this->integer()->unsigned(),

            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'closed_at' => $this->integer()->unsigned(),
        ]);

        // creates index for column `organization_id`
        $this->createIndex(
            '{{%idx-ticket-organization_id}}',
            '{{%ticket}}',
            'organization_id'
        );

        // add foreign key for table `{{%organization}}`
        $this->addForeignKey(
            '{{%fk-ticket-organization_id}}',
            '{{%ticket}}',
            'organization_id',
            '{{%organization}}',
            'id',
            'CASCADE'
        );

        // creates index for column `group_id`
        $this->createIndex(
            '{{%idx-ticket-group_id}}',
            '{{%ticket}}',
            'group_id'
        );

        // add foreign key for table `{{%group}}`
        $this->addForeignKey(
            '{{%fk-ticket-group_id}}',
            '{{%ticket}}',
            'group_id',
            '{{%group}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-ticket-user_id}}',
            '{{%ticket}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-ticket-user_id}}',
            '{{%ticket}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
        
                // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-ticket-assign_to}}',
            '{{%ticket}}',
            'assign_to'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-ticket-assign_to}}',
            '{{%ticket}}',
            'assign_to',
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
        // drops foreign key for table `{{%organization}}`
        $this->dropForeignKey(
            '{{%fk-ticket-organization_id}}',
            '{{%ticket}}'
        );

        // drops index for column `organization_id`
        $this->dropIndex(
            '{{%idx-ticket-organization_id}}',
            '{{%ticket}}'
        );

        // drops foreign key for table `{{%group}}`
        $this->dropForeignKey(
            '{{%fk-ticket-group_id}}',
            '{{%ticket}}'
        );

        // drops index for column `group_id`
        $this->dropIndex(
            '{{%idx-ticket-group_id}}',
            '{{%ticket}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-ticket-user_id}}',
            '{{%ticket}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-ticket-user_id}}',
            '{{%ticket}}'
        );
        
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-ticket-assign_to}}',
            '{{%ticket}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-ticket-assign_to}}',
            '{{%ticket}}'
        );

        $this->dropTable('{{%ticket}}');
    }
}
