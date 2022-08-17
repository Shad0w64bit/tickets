(function( $ ) {
    $.fn.attachFiles = function( options ) {
        var settings = {
            'urlUpload' : 'upload.php',
            'urlRemove' : 'remove.php',
            'modelName' : 'Model',
            'inputFile' : '#loadFiles',
            'id'        : '#input-files',
            'viewFile'  : 'tmp.php?file='
        };
        
        var form = this;
        
        function setCloseEvent()
        {
            $(form).find(".attach-remove").off("click").on("click", function(){
                var item = $(this).closest("li");
                var file = item.find("input[name=\""+settings['modelName']+"[files][file][]\"]").attr("value");                

                $.ajax({
                    type: "post",
                    url: settings['urlRemove'] + file,
                    cache: false,
                    contentType: false,
                    processData: false
                });

                item.remove();
            });
        };

        function addFiles(files)
        {
            for (var i in files) 
            {
                $( settings['id'] ).find("ol").append(
                    "<li><input type=\"hidden\" name=\""+settings['modelName']+"[files][name][]\" value=\"" + files[i].name + "\" >" +
                    "<input type=\"hidden\" name=\""+settings['modelName']+"[files][file][]\" value=\"" + files[i].file + "\" >" +
                    "<input type=\"hidden\" name=\""+settings['modelName']+"[files][size][]\" value=\"" + files[i].size + "\" >" + 
                    "<a target=\"_blank\" href=\""+settings['viewFile'] + files[i].file + "\">"+files[i].name +" (" + files[i].size + ")"+"</a>" + 
                    "&nbsp;<span class=\"attach-remove glyphicon glyphicon-remove\"></span></li>"
                );
            }
        }
        
        if ( settings['files'] !== undefined)
        {
            addFiles( settings['files'] );
        }        
        
        $( settings['id'] )
            
            .append(
                '<ol></ol>' +
                '<div class="input-error alert alert-danger" style="display: none;"></div>' +
                '<div class="chat-bar" style="display: none;">' +
                    '<span>Загразка файлов на сервер...</span>' +
                    '<progress max="100" value="0" style="width:100%;"></progress>' +
                '</div>'
            );
        
        
        
        setCloseEvent();
        
        return this.each(function(){
            if ( options )
            {
                $.extend( settings, options );
            }
            
            if (!$(this).is('form'))
            {
                return console.log('AttachFiles: Element type is not a Form!');
            }
            
            var form = this;
            
            $( settings['inputFile'] ).change(function (){
                var formData = new FormData( form );
                $.each($("input[type=file]"), function(i, obj) {
                    $.each(obj.files,function(j, file){
                        formData.append('files['+j+']', file);
                    });
                });

                var bar = $( settings['id'] ).find(".chat-bar");
                var progress = bar.find("progress");
                var status = $( settings['id'] ).find(".input-error");

                $.ajax({
                    type: "post",
                    url: settings['urlUpload'],
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    beforeSend: function (){
                        status.text("").hide();
                        bar.show();
                        progress.attr("value", 0);
                    },
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            var percentComplete = Math.floor(evt.loaded / evt.total * 100);
                            progress.attr("value", percentComplete);                                  
                        }, false);
                        return xhr;
                    },
                    success: function(data) {
                        if ("error" in data)
                        {
                            status.text(data.error).show();
                            bar.hide();
                            progress.attr("value", 0);
                        } else {       
                            $( settings['inputFile'] ).wrap('<form>').closest('form').get(0).reset();
                            $( settings['inputFile'] ).unwrap();

                            status.text(data.error).hide();
                            bar.hide();
                            progress.attr("value", 0);
                            addFiles(data.files);
                            setCloseEvent();
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.readyState);
                        console.log(xhr.status);
                        if ( !((xhr.readyState === 4) && (xhr.status === 200)) )
                        {  
                            console.log(xhr.responseText);
                            bar.hide();
                            progress.attr("value", 0);
                            status.text("Не удалось загрузить файлы!").show();
                        }
                    }
                });
            });
            //SetCloseEvent;
        });
    };
})(jQuery);