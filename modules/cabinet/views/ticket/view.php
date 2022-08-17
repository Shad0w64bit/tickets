<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Ticket;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use \app\models\Permission;
use app\assets\MessageAsset;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use \app\models\Message;
use yii\web\JsExpression;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */

MessageAsset::register($this);

$this->title = "Заявка #".$model->getId();
$this->params['breadcrumbs'][] = ['label' => 'Заявки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$client = Yii::$app->user->identity->isUser();

$accessWrite = Yii::$app->user->identity->perm($model->group_id, Permission::ACCESS_WRITE);
$accessDelete = Yii::$app->user->identity->perm($model->group_id, Permission::ACCESS_DELETE);
$accessAssign = Yii::$app->user->identity->perm($model->group_id, Permission::ACCESS_ASSIGN_GROUP);

?>
<div class="ticket-view">

    <h1>
        <?= Html::encode($model->title) ?>
        <?php if ($accessWrite): ?>
            
        <?php endif; ?>
    </h1>
    
    <?php
            Modal::begin([
                'header' => '<h4>Изменить заголовок</h4>',
                'options' => [
                    'id' => 'change-title',
                    'tabindex' => false,
                ],
            ]); 
            ?>          
            
            <?php $form = ActiveForm::begin(['id' => 'change-title',]);// 'action' => Url::to(['change-title', 'id' => $model->id]), 'method' => 'post']); ?>
        
            <?= $form->field($model, 'title')->textInput(['autocomplete'=>'off'])->label(false); ?>
        
            <div class="form-group">
                <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']); ?>
                <?= Html::button('Отмена', ['class'=>'btn btn-default', 'data-dismiss'=>'modal']); ?>
            </div>
        
            <?php ActiveForm::end();        
            
            Modal::end();
        ?>
    
    <div class="form-group">
        <?php if (!$client && $accessAssign): ?>
        <?php
            Modal::begin([
                'header' => 'Назначить сотруднику',
                'toggleButton' => ['label'=>'Переназначить', 'class'=>'btn btn-default'],
                'options' => [
                    'tabindex' => false,
                ],
            ]); 
            ?>          
            
            <?php $form = ActiveForm::begin(['id' => 'assign-user', 'action' => Url::to(['assign-ticket', 'id' => $model->id]), 'method' => 'post']); ?>
        
            <?= $form->field($assignForm, 'group_id')->widget(Select2::classname(), [
                'options' => [
                    'id' => 'select2-assign-group',
                    'placeholder' => 'Выберите отдел ...'
                ],
                'pluginOptions' => [
                    'language' => 'ru',
                    'ajax' => [
                        'url' => Url::to(['/ajax/assign/staff-group-list']),
                        'dataType' => 'json',
                    ],
                ],
                'pluginEvents' => [
                    'select2:select' => new JsExpression( 'function(e){
                        var userSel = $("select#select2-assign-user");
                        userSel.empty().prop("disabled", false);                                                
                    }'),
                ],
            ]); ?>
        
            <?= $form->field($assignForm, 'user_id')->widget(Select2::classname(), [                
                'options' => [
                    'id' => 'select2-assign-user',
                    'placeholder' => 'На весь отдел'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'language' => 'ru',
                    'disabled' => true,
                    'ajax' => [
                        'url' => new JsExpression('function () {
                            var group = $("select#select2-assign-group").select2("data")[0].id;                            
                            return "' . Url::to(['/ajax/assign/staff-user-list']) . '?group=" + group;
                        }'),
                        'dataType' => 'json',
                    ],
                ],
            ]); ?>

            <?= $form->field($assignForm, 'text')->textarea(['placeholder' => 'Причина (опционально, публично)']) ?>
        
            <?php $assignForm->ticket_id = $model->id; ?>
        
            <?php //= $form->field($assignForm, 'ticket_id')->hiddenInput()->label(false) ?>
        
            <div class="form-group">
                <?= Html::submitButton('Переназначить', ['class' => 'btn btn-primary']); ?>
                <?= Html::button('Отмена', ['class'=>'btn btn-default', 'data-dismiss'=>'modal']); ?>
            </div>
        
            <?php ActiveForm::end();        
            
            Modal::end();
        ?>
        
        <?php endif; if ($accessDelete): ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-default',
                'role' => 'button',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить эту заявку?',
                    'method' => 'post',
                ],
            ]); ?>
        <?php endif; ?>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" style="cursor: pointer; font-weight: bold;">Информация о заявке<span class="spoiler-trigger glyphicon glyphicon-collapse-down pull-right" style="cursor:pointer;"></span></div>
        <div id="detail-view" class="panel-body collapse">
            <?= DetailView::widget([
                'model' => $model,
                'options' => [
                    'tag' => 'div',
                ],
                'template' => '<div style="padding-bottom: .5em;"><strong style="display:block;">{label}</strong><span>{value}</span></div>',
                'attributes' => [
                    [
                        'label' => 'Организация',
                        'value' => function ($model)
                        {
                            return $model->user->organization->name;
                        }
                    ],
                    [
                        'attribute' => 'user_id',
                        'label' => 'Автор',
                        'value' => function ($model)
                        {
                            return $model->user->fullname;
                        }
                    ],
                    [
                        'attribute' => 'group_id',
                        'label' => 'Отдел',
                        'value' => function ($model)
                        {
                            return $model->group->name;
                        }
                    ],
                    [
                        'attribute' => 'assigned',
                        'label' => 'Назначено',
                        'value' => function ($model)
                        {
                            return (isset($model->assign_to)) ? $model->assigned->email : null;
                        }
                    ],
        //            'title',
                    [
                        'attribute' => 'Status',
                        'label' => 'Статус',
                        'value' => function ($model)
                        {
                            switch ($model->status)
                            {
                                case Ticket::TICKET_STATUS_CLOSE:
                                    return 'Закрыто';
                            }
                            return 'Открыто';

                        }
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'closed_at',
                        'label' => 'Закрыто',
                        'value' => function ($model)
                        {
                            return ($model->closed_at == 0)
                                ? null
                                : Yii::$app->formatter->asDatetime($model->closed_at);
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <ul class="chat">
    <?php foreach ($model->actions as $action): ?>
        <?php // if (get_class($action) == \app\models\Message::class): ?>
        <?php
            if ($action instanceof Message):
                $offset = ($client && $action->user->isUser()) || (!$client && $action->user->isStaff());
        ?>

            <div class="row message-box">
                <li class="message col-md-10 col-xs-10 <?= ($offset) ?: 'darker col-md-offset-2 col-xs-offset-2' ?> ">
                    <img src="<?= $action->user->avatar ?>" alt="Avatar" <?= ($offset) ?: 'class="right"' ?>><b><?= $action->user->fullname ?></b>
                    <p><?= Html::encode($action->text) ?></p>
                    <div class="message-files">
                        <ol>
                            <?php foreach ($action->messageFiles as $file): ?>
                            <li><a href="<?= Url::to(['/file/download', 'id' => $file->id]) ?>"><?= $file->name ?></a></li>
                            <?php endforeach; ?>                    
                        </ol>
                    </div>
                    <span class="time-<?= ($offset) ? 'right' : 'left' ?>"><?= Yii::$app->formatter->asDatetime($action->created_at) ?></span>
                </li>                
            </div>
        <?php else: ?>   
            <span class="event"><?= $action->format() ?></span>                     
        <?php endif; ?>                
    <?php endforeach; ?>
    </ul>
    
<?php if ($accessWrite): ?>
    
        <?php $form = ActiveForm::begin(['id'=>'form-input', 'options' => ['method' => 'post', 'enctype' => 'multipart/form-data']]) ?>
            <?= Html::fileInput('hide-input',null,['id' => 'loadFiles', 'multiple' => true, 'style'=>'display: none;']) ?>
            <div class="form-group">                
                    <?php /*= $form->field($input, 'text', ['errorOptions' => ['tag' => null]])->textInput(['class'=>'form-control input-text', 'placeholder' => 'Введите текст...', 'autocomplete' => 'off'])->label(false) */ ?>
                    <?= $form->field($input, 'text', ['errorOptions' => ['tag' => null]])->textarea(['class'=>'form-control input-text', 'placeholder' => 'Введите текст...', 'style'=>'height: 54px;'])->label(false)  ?>
                    <?php /*= Html::textInput('text', null, ['class'=>'form-control', 'placeholder' => 'Введите текст...']); */?>                                
            </div>
            <div id="input-files" class="form-group"></div>
            <?= $form->field($input, 'close')->hiddenInput(['id'=>'reassign-field'])->label(false); ?>
            <div class="form-group">    
                <?= Html::button(
                        '<span class="glyphicon glyphicon-paperclip"></span>&nbsp;Файл',
                        [
                            'class' => 'btn btn-default',
                            'onclick' => '(function ( $event ) { $("#loadFiles").click(); })();',
                        ]); ?>
                <div class="btn-group pull-right">
                <?= Html::submitButton('<span class="glyphicon glyphicon-send"></span>&nbsp;' 
                    . (($model->closed) ? 'Переоткрыть заявку' : 'Отправить'),
                    ['class' => ($model->closed) ? 'btn btn-primary' : 'btn btn-default']) ?>
                <?php if (!$client && $model->opened): ?>
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><?php //= Html::a( 'Закрыть заявку', [ 'close', 'id' => $model->id]) ?>
                        <?= Html::a( 'Закрыть заявку', '#', [
                            'id' => 'reassign-btn',
/*                            'onclick' => new \yii\web\JsExpression(
//                                '$("<input>").attr("type":"hidden").appendTo("form");'
                                    '$("#reassign-field").attr("value",1);' . 
                                    '$("#form-input").submit();'
                                ),*/
                            ]) ?>                    
                    </li>
                  </ul>
                <?php endif; ?>
                </div>
            </div>                
        <?php ActiveForm::end(); ?>

<?php       

//$this->registerJsFile(Yii::getAlias('@web/js/attach.files.js'),['depends' => ['yii\web\JqueryAsset']]);

//$files = json_encode($input->allFiles);

$script = <<< JS
$(document).ready(function(){    
    $("#form-input").attachFiles({
        'urlUpload' : '/ajax/file/upload',
        'urlRemove' : '/ajax/file/rm-upload?file=',
        'viewFile'  : '/file/temp?name=',
        'modelName' : 'Message',        
    });

    $("#reassign-btn").off("click").on("click", function(e){
        e.preventDefault();
        $("#reassign-field").attr("value",1);
        $("#form-input").submit();
    });        
});
JS;

$this->registerJs($script);

?>
<?php else: ?>
    <div class="alert alert-danger">У вас нет прав на запись в данную категорию.</div>
<?php endif; ?>    

<?php
$this->registerJs(
'$(".spoiler-trigger").parent().click(function(){
    $(this).next().collapse("toggle");
    $(".spoiler-trigger").toggleClass("glyphicon-collapse-down glyphicon-collapse-up");
});

$("#form-input textarea").keypress(function(e){
    if (e.keyCode == 13 && e.ctrlKey)
    {
        if (jQuery.trim(this.value))
        {
            $("form#form-input").submit();
        }
        return false;
    }
});

$("h1").hover(function(){
    $(this).append( $(\'<span class="glyphicon glyphicon-pencil" style="font-size: .5em; cursor: pointer;" data-toggle="modal" data-target="#change-title"></span>\') );
}, function() {
    $(this).find("span:last").remove();
});

$("#change-title").on("beforeSubmit", function(){
    var data = $("form#change-title").serialize();
    $.ajax({
        url: "' . Url::to(['update', 'id' => $model->id]) . '",
        type: "POST",
        data: data,
        success: function(r){
            if (r.error != null)
            {
                alert(r.error);
            } else {
                $("h1").html(r.data.text);
                $("#change-title").modal("toggle");
            }
        },
        error: function(){
            alert("error");
        }
    });
    return false;
});');    
?>
</div>
