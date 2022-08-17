<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%group}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%organization}}`
 */
class m180701_165342_create_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%group}}', [
            'id' => $this->primaryKey()->unsigned(),
            'organization_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string()->notNull(),
            'manager' => $this->integer()->unsigned(),
            'email' => $this->integer()->unsigned(),

        ]);

        // creates index for column `organization_id`
        $this->createIndex(
            '{{%idx-group-organization_id}}',
            '{{%group}}',
            'organization_id'
        );

        // add foreign key for table `{{%organization}}`
        $this->addForeignKey(
            '{{%fk-group-organization_id}}',
            '{{%group}}',
            'organization_id',
            '{{%organization}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `manager`
        $this->createIndex(
            '{{%idx-group-manager}}',
            '{{%group}}',
            'manager'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-group-manager}}',
            '{{%group}}',
            'manager',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `email`
        $this->createIndex(
            '{{%idx-group-email}}',
            '{{%group}}',
            'email'
        );

        // add foreign key for table `{{%email}}`
        $this->addForeignKey(
            '{{%fk-group-email}}',
            '{{%group}}',
            'email',
            '{{%email}}',
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
            '{{%fk-group-organization_id}}',
            '{{%group}}'
        );

        // drops index for column `organization_id`
        $this->dropIndex(
            '{{%idx-group-organization_id}}',
            '{{%group}}'
        );
        
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-group-manager}}',
            '{{%group}}'
        );

        // drops index for column `manager`
        $this->dropIndex(
            '{{%idx-group-manager}}',
            '{{%group}}'
        );

        // drops foreign key for table `{{%email}}`
        $this->dropForeignKey(
            '{{%fk-group-email}}',
            '{{%group}}'
        );

        // drops index for column `email`
        $this->dropIndex(
            '{{%idx-group-email}}',
            '{{%group}}'
        );

        $this->dropTable('{{%group}}');
    }
}
