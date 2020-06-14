<?php

namespace app\models;
use \Yii;
use yii\db\Exception;

class Game
{
    private $minPrize;
    private $maxPrize;
    private $minPoints;
    private $maxPoints;

    function __construct()
    {
        $this->minPrize = Yii::$app->params['rules']['minPrize'];
        $total = Option::findOne(['name' => 'total'])->value;
        $maxPrize = Yii::$app->params['rules']['maxPrize'];
        $this->maxPrize = $maxPrize < $total ? $maxPrize : $total;

        $this->minPoints = Yii::$app->params['rules']['minPoints'];
        $this->maxPoints = Yii::$app->params['rules']['maxPoints'];
    }

    /**
     * @return array массив c результатом для дальнейшего отображения на фронте
     * @throws Exception
     */
    public function play()
    {
        $prizes = $this->definePrizes();

        // определим какой тип подарка будет разыгрываться
        // если остались только бонусные баллы, то сразу начинаем розыгрыш
        if (count($prizes) == 1 && $prizes[0] == 'points') {
            return [
                'type' => 'points',
                'result' => $this->saveResult('points')
            ];
        } else {
            // рандомно выберем что разыгрывать
            $rand = rand(0, count($prizes) - 1);
            $prizeType = $prizes[$rand];
            return [
                'type' => $prizeType,
                'result' => $this->saveResult($prizeType)
            ];
        }

    }

    /**
     * Определяет какие типы призов могут участвовать в розыгрыше
     * @return array типы призов
     */
    private function definePrizes()
    {
        // если есть неразыгранные товары, то они участвуют
        $itemPrizes = ItemPrize::findAll(['winner_id' => null]);
        $prizes = [];
        if ($itemPrizes) {
            $prizes[] = 'items';
        }

        // если осталось достаточно денег, то денежные призы участвуют
        $total = Option::findOne(['name' => 'total'])->value;
        $minPrize = \Yii::$app->params['rules']['minPrize'];
        if ($total >= $minPrize) {
            $prizes[] = 'money';
        }
        // бонусные баллы участвуют всегда
        $prizes[] = 'points';

        return $prizes;
    }

    /**
     * Сохраняет результат розыгрыша
     * @param string $prizeType тип приза
     * @throws Exception
     * @return string сохраненный результат
     */
    private function saveResult($prizeType)
    {
        $user = User::findOne(['id' => Yii::$app->user->identity->id]);
        if ($prizeType == 'points') {
            $result = rand($this->minPoints, $this->maxPoints);
            $user->points += $result;
            if ($user->save())
                return $result;
        }
        elseif ($prizeType == 'money') {
            $result = rand($this->minPrize, $this->maxPrize);
            // обернём в транзакцию, чтобы сохранить целостность запроса перевода денег
            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()->insert('money_prizes',
                        [
                            'sum' => $result,
                            'winner_id' => Yii::$app->user->identity->id
                        ])
                    ->execute();
                $total = (new \yii\db\Query())
                    ->select('value')
                    ->from('options')
                    ->where(['name' => 'total'])
                    ->scalar();
                Yii::$app->db->createCommand()
                    ->update('options', ['value' => $total - $result], ['name' => 'total'])
                    ->execute();
                $transaction->commit();

                return $result;
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            $items = (new \yii\db\Query())
                ->select(['id', 'title'])
                ->from('item_prizes')
                ->where(['winner_id' => null])
                ->all();
            // если остается только один предмет, то его и дарим, а если нет, то случайным образом из оставщихся (0 тут уже не может быть)
            if (count($items) == 1) {
                $itemId = $items[0]['id'];
                $itemTitle =$items[0]['title'];
            } else {
                $rand = rand(0, count($items) - 1);
                $itemId = $items[$rand]['id'];
                $itemTitle = $items[$rand]['title'];
            }

            Yii::$app->db
                ->createCommand()
                ->update('item_prizes',
                    ['winner_id' => Yii::$app->user->identity->id],
                    ['id' => $itemId])
                ->execute();
            return $itemTitle;
        }
    }
}