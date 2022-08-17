<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%group}}`
 */
class m180701_165523_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey()->unsigned(),
            'group_id' => $this->integer()->notNull()->unsigned(),
            'name' => $this->string()->notNull(),
        ]);

        // creates index for column `group_id`
        $this->createIndex(
            '{{%idx-category-group_id}}',
            '{{%category}}',
            'group_id'
        );

        // add foreign key for table `{{%group}}`
        $this->addForeignKey(
            '{{%fk-category-group_id}}',
            '{{%category}}',
            'group_id',
            '{{%group}}',
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
            '{{%fk-category-group_id}}',
            '{{%category}}'
        );

        // drops index for column `group_id`
        $this->dropIndex(
            '{{%idx-category-group_id}}',
            '{{%category}}'
        );

        $this->dropTable('{{%category}}');
    }
}
