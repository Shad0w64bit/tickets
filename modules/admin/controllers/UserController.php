<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use app\models\Permission;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\PermissionSearch;

use \app\modules\admin\models\UserCreateForm;
use app\modules\admin\models\ChangePasswordForm;
use yii\widgets\ActiveForm;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $oid = Yii::$app->request->get('organization_id');
        $organization = (isset($oid)) ? \app\models\Organization::findOne($oid) : null;
        
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['user.organization_id' => Yii::$app->params['main_organizations']]);
        if (isset($organization))
        {
            $dataProvider->query->andFilterWhere(['user.organization_id' => $oid]);   
        }        
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    
    /**
     * Lists all Permission models.
     * @return mixed
     */
    public function actionPermission($id)
    {
        $new = new \app\models\Permission;
        $new->user_id = $id;
        
        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['user.id' => $id]);
        
        $permissions = \app\models\Permission::find()->select('group_id')->where(['user_id' => $id]);
        $groups = \app\models\Group::find()->where(['not in', 'id', $permissions]);
        $availableGroups = $groups->all();

        return $this->render('permission', [
            'user' => User::findOne($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'new' => $new,
            'availableGroups' => $availableGroups,
        ]);
    }
    
    public function actionPermissionAdd()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;       
        
        $model = new Permission;
        
        $model->load(Yii::$app->request->post());
                
        return ($model->save()) 
            ? [ 'success' => true ] 
            : [ 'error'   => $model->getErrorSummary(true) ];
    }
    
    public function actionPermissionUpdate($id)
    {
        $model = Permission::findOne($id);        
        $uid = $model->user_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Данные успешно сохранены.');
            return $this->redirect(['permission', 'id' => $uid]);
        }

        return $this->render('permission-update', [
            'model' => $model,
        ]);
    }

    
    public function actionPermissionDelete($id)
    {
        $perm = Permission::findOne($id);
        $group = $perm->group->name;
        
        $uid = $perm->user_id;
        
        if ($perm->delete())
        {
            Yii::$app->session->addFlash('success', 'Удалены разрешения на группу ' . $group);
        } else {
            Yii::$app->session->addFlash('danger', 'Не удалось удалить разрешения на группу ' . $group);
        }

        return $this->redirect(['permission', 'id' => $uid]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserCreateForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
//        var_dump($model->getErrors());

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'changePasswordForm' => new ChangePasswordForm(['uid' => $id]),
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()))
        {
            if ($model->validate())
            {
                $user = User::findOne($model->uid);
                $user->setPassword( $model->newPassword );

                if ($user->save())
                {
                    Yii::$app->session->addFlash('success', 'Пароль успешно изменен');
                } else {
                    Yii::$app->session->addFlash('danger', 'Ошибка при сохранении пароля в базу');
                }

            } else {
                Yii::$app->session->addFlash('danger', 'Ошибка при проверке формы');
            }
        }

        $this->redirect(['update', 'id'=>$user->id]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
