<?php

namespace app\components;

use yii\base\Component;

class ConfigFile extends Component
{
    private $_config;
    
    public $file = '@app/config/params.php';
    
    private function getConfig()
    {
//        if (!isset($this->_config))
    }


    public function __get($name) {
        
        
        
        return parent::__get($name);
    }
}