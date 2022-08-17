<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%email_template}}`.
 */
class m180829_093905_create_message_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%message_template}}', [
            'id' => $this->primaryKey()->unsigned(),
            'type' => $this->integer()->unsigned()->notNull(),
            'event' => $this->integer()->unsigned()->notNull(),
            'data' => $this->json(),
            'created_at' => $this->json()->notNull(),
        ]);
        
        $this->createIndex(
            '{{%idx-message_template-type-event}}',
            '{{%message_template}}',
            ['type', 'event'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-message_template-type-event}}', 'message_template');
        
        $this->dropTable('{{%message_template}}');
    }
}
