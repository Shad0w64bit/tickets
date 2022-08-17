<?php

namespace app\modules\ajax\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

class FileController extends Controller
{

       public function actionUpload() {
        $files = $_FILES['files'];

/*        $human_filesize = function ($bytes, $decimals = 2) {
            $size = array('Б','КБ','МБ','ГБ','ТБ','ПБ','ЕБ','ЗБ','ЙБ');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
        };*/

        $uploads = [];

        foreach ($files['name'] as $id => $name)
        {
            $tmp = Yii::getAlias( Yii::$app->params['tmpDir'] . 'file' . uniqid() . '.' .  pathinfo($files['name'][$id], PATHINFO_EXTENSION));

            @move_uploaded_file($files['tmp_name'][$id], $tmp) or
                die( json_encode(['error' => 'Сервер получил файл, но не смог его обработать.']) );

            $uploads[] = [
                'name' => $files['name'][$id],
                'file' => basename($tmp),
                'size' => $files['size'][$id], //$human_filesize($files['size'][$id], 0),
            ];
        }

        return json_encode(['files' => $uploads]);

    }

    public function actionRmUpload($file)
    {
        $f = Yii::getAlias( Yii::$app->params['tmpDir'] . $file);
        if (file_exists($f) && !unlink($f))
        {
            return false;
        }
        return true;
    }

}
