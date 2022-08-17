<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m180816_100826_create_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey()->unsigned(),
            'file' => $this->string(64)->notNull(),
            'name' => $this->string(64)->notNull(),
            'size' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-file-user_id}}',
            '{{%file}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-file-user_id}}',
            '{{%file}}',
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
            '{{%fk-file-user_id}}',
            '{{%file}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-file-user_id}}',
            '{{%file}}'
        );

        $this->dropTable('{{%file}}');
    }
}
