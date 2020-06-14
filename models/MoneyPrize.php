<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "money_prizes".
 *
 * @property int $id
 * @property float $sum
 * @property int $winner_id
 * @property string|null $operation_datetime
 *
 * @property User $winner
 */
class MoneyPrize extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'money_prizes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sum', 'winner_id'], 'required'],
            [['sum'], 'number'],
            [['winner_id'], 'integer'],
            [['operation_datetime'], 'safe'],
            [['winner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['winner_id' => 'id']],
        ];
    }

    /**
     * Gets query for [[Winner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWinner()
    {
        return $this->hasOne(User::className(), ['id' => 'winner_id']);
    }
}
