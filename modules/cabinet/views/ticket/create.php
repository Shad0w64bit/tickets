<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;
use yii\helpers\ArrayHelper;
use app\models\User;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\assets\MessageAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */

MessageAsset::register($this);

$this->title = 'Создать заявку';
$this->params['breadcrumbs'][] = ['label' => 'Заявки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$client = Yii::$app->user->identity->isUser();


$allowCategory = Category::find()->leftJoin('permission', 'permission.user_id='.\Yii::$app->user->identity->id)
    ->where('permission.access & '.\app\models\Permission::ACCESS_WRITE)
    ->andWhere('category.group_id=permission.group_id')->all();

?>
<div class="ticket-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="ticket-form">

        <?php $form = ActiveForm::begin(['id'=>'form-input', 'options' => ['method' => 'post', 'enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'category_id')->dropDownList(
            ArrayHelper::map($allowCategory, 'id', 'name'),
            [ 'prompt' => 'Выберите категорию...' ]
        )->label('Категория') ?>


        <?php if (!$client): ?>
        <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
            'options' => ['placeholder' => 'Поиск пользователя ...'],
            'pluginOptions' => [
                'minimumInputLength' => 3,
                'language' => 'ru',
                'ajax' => [
                    'url' => Url::to(['/ajax/user/user-sort-list']),
                    'dataType' => 'json',
                ],
            ],
        ])->label('Пользователь'); ?>
        <?php endif; ?>
        
        <?= $form->field($model, 'title')->textInput(['autocomplete'=>'off', 'maxlength' => true]) ?>        

        
            <?= Html::fileInput('hide-input',null,['id' => 'loadFiles', 'multiple' => true, 'style'=>'display: none;']) ?>
            <div class="form-group">                
            <?= $form->field($model, 'text')->textarea(['class'=>'form-control input-text', 'placeholder' => 'Введите текст...', 'style'=>'height: 54px;'])  ?>
            <?php //=  Html::textarea($model->formName().'[text]', '', ['class'=>'form-control input-text', 'placeholder' => 'Введите текст...', 'style'=>'height: 54px;'])  ?>                
            </div>
            <div id="input-files" class="form-group"></div>
            <div class="form-group">    
                <?= Html::button(
                        '<span class="glyphicon glyphicon-paperclip"></span>&nbsp;Файл',
                        [
                            'class' => 'btn btn-default',
                            'onclick' => '(function ( $event ) { $("#loadFiles").click(); })();',
                        ]); ?>
                <div class="btn-group pull-right">
                <?= Html::submitButton('<span class="glyphicon glyphicon-send"></span>&nbsp;Отправить',
                    ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        
        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php
//$this->registerJsFile(Yii::getAlias('@web/js/staff.create.upload.js'),['depends' => ['yii\web\JqueryAsset']]);

//$this->registerJsFile(Yii::getAlias('@web/js/attach.files.js'),['depends' => ['yii\web\JqueryAsset']]);

//$files = json_encode($input->allFiles);

$script = <<< JS
$(document).ready(function(){    
    $("#form-input").attachFiles({
        'urlUpload' : '/cabinet/ticket/upload',
        'urlRemove' : '/cabinet/ticket/rm-upload?file=',
        'viewFile'  : '/file/temp?name=',
        'modelName' : 'NewTicket',        
    });
});
JS;

$this->registerJs($script);
