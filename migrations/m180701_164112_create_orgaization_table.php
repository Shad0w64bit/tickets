<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orgaization}}`.
 */
class m180701_164112_create_orgaization_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%organization}}', [
            'id' => $this->primaryKey()->unsigned(),
            'inn' => $this->string(12)->unique()->notNull(),
            'name' => $this->string(500)->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%organization}}');
    }
}
