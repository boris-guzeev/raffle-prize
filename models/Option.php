<?php

namespace app\models;

/**
 * This is the model class for table "rules".
 *
 * @property int $id
 * @property string $name
 * @property float $value
 */
class Option extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'options';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['value'], 'number'],
            [['name'], 'string', 'max' => 50],
        ];
    }
}
