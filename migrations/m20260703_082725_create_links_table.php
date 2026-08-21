<?php

use yii\db\Migration;

/**
 * Class m20260703_082725_create_links_table
 */
class m20260703_082725_create_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('links', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'code' => $this->string()->notNull()->unique(),
            'original_url' => $this->string(2048)->notNull(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-links-user_id', 'links', 'user_id');
        $this->createIndex('idx-links-code', 'links', 'code', true);
        
        $this->addForeignKey('fk-links-user_id', 'links', 'user_id', 'users', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk-links-user_id', 'links');
        $this->dropTable('links');
    }
}
