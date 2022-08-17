<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_token}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m181115_071404_create_user_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_token}}', [
            'id' => $this->primaryKey()->unsigned(),
            'uid' => $this->integer()->unsigned()->notNull(),
            'type' => $this->integer()->unsigned()->notNull(),
            'token' => $this->string(64)->notNull()->unique(),
            'data' => $this->json(),
            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ]);

        // creates index for column `uid`
        $this->createIndex(
            '{{%idx-user_token-uid}}',
            '{{%user_token}}',
            'uid'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_token-uid}}',
            '{{%user_token}}',
            'uid',
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
            '{{%fk-user_token-uid}}',
            '{{%user_token}}'
        );

        // drops index for column `uid`
        $this->dropIndex(
            '{{%idx-user_token-uid}}',
            '{{%user_token}}'
        );

        $this->dropTable('{{%user_token}}');
    }
}
