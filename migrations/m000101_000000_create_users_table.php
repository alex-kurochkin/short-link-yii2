<?php

use yii\db\Migration;

/**
 * Class m000101_000000_create_users_table
 */
class m000101_000000_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'email_verified_at' => $this->integer()->null(),
            'password' => $this->string()->notNull(),
            'remember_token' => $this->string(100)->null(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-users-email', 'users', 'email', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('users');
    }
}
