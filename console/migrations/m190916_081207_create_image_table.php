<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%image}}`.
 */
class m190916_081207_create_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%image}}', [
            'id' => $this->primaryKey(),
            'pet_id' => $this->integer(),
            'path' => $this->string(),
        ]);
        $this->addForeignKey('fk-image-pet_id', '{{%image}}', 'pet_id', '{{%pet}}', 'id',
            'CASCADE');
        $this->createIndex('idx-image-pet_id', '{{%image}}', 'pet_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%image}}');
        $this->dropForeignKey('fk-image-pet_id', '{{%image}}');
        $this->dropIndex('idx-image-pet_id', '{{%image}}');
    }
}
