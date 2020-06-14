<?php

namespace app\models;

/**
 * This is the model class for table "item_prizes".
 *
 * @property int $id
 * @property string $title
 * @property float $price
 * @property int|null $winner_id
 * @property string|null $shipping_datetime
 *
 * @property User $winner
 */
class ItemPrize extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_prizes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['price'], 'number'],
            [['winner_id'], 'integer'],
            [['shipping_datetime'], 'safe'],
            [['title'], 'string', 'max' => 50],
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
