<?php

namespace app\modules\cabinet;

use yii\filters\AccessControl;
use app\components\AccessRule;
use app\models\User;
use Yii;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{

    public function behaviors()
    {
        return [
            'access' => [
                //'class' => AccessRule::className(),
                'class' => AccessControl::className(),
                   // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
//                        'roles' => [Organization::ROLE_ADMIN]
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                return true;
                            }
                            return false;
                        }

                    ]
                ]
            ]
        ];
    }


    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\cabinet\controllers';
    public $layout = 'cabinet';
    public $defaultRoute = 'ticket';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
