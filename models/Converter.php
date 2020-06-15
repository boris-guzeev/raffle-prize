<?php

namespace app\models;

use \Yii;
use yii\db\Query;

class Converter
{
    // типы операций с деньгами
     const CONVERT_TYPE = 'convert';
     const SEND_TYPE = 'send';

    /**
     * Меняет товар на деньги
     * @param integer $id ИД товара
     * @throws \Exception
     * @return true|null
     */
    public static function toMoney($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $price = (new Query())
                ->select('price')
                ->from('item_prizes')
                ->where(['id' => $id])
                ->scalar();

            Yii::$app->db->createCommand()->update(
                'item_prizes',
                ['winner_id' => null],
                ['winner_id' => Yii::$app->user->identity->id, 'id' => $id])
                ->execute();

            Yii::$app->db->createCommand()->insert('money_prizes',
                [
                    'sum' => $price, 'winner_id' => Yii::$app->user->identity->id
                ]
            )->execute();
            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Меняет последний выйгранный текущим пользователем денежный приз на баллы
     * @param integer $userid
     * @return true|false
     * @throws \Exception
     */
    public static function toPoints($userid)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // получим id транзакции и сумму
            $query = (new Query())
                ->select(['id', 'sum'])
                ->from('money_prizes')
                ->where(['winner_id' => $userid, 'operation_type' => null])
                ->orderBy(['id' => SORT_DESC])->one();
            $moneyPrice = $query['sum'];
            $moneyId = $query['id'];

            // сконвертируем в баллы
            $points = $moneyPrice * Yii::$app->params['rules']['ratio'];
            $currentPoints = (new Query())
                ->select('points')
                ->from('users')
                ->where(['id' => $userid])
                ->scalar();

            // занесем в аккаунт пользователя
            Yii::$app
                ->db
                ->createCommand()
                ->update('users', ['points' => $currentPoints + $points], ['id' => $userid])
                ->execute();



            // зафиксируем операцию как конвертация
            $result = Yii::$app
                ->db
                ->createCommand()
                ->update('money_prizes', ['operation_datetime' => date('Y-m-d H:i:s'), 'operation_type' => self::CONVERT_TYPE],
                    ['id' => $moneyId])
            ->execute();
            $transaction->commit();

            return $result;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}