<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use app\models\Email;

class ViberTemplateForm extends Model
{
    public $title;
    public $text;
    
    public function rules()
    {
        return [
            [['title', 'text'], 'required'],
            [['text', 'title'], 'string'],
        ];
    }

    public function attributeLabels()
    {
         return [
            'title'  => 'Заголовок',
            'text'   => 'Сообщение',
        ];
    }
    
    public function getJSON()
    {
        return json_encode([
            'title' => $this->title,
            'text' => $this->text,
        ]);
    }
    
    public function getFields(&$form)
    {
        return $form->field($this, 'title')->textInput(['maxlength' => true])
            . $form->field($this, 'text')->textarea(['rows' => '12', 'maxlength' => true])
        ;
    }
    
    
}