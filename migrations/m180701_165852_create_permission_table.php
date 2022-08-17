<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%permission}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%group}}`
 * - `{{%user}}`
 */
class m180701_165852_create_permission_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%permission}}', [
            'id' => $this->primaryKey()->unsigned(),
            'group_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'access' => $this->integer()->notNull(),
        ]);

        // creates index for column `group_id`
        $this->createIndex(
            '{{%idx-permission-group_id}}',
            '{{%permission}}',
            'group_id'
        );

        // add foreign key for table `{{%group}}`
        $this->addForeignKey(
            '{{%fk-permission-group_id}}',
            '{{%permission}}',
            'group_id',
            '{{%group}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-permission-user_id}}',
            '{{%permission}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-permission-user_id}}',
            '{{%permission}}',
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
        // drops foreign key for table `{{%group}}`
        $this->dropForeignKey(
            '{{%fk-permission-group_id}}',
            '{{%permission}}'
        );

        // drops index for column `group_id`
        $this->dropIndex(
            '{{%idx-permission-group_id}}',
            '{{%permission}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-permission-user_id}}',
            '{{%permission}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-permission-user_id}}',
            '{{%permission}}'
        );

        $this->dropTable('{{%permission}}');
    }
}
