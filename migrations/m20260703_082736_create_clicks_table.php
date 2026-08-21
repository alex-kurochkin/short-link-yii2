<?php

use yii\db\Migration;

/**
 * Class m20260703_082736_create_clicks_table
 */
class m20260703_082736_create_clicks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('clicks', [
            'id' => $this->primaryKey(),
            'link_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'clicked_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-clicks-link_id', 'clicks', 'link_id');
        
        $this->addForeignKey('fk-clicks-link_id', 'clicks', 'link_id', 'links', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk-clicks-link_id', 'clicks');
        $this->dropTable('clicks');
    }
}
