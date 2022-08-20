<?php
namespace app\controllers;


use Yii;
use yii\web\Controller;
use app\models\OrganizationForm;
use app\models\SignupForm;

use app\models\Organization;
use app\models\User;

class InstallController extends Controller
{

	public $layout = 'install';
	
    public function actionIndex()
    {
        if (\Yii::$app->db->getTableSchema('{{%migration}}', true) == null) {
            $errors[] = 'Необходимо выполнить миграцию из консоли.';
        }		
				
		$owner_ids = Yii::$app->params['main_organizations'];
//		var_dump($owner_ids);
		
				
		$owners = Organization::find()->where(['id' => $owner_ids])->all();
		
		if (count($owners) == 0)
			return $this->redirect('/install/add-organization');
		
		if (count($owners) > 0)
			return $this->redirect('/');
		
        return $this->render('index', [
            'errors' => $errors,
			'owners' => count( $owners ) ? $owners : null,
        ]);
    }

    public function actionAddOrganization()
    {
        $org = Organization::find()->limit(1)->one();
        if (isset($org))
        {
            return $this->redirect('/install/add-user');
        }

        $model = new OrganizationForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($org = $model->add()) {
                return $this->refresh();
            }
        }

        return $this->render('organization', [
            'model' => $model,
        ]);
    }

    public function actionAddUser()
    {
        $org = Organization::find()->limit(1)->one();
        $model = new SignupForm();
        $model->organization_id = $org->id;

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->redirect('/install/secret-key');
                }
            }
        }

        return $this->render('user', [
            'model' => $model,
        ]);
    }
	
	public function actionSecretKey()
	{
		if (Yii::$app->params['secretKey'] != 'SECRET_PASS_KEY')
			return $this->redirect('/install/done');
		
		$conf = Yii::getAlias('@app/config/params.php');
		if (!file_exists($conf))
			throw new \Exception("Unable to find params.php in $conf");
		
		try {
			$data = file_get_contents($conf);
			$data = str_replace('SECRET_PASS_KEY', \Yii::$app->security->generateRandomString(32), $data);
			file_put_contents($conf, $data);
			
			return $this->refresh();
		} catch (\Exception $e) {
			return $this->render('secret-key', [
				'err' => $e->getMessage(),
			]);
			//return $e->message;
		}
		
		return $this->render('secret-key');
		
		//$config = include $conf;
		
		//if $config['secretKey'] == 'SECRET_PASS_KEY' secretKey
		
	}

    public function actionDone()
    {
		$owner_ids = Yii::$app->params['main_organizations'];
				
		$owners = Organization::find()->where(['id' => $owner_ids])->all();
		
        return $this->render('done', [
			'owners' => $owners,
		]);
    }

}