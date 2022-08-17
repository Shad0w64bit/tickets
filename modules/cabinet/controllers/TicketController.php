<?php

namespace app\modules\cabinet\controllers;

use Yii;

use app\models\Ticket;
use app\models\TicketSearch;
use app\models\NewTicket;
use app\models\Message;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Permission;
use yii\db\Query;
use app\models\AssignForm;
use app\models\User;
use yii\web\UploadedFile;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
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
                    'rm-upload' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {
        $client = Yii::$app->user->identity->isUser();

        $searchModel = new TicketSearch();
        $searchModel->status = Ticket::TICKET_STATUS_OPEN;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if ($client) {
            $dataProvider->query->andFilterWhere(['organization.id' => Yii::$app->user->identity->organization_id]);
        }
        
/*        if (Yii::$app->user->identity->onlyTheir())
        {
            if ($client) {
                $dataProvider->query->andFilterWhere(['ticket.user_id' => Yii::$app->user->id]);
            } else {
                $dataProvider->query->andFilterWhere(['ticket.assign_to' => Yii::$app->user->id]);
            }
        }*/
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $client = Yii::$app->user->identity->isUser();
        $model = $this->findModel($id);                        
        
        Yii::$app->user->identity->alertPermission(
            $model->group_id, 
            Permission::ACCESS_READ
        );

        $onlyTheir = Yii::$app->user->identity->perm(
            $model->group_id,
            Permission::ACCESS_ONLY_THEIR
        );
        
        if ( ($client &&
            (($model->organization->id !== Yii::$app->user->identity->organization_id)
                || ($onlyTheir                              // !!!!!
                    && ($model->user_id !== Yii::$app->user->id)))
            ) || (!$client && ($onlyTheir && $model->assign_to !== Yii::$app->user->id) )
        )
        {
            throw new \yii\web\ForbiddenHttpException('User don\'t have permission on view');
        }
        
        $input = new Message();
        $input->ticket_id = $id;


        if ($input->load(Yii::$app->request->post())
            && Yii::$app->user->identity->alertPermission( 
                $model->group_id, 
                Permission::ACCESS_WRITE)
            && $input->save( !($input->close || $model->closed ) )) // Если закрываем, то не посылаем отдельно сообщение
        {
                $this->refresh('#form-input');
        }

        $this->modelError($input->errors);

        return $this->render('view', [
            'model' => $model,
            'input' => $input,
            ($client) ?: 'assignForm' => new AssignForm(),
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NewTicket();
//        $client = Yii::$app->user->identity->isUser();

        if ($model->load(Yii::$app->request->post())
                && $model->validate() // Set Group
                && Yii::$app->user->identity->alertPermission( 
                $model->group_id, 
                Permission::ACCESS_CREATE)
                && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $this->modelError($model->errors);
        $this->modelError($model->message->errors);

        return $this->render('create', [
            'model' => $model,
            'input' => $model->message,
        ]);
    }

    private function modelError(&$errors)
    {
        if (count($errors) > 0)
        {
            foreach ($errors as $field => $error)
            {
                Yii::$app->session->addFlash('danger', $field . ': ' . print_r($error, true));
            }
        }
    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;       
        
        $model = $this->findModel($id);
        
        if (!Yii::$app->user->identity->perm(
            $model->group_id,
            Permission::ACCESS_CREATE)
        ){
            return ['error' => 'Access denied!'];
        }
        
/*        if (Yii::$app->request->isAjax)
        {*/            
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'data' => [
                    'text' => $model->title,
                ]
            ];
        }
        //}
        
        return ['error' => $model->getErrors()];
/*        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }*/

//        return $this->render('update', [
//            'model' => $model,
//        ]);
    }

    public function actionOpen($id)
    {
        $model = $this->findModel($id);

        if ($model->open())
        {
            Yii::$app->session->addFlash( 'success', 'Заявка успешно открыта' );
        } else {
            Yii::$app->session->addFlash( 'danger', 'Не удалось открыть заявку');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionClose($id)
    {
        $model = $this->findModel($id);

        if ($model->close())
        {
            Yii::$app->session->addFlash( 'success', 'Заявка успешно закрыта' );
        } else {
            Yii::$app->session->addFlash( 'danger', 'Не удалось закрыть заявку');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Yii::$app->user->identity->alertPermission(
            $model->group_id, 
            Permission::ACCESS_DELETE);
        
        if ($model->delete())
        {
            Yii::$app->session->addFlash('success','Заявка успешно удалена.');
        }
        return $this->redirect(['index']);
    }
    
    public function actionDeleteMultiple()
    {
        $client = Yii::$app->user->identity->isUser();
//        $model = $this->findModel($id);
        $keys = Yii::$app->request->post('keys');
//        var_dump($keys);die();
        $count = count($keys);
        $del = 0;
                    
        foreach ($keys as $key)
        {
            $ticket = Ticket::findOne($key);
            
            if (
                (($client && ($ticket->organization_id === Yii::$app->user->identity->organization_id)) || !$client)
            &&   Yii::$app->user->identity->perm($ticket->group_id, Permission::ACCESS_DELETE)
            )
            {
                $ticket->delete();
                $del++;                
            }            
        }               
        
        Yii::$app->session->addFlash('success', "Успешно удалено $del из $count заявок.");
        
        return $del;
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
//        if (($model = Ticket::findOne($id)) !== null) {
        if (($model = Ticket::find()->where(['id'=>$id])->with(
//                'category', 'user', 'events', 'assigned'
                'events.user', 'messages.user', 
                'messages.messageFiles')->one()) !== null) {
//        if (($model = Ticket::find(['id'=>$id])->with('messages.user', 'messages.messageFiles')->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    
           
    public function actionAssignTicket($id)
    {
        if (!Yii::$app->request->isPost)
        {            
            return $this->redirect( ['view', 'id' => $id]);
        }
        
        $model = new AssignForm();
        $model->load(Yii::$app->request->post());        
        
/*        $newGroup = $model->group_id;
        $newStaff = (is_integer($model->user_id)) ? $model->user_id : null;*/
        
        $newGroup = (is_numeric($model->group_id)) ? (int)$model->group_id : null;
        $newStaff = (is_numeric($model->user_id)) ? (int)$model->user_id : null;
        
        if (!Yii::$app->user->identity->perm($newGroup, Permission::ACCESS_ASSIGN_GROUP))
        {
            throw new \yii\web\ForbiddenHttpException('User don\'t have permission to assign this group');
        }                
                
        if (isset($newStaff))
        {
            if (!Yii::$app->user->identity->perm($newGroup, Permission::ACCESS_ASSIGN_USER))
            {
                throw new \yii\web\ForbiddenHttpException('User don\'t have permission to assign this staff');
            }
            
            $user = User::findOne($newStaff);
            if ($user->perm($newGroup, Permission::ACCESS_READ))
            {
                new \yii\web\ForbiddenHttpException('User don\'t have permission to READ this group');
            }
        }
        
/*        if (is_numeric($newStaff) 
                && !Yii::$app->user->identity->perm($newGroup, Permission::ACCESS_ASSIGN_USER))
        {
            throw new \yii\web\ForbiddenHttpException('User don\'t have permission to assign this staff');
        }*/

        if (isset($model->text))
        {
            $msg = new Message();
            $msg->ticket_id = $id;
            $msg->text = $model->text;
//            $msg->user_id = Yii::$app->user->id;
            $msg->save(false);
        }
        
        $ticket = Ticket::findOne($id);
        if ($ticket->assign($newGroup, $newStaff, $msg->id))
        {
            Yii::$app->session->addFlash('success', 'Заявка успешно переназначена.');
        } else {
            Yii::$app->session->addFlash('danger', 'Не удалось переназначить заявку.');
        }
        
        return $this->redirect( ['view', 'id' => $id]);
    }
}
