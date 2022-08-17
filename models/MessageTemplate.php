<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%message_template}}".
 *
 * @property int $id
 * @property int $type
 * @property int $event 
 * @property array $data
 */
class MessageTemplate extends \yii\db\ActiveRecord
{
    const TEMPLATE_UNKNOWN = 0;
    const TEMPLATE_EMAIL = 1;
    const TEMPLATE_VIBER = 2;    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%message_template}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'event', 'data'], 'required'],
            [['type', 'event'], 'integer'],
            [['data'], 'safe'],
            [['type', 'event'], 'unique', 'targetAttribute' => ['type', 'event']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'event' => 'Событие',
            'data' => 'Данные',
        ];
    }
}
