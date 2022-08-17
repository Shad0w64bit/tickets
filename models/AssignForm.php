<?php

namespace app\models;

use yii\base\Model;

class AssignForm extends Model
{
    public $ticket_id;
    public $group_id;
    public $user_id;
    public $text;

    public function rules()
    {
        return [
            [['ticket_id', 'group_id'], 'required'],
            [['ticket_id', 'group_id', 'user_id'], 'integer'],
            [['text'], 'string'],
        ];
    }

    public function attributeLabels() {
        return [
            'ticket_id' => 'Заявка',
            'group_id' => 'Отдел',
            'user_id' => 'Сотрудник',
            'text' => 'Сообщение',
        ];
    }
    
    public function assign()
    {
        return true;
    }
    
}
