<?php

namespace app\modules\client_admin;
use yii\filters\AccessControl;
use app\components\AccessRule;
use Yii;

/**
 * client module definition class
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
                //        'roles' => [Organization::ROLE_ADMIN]
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest &&
                                Yii::$app->user->identity->isUser() &&
                                Yii::$app->user->identity->isAdmin()) {
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
    public $controllerNamespace = 'app\modules\client_admin\controllers';
    public $layout = 'client';
    public $defaultRoute = 'user';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
