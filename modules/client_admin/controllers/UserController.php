<?php

namespace app\modules\client_admin\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use app\models\Permission;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\PermissionSearch;

use \app\models\Organization;
use \app\modules\admin\models\UserCreateForm;
use app\modules\client_admin\models\ChangeEmailForm;

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
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['user.organization_id' => Yii::$app->user->identity->organization_id]);
     
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
        $model->organization_id = Yii::$app->user->identity->organization->id;
//        $organization = Yii::$app->user->identity->organization;
//        $organization = (isset($oid)) ? Organization::findOne($oid) : null;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
//        var_dump($model->getErrors());

        return $this->render('create', [
            'model' => $model,
//            'organization' => $organization,
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
        
        $changeEmailForm = new ChangeEmailForm();
        $changeEmailForm->id = $model->id;

        return $this->render('update', [
            'model' => $model,
            'changeEmailForm' => $changeEmailForm,
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
    
    public function actionChangeEmail($id)
    {
        $model = $this->findModel($id);
        $form = new ChangeEmailForm();
        $form->load(Yii::$app->request->post());
        
        Yii::$app->session->addFlash('warning', "Данная функция временно недоступна.<br>"
                . "Старый адрес: " . $model->email . "<br>"
                . "Новй адрес: " . $form->email);

        return $this->redirect(['view', 'id' => $id]);
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
