<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%email}}`.
 */
class m180701_164712_create_email_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%email}}', [
            'id' => $this->primaryKey()->unsigned(),
            'host' => $this->string(255)->notNull(),
            'port' => $this->integer()->unsigned()->notNull(),
            'username' => $this->string(320)->notNull(),
            'password' => $this->string(352)->notNull(),
            'mail' => $this->string(320)->notNull(),
            'encryption' => $this->integer(1)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email}}');
    }
}
