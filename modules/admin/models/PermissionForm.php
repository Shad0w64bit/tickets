<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use app\models\Permission;

/**
 * ContactForm is the model behind the contact form.
 */
class PermissionForm extends Permission
{
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
//                [['access'], 'required'],
//                ['access', 'checkPermission'],
            ]
        );
    }
    
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                'permission' => 'Разрешения',
            ]
        );
    }
    
}
