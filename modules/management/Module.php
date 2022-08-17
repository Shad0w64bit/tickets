<?php

namespace app\modules\management;
use yii\filters\AccessControl;
use app\components\AccessRule;
use Yii;

/**
 * staff module definition class
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
                                Yii::$app->user->identity->isStaff() &&
                                Yii::$app->user->identity->isModerator() ) {
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
    public $controllerNamespace = 'app\modules\management\controllers';
    public $layout = 'management';
    public $defaultRoute = 'organization';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
