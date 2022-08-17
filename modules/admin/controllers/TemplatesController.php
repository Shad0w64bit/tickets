<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\MessageTemplate;
use app\models\MessageTemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailTemplateController implements the CRUD actions for EmailTemplate model.
 */
class TemplatesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
/*            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],*/
        ];
    }

    /**
     * Lists all EmailTemplate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageTemplateSearch();               
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailTemplate model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
/*    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new EmailTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
/*    public function actionCreate()
    {
        $model = new MessageTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/
    
    public function actionSync()
    {
        $events = \app\models\Event::getAllEvents();
        
        $types = [
            MessageTemplate::TEMPLATE_EMAIL,
            MessageTemplate::TEMPLATE_VIBER,
        ];                 
        
        foreach ($types as $type)
        {
            $newEvents = $events;
            $templates = MessageTemplate::find()->where(['type' => $type])->all();
            
            foreach ($templates as $template)
            {                
                if (array_key_exists($template->event, $newEvents))
                {
                    unset($newEvents[$template->event]);
                }
            }
            
            foreach ($newEvents as $key => $value)
            {
                $template = new MessageTemplate();
                $template->type = $type;
                $template->event = $key;
                $template->data = json_encode(['title' => 'Не задано', 'text' => 'Не задано']);
                $template->save();
            }
            
        }
                
        
        return $this->redirect('index');
    }

    /**
     * Updates an existing EmailTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

/*        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }*/
        if ($model->event === MessageTemplate::TEMPLATE_EMAIL)
        {
            $form = new \app\modules\admin\models\EmailTemplateForm();            
        } else {
            $form = new \app\modules\admin\models\ViberTemplateForm();
        }           
        
        if (isset($model->data))
        {
            $data = json_decode($model->data, true);
            $form->load( $data, '' );    
        }
        

        if (Yii::$app->request->isPost 
            && $form->load(Yii::$app->request->post())
            && $form->validate() )
        {
            $model->data = $form->getJSON();
            if ($model->save())
            {
                $this->redirect('index');
            }            
        }
        
        
        return $this->render('update', [
            'model' => $model,
            'formData' => $form,
        ]);
    }

    /**
     * Deletes an existing EmailTemplate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
/*    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the EmailTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MessageTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
