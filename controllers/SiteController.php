<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\NewTicket;
use app\models\Message;
use app\models\Ticket;
use app\models\UserToken;
use app\models\RecoveryPasswordForm;
use app\models\ChangePasswordForm;
use app\models\User;
use yii\helpers\Url;
use app\models\Event;

class SiteController extends Controller
{

//    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
	public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'login','logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
//                    'send-all' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        if (in_array($action->id, ['send-all']))
        {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
	 /*
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }*/

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest)
        {
            return $this->redirect('/cabinet/');
        }
        return $this->redirect('login');
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $this->layout = 'login';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()))
        {
            if (!$model->isActive())
            {
                Yii::$app->session->addFlash('danger', 'Учетная запись не активирована.');
                return $this->redirect(['need-activate', 'email' => $model->email]);
            }

            if ($model->login()) {
                return $this->goBack();
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */

    public function actionRecoveryPassword()
    {
        $model = new RecoveryPasswordForm();
        $this->layout = 'login';

        if ($model->load(Yii::$app->request->post()) && $model->send())
        {
            Yii::$app->session->addFlash('success', 'На вашу почту отправлено письмо!');
            return $this->redirect(['index']);
        }
        
        return $this->render('recovery', [
            'model' => $model,
        ]);
    }

    public function actionReset($token)
    {
        $userToken = UserToken::find()->where([
            'type' => UserToken::TYPE_RESET,
            'token' => $token
        ])->one();

        if (!$userToken)
        {
            Yii::$app->session->addFlash('danger', 'Недействительный token');
            return $this->redirect(['index']);
        }

        $this->layout = 'login';

        $model = new ChangePasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->save($userToken))
        {
            Yii::$app->session->addFlash('success', 'Пароль успешно изменен');
            return $this->redirect(['index']);
        }
        return $this->render('setpassword', [
            'model' => $model,
        ]);

    }

    public function actionActivate($token)
    {
        $userToken = UserToken::find()->where([
            'token' => $token
        ])->one();

        if (isset($userToken))
        {
            $userToken->user->status = $userToken->user->status | User::USER_ACTIVE;
            if ($userToken->user->save())
            {
                Yii::$app->session->addFlash('success', 'Пользователь активирован успешно');
            }   else {
                Yii::$app->session->addFlash('danger', 'Не удалось активировать пользователя');
            }
        } else {
            Yii::$app->session->addFlash('danger', 'Неверный ключ');
        }
        return $this->redirect('index');
    }

    public function actionNeedActivate($email)
    {
        $user = User::findByEmail($email);
        if ($user->status & User::USER_ACTIVE)
        {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost)
        {
            $token = $user->generateActivationToken();

            if ($token === false)
            {
                Yii::$app->session->addFlash('danger', 'Ошибка при генерации ключа');
                return $this->refresh();
            }

            $data = [
                'uid' => $user->id,
                'email' => $user->email,
                'link' => Url::to(['site/activate', 'token' => $token], true),
            ];

            $event = Event::add(null, Event::USER_ACTIVATE, $data);
            if (!$event)
            {
                Yii::$app->session->addFlash('danger', 'Ошибка при генерации события');
                return $this->refresh();
            }

            Yii::$app->session->addFlash('success', 'Письмо направлено на ваш Email');
        }

        $this->layout = 'login';

        return $this->render('need-activate', [
            'email' => $email,
//            'model' => $model,
        ]);
    }

    public function actionChangeEmail($token)
    {
        $userToken = UserToken::find()->where([
            'token' => $token
        ])->one();

        if (isset($userToken))
        {
            $data = (!isset($userToken->data)) ? [] : json_decode($userToken->data);
            if (isset($data->email))
            {                
                try
                {
                    
                    $user = User::findOne($userToken->uid);
                    $user->email = $data->email;
                    if ($user->save())
                    {
                        $userToken->delete();
                        Yii::$app->session->addFlash('success', 'Email успешно изменен');
                        return $this->redirect('index');
                    } else {
                        Yii::$app->session->addFlash('danger', 'Не удалось сохранить изменения');
                    }
                } catch (\Exception $e) {
//                    var_dump($e->getMessage()); die();
                    Yii::$app->session->addFlash('danger', $e->getMessage());
                }
            }

        } else {
            Yii::$app->session->addFlash('danger', 'Недействительный token');
        }
        return $this->redirect('index');
    }


    /**
     * Displays about page.
     *
     * @return string
     */
/*    public function actionAbout()
    {
        return $this->render('about');
    }*/
    
    public function actionSendAll()
    {
        $event = new \app\models\Event();
        $event->load(Yii::$app->request->post(), '');
        return \app\models\Notification::sendOut($event);
    }

    public function actionSendHand($event)
    {
        $e = \app\models\Event::findOne($event);
        return \app\models\Notification::sendOut($e);
    }
}
