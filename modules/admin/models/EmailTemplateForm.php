<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use app\models\Email;

class EmailTemplateForm extends Model
{
    public $template_id;
//    public $subject;
//    public $plain;
//    public $html;
    public $title;
    public $text;


    public function rules()
    {
        return [
            [['title', 'text'], 'required'],
            [['title', 'text'], 'string'],
//            [['subject', 'plain'], 'required'],
//            [['subject', 'plain', 'html'], 'string'],
        ];
    }

    public function attributeLabels()
    {
         return [
            'title'  => 'Тема письма',
            'text'    => 'Текст письма',
        ];
    }
    
    public function getJSON()
    {
        return json_encode([
            'title' => $this->title,
            'text' => $this->text,
//            'subject' => $this->subject,
//            'plain' => $this->plain,
//            'html' => $this->html,
        ]);
    }
    
    public function getFields(&$form)
    {
        return $form->field($this, 'title')->textInput(['maxlength' => true])
            . $form->field($this, 'text')->textarea(['rows' => '12', 'maxlength' => true])
//        return $form->field($this, 'subject')->textInput(['maxlength' => true])
//            . $form->field($this, 'plain')->textarea(['maxlength' => true])
//            . $form->field($this, 'html')->textarea(['maxlength' => true])
        ;
    }
    
    
}