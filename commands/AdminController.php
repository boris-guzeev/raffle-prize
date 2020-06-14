<?php

namespace app\commands;

use app\models\Converter;
use yii\console\Controller;
use yii\console\ExitCode;
use \Yii;
use yii\db\Exception;
use yii\db\Query;

class AdminController extends Controller
{
    /**
     * Отправка денежных средств со счёта пользователя
     * @param integer $userId ИД пользователя, чьи платежи нужно отправить в банк
     * @param integer $quantity Количество платежей, которое нужно отправить
     * @return int Exit code
     */
    public function actionSend($userId, $quantity)
    {
        // найдем все денежные неотправленные денежные призы в нужном количестве
        $unsendedMoney = (new Query())
            ->select(['id', 'sum'])
            ->from('money_prizes')
            ->where(['winner_id' => $userId, 'operation_datetime' => null, 'operation_type' => null])
            ->limit($quantity)
            ->all();

        //отправим информацию по каждому денежному подарку в импровизированный банк например через cURL через Апи банка
        foreach ($unsendedMoney as $item) {
            //$url = 'https://myBank.com/'; $item['sum']; $curl = curl_init(); и тд..
            // в случае успешного ответа запишем что данная операция выполнена
            if (true) {
                try {
                    Yii::$app->db->createCommand()->update('money_prizes',
                        [
                            'operation_datetime' => date('Y-m-d H:i:s'),
                            'operation_type' => Converter::SEND_TYPE
                        ], ['id' => $item['id']]
                    )->execute();
                } catch (Exception $e) {
                    $e->getMessage();
                }

            } else {
                // пишем лог ошибок или выводим на экран ошибку банка
            }
        }

        return ExitCode::OK;
    }
}