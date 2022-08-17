<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'My Yii Application';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            
            <ul class="chat">
                <li class="message">
                  <img src="https://www.w3schools.com/w3images/bandmember.jpg" alt="Avatar">
                  <p>Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today? Hello. How are you today?</p>
                  <span class="time-right">11:00</span>
                </li>

                <li class="message darker">
                  <img src="https://www.w3schools.com/w3images/avatar_g2.jpg" alt="Avatar" class="right">
                  <p>Hey! I'm fine. Thanks for asking! Hey! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking! I'm fine. Thanks for asking!</p>
                  <span class="time-left">11:01</span>
                </li>

                <li class="message">
                  <img src="https://www.w3schools.com/w3images/bandmember.jpg" alt="Avatar">
                  <p>Sweet! So, what do you wanna do today?</p>
                  <span class="time-right">11:02</span>
                </li>

                <li class="message darker">
                  <img src="https://www.w3schools.com/w3images/avatar_g2.jpg" alt="Avatar" class="right">
                  <p>Nah, I dunno. Play soccer.. or learn more coding perhaps?</p>
                  <span class="time-left">11:05</span>
                </li>
            </ul>
            
        </div>
    </div>
</div>


<?php $form = ActiveForm::begin(['id'=>'form-input', 'options' => ['method' => 'post', 'enctype' => 'multipart/form-data']]) ?>
    <?= Html::fileInput('hide-input',null,['id' => 'loadFiles', 'multiple' => true, 'style'=>'display: none;']) ?>
    <div class="form-group">                
            <?php /*= $form->field($input, 'text', ['errorOptions' => ['tag' => null]])->textInput(['class'=>'form-control input-text', 'placeholder' => 'Введите текст...', 'autocomplete' => 'off'])->label(false) */ ?>
            <?= $form->field($input, 'text', ['errorOptions' => ['tag' => null]])->textarea(['class'=>'form-control input-text', 'placeholder' => 'Введите текст...', 'style'=>'height: 54px;'])->label(false)  ?>
            <?php /*= Html::textInput('text', null, ['class'=>'form-control', 'placeholder' => 'Введите текст...']); */?>                                
    </div>
    <div id="input-files" class="form-group"></div>
<?php /*
    <div id="input-files" class="form-group input-files">
        <ol>                    
            <?php foreach ($input->allFiles as $file): ?>
                <li>
                    <input type="hidden" name="Message[files][name][]" value="<?= $file->name ?>" >
                    <input type="hidden" name="Message[files][file][]" value="<?= $file->file ?>" >
                    <input type="hidden" name="Message[files][size][]" value="<?= $file->size ?>" >
                    <a href="/file/temp?name=<?= $file->file ?>"><?= $file->name ?> (<?= $file->size ?>)</a>&nbsp;
                    <span class="attach-remove glyphicon glyphicon-remove"></span>
                </li>
            <?php endforeach; ?>                    
        </ol>
<?php /*
        <div class="input-error alert alert-danger" style="display: none;"></div>
        <div class="progress" style="display: none;">
            <span>Загразка файлов на сервер...</span>
            <progress max="100" value="0" style="width:100%;"></progress>
        </div>
 * </div>
 * */?>
    
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
        </div>
    </div>                
<?php ActiveForm::end(); ?>

<?php       
$this->registerJsFile(Yii::getAlias('@web/js/attach.files.js'),['depends' => ['yii\web\JqueryAsset']]);

//$files = json_encode($input->allFiles);

$script = <<< JS
$(document).ready(function(){    
    $("#form-input").attachFiles({
        'urlUpload' : '/client/ticket/upload',
        'urlRemove' : '/client/ticket/rm-upload?file=',
        'viewFile'  : '/file/temp?name=',
        'modelName' : 'Message',        
    });
});
JS;

$this->registerJs($script);
