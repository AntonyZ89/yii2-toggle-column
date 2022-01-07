<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%toggle_column}}`.
 */
class m220105_225647_create_toggle_column_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%toggle_column}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),

            'table' => $this->string()->notNull(),
            'columns' => $this->json()->notNull(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%toggle_column}}');
    }
}
