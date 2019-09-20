<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pet}}`.
 */
class m190916_075101_create_pet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pet}}', [
            'id' => $this->primaryKey(),
            'nickname' => $this->string(),
            'breed' => $this->string(),
            'category_id' => $this->integer(),
            'commentary' => $this->string(),
            'user_id' => $this->integer(),
            'price' => $this->integer(),
            'status' => $this->boolean()->defaultValue(1),
        ]);
        $this->addForeignKey('fk-pet-category_id', '{{%pet}}', 'category_id', '{{%category}}',
            'id', 'CASCADE');
        $this->createIndex('idx-pet-category_id', '{{%pet}}', 'category_id');
        $this->addForeignKey('fk-pet-user_id', '{{%pet}}', 'user_id', '{{%user}}', 'id',
            'CASCADE');
        $this->createIndex('idx-pet-user_id', '{{%pet}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pet}}');
        $this->dropForeignKey('fk-pet-category_id', '{{%pet}}');
        $this->dropIndex('idx-pet-category_id', '{{%pet}}');
        $this->dropForeignKey('fk-pet-user_id', '{{%pet}}');
        $this->dropIndex('idx-pet-user_id', '{{%pet}}');
    }
}
