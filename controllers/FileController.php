<?php

namespace app\controllers;

use Yii;
use app\models\File;

class FileController extends \yii\web\Controller
{    
    
    public function actionDownload($id)
    {
        $file = File::findOne($id);        
        $f = Yii::getAlias( Yii::$app->params['uploadDir'])
            . substr($file->file, 0, 1) . '/' . substr($file->file, 1, 1) . '/' . $file->file;
        
        if (file_exists($f) && is_file($f))
        {
            return \Yii::$app->response->sendFile($f, $file->name);
        }
          
        throw new \yii\web\NotFoundHttpException('File not found');
    }
    
    public function actionTemp($name)
    {
        $f = Yii::getAlias( Yii::$app->params['tmpDir'] . $name);

        if (file_exists($f) && is_file($f))
        {
            return \Yii::$app->response->sendFile($f);   
        }
        
        throw new \yii\web\NotFoundHttpException('File not found');
    }

}
