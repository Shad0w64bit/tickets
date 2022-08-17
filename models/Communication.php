<?php

namespace app\models;

use Yii;
use yii\base\Model;

abstract class Communication extends Model
{
    protected $type;
    protected $user;

    public function getType() {
        return $this->type;
    }

    abstract public function send(Event $e);

/*    public function init(Array $init)
    {
        foreach ($init as $key => $val) {
            $this->$key = $val;
        }
    }*/

 public function __construct(Array $config) {
    parent::__construct();
    foreach ($config as $key => $val) {
        $this->$key = $val;
    }    
 }
}