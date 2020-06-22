<?php

use yii\db\Migration;

/**
 * Class m200614_125332_init_data
 */
class m200614_125332_init_db extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'login' => $this->string()->notNull(),
            'firstname' => $this->string()->notNull(),
            'points' => $this->integer(),
            'password' => $this->string(),
            'authKey' => $this->string()
        ]);

        $this->createTable('item_prizes', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'price' => $this->decimal(10, 2),
            'winner_id' => $this->integer(),
            'shipping_datetime' => $this->dateTime()
        ]);
        $this->addForeignKey(
            'fk1',
            'item_prizes',
            'winner_id',
            'users',
            'id',
            'RESTRICT');

        $this->createTable('money_prizes', [
            'id' => $this->primaryKey(),
            'sum' => $this->decimal(10, 2),
            'winner_id' => $this->integer()->notNull(),
            'operation_datetime' => $this->dateTime(),
            'operation_type' => $this->string()
        ]);

        $this->addForeignKey(
            'fk2',
            'money_prizes',
            'winner_id',
            'users',
            'id',
            'RESTRICT'
        );

        $this->createTable('options', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value' => $this->decimal(10, 2)
        ]);
        $this->insert('options', [
            'name' => 'total',
            'value' => Yii::$app->params['rules']['total']
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200614_125332_init_db cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200614_125332_init_data cannot be reverted.\n";

        return false;
    }
    */
}
