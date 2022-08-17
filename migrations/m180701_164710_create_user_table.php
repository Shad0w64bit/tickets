<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%organization}}`
 */
class m180701_164710_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'organization_id' => $this->integer()->notNull()->unsigned(),
            'email' => $this->string(140)->notNull(),
            'first_name' => $this->string(30),
            'last_name' => $this->string(30),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(180)->notNull(),
/*            'password_reset_token' => $this->string(100)->unique(),
            'password_reset_date' => $this->integer()->unsigned(),*/
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),

			
        ]);

        // creates index for column `organization_id`
        $this->createIndex(
            '{{%idx-user-organization_id}}',
            '{{%user}}',
            'organization_id'
        );

        // add foreign key for table `{{%organization}}`
        $this->addForeignKey(
            '{{%fk-user-organization_id}}',
            '{{%user}}',
            'organization_id',
            '{{%organization}}',
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
            '{{%fk-user-organization_id}}',
            '{{%user}}'
        );

        // drops index for column `organization_id`
        $this->dropIndex(
            '{{%idx-user-organization_id}}',
            '{{%user}}'
        );

        $this->dropTable('{{%user}}');
    }
}
