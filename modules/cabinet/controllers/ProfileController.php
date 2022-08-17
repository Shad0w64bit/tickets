<?php

namespace app\modules\cabinet\controllers;

use \Yii;
use yii\web\Controller;
use app\models\User;
use app\modules\cabinet\models\ChangePasswordForm;
use app\modules\cabinet\models\ChangeEmailForm;
use \yii\widgets\ActiveForm;
use yii\web\Response;
use \yii\data\ArrayDataProvider;
use yii\helpers\Html;

class ProfileController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->load(Yii::$app->request->post());
        
        $permissions = $user->getPermissions()->all();
        $permProvider = new ArrayDataProvider([
            'allModels' => $permissions,
            'pagination' => false,
            'sort' => [
                'attributes' => ['group'],
            ],            
        ]);
        
        if (Yii::$app->request->isPost && $user->validate())
        {
            if ($user->save())
            {
                Yii::$app->session->addFlash('success', 'Профиль успешно обновлен.');
            } 
        }
                
        return $this->render('index',[
            'model' => $user,
            'permissions' => $permProvider,
            'changePasswordForm' => new ChangePasswordForm(),
            'changeEmailForm' => new ChangeEmailForm(),
        ]);
    }
    
    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();        
         
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()))
        {
            if ($model->validate())
            {
                Yii::$app->user->identity->setPassword( $model->newPassword );
                if (Yii::$app->user->identity->save())
                {
                    Yii::$app->session->addFlash('success', 'Пароль успешно изменен');
                } else {
                    Yii::$app->session->addFlash('danger', 'Ошибка при сохранении пароля в базу');
                }
                
            } else {
                Yii::$app->session->addFlash('danger', 'Ошибка при проверке формы');
            }            
        }
        
        $this->redirect(['index']);
    }
    
    public function actionChangePasswordValidate()
    {
        $model = new ChangePasswordForm();        
        $model->load(Yii::$app->request->post());
        
        if (Yii::$app->request->isAjax)
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } 
        
        return $this->renderPartial('_password', [
           'model' => $model, 
        ]);
        
    }

    public function actionChangeEmail()
    {
        $model = new ChangeEmailForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()))
        {
            if ($model->validate())
            {
                if ($model->changeEmail())
                {
                    Yii::$app->session->addFlash('success', 'Письмо направлено на '. Html::encode($model->email));
                } else {
                    Yii::$app->session->addFlash('danger', 'Возникла проблема при отправке письма');
                }
            } else {
                Yii::$app->session->addFlash('danger', 'Ошибка при проверке формы');
            }
        }

        $this->redirect(['index']);
    }

    public function actionChangeEmailValidate()
    {
        $model = new ChangeEmailForm();
        $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax)
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        return $this->renderPartial('_email', [
           'model' => $model,
        ]);

    }
}
