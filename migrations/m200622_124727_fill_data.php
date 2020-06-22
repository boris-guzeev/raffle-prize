<?php

use yii\db\Migration;

/**
 * Class m200622_124727_fill_data
 */
class m200622_124727_fill_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('users', [
            'login', 'firstname', 'password',
        ], [
            ['ivan', 'Иван', '1234'],
            ['alex', 'Алексей', '1234'],
            ['petr', 'Пётр', '1234'],
            ['serg', 'Сергей', '1234'],
            ['artem', 'Артём', '1234'],
        ]);

        $this->batchInsert('item_prizes', [
            'title', 'price'
        ], [
            ['ботинки', 150],
            ['куртка', 200],
            ['шапка', 50],
            ['толствовка', 75],
            ['рубашка', 120]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200622_124727_fill_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200622_124727_fill_test_data cannot be reverted.\n";

        return false;
    }
    */
}
