'use strict';
(function($){
    ctx.actionHandlers = {};
    ctx.fileHandler = {
        handleFileRead: function(onSuccess, imageFile, data, e) {
            var uint8Result = new Uint8Array(e.target.result);
            var content = String.fromCharCode.apply(null, uint8Result);
            var encodedContent = btoa(content).replace(/.{76}(?=.)/g,'$&\n');
            data.image = imageFile;
            data.imageType = imageFile.type;
            data.imageEncodedContent = encodedContent;
            onSuccess();
        },

        handleReadError: function(e) {
            alert('Error: (' +e.target.error.code +') - '+e.target.error.message);
        },

        onFallbackLoad: function(onSuccess, data, e) {
            var img = $(document.frames[0].document.body).find('#imageFallback');
            var imgSrc = img.attr('src');
            data.imageType = imgSrc.substring(5, imgSrc.indexOf(';'));
            data.imageEncodedContent = imgSrc.substring(imgSrc.indexOf(',')+1);
            $('span:contains(Choix de fichier)').append(data.extras.imageInput);
            onSuccess();
        },

        readImage: function(onSuccess, data, e) {
            if($('#imageInput')[0].files) {
                var imageFile = $('#imageInput')[0].files[0];
                var reader = new FileReader();
                reader.onloadend = window.ctx.fileHandler.handleFileRead.bind(null, onSuccess, imageFile, data);
                reader.onerror = window.ctx.fileHandler.handleReadError.bind(null, data);
                reader.readAsArrayBuffer(imageFile);
            } else {
                var fallbackFrameBody = $(document.frames[0].document.body);
                var hiddenForm = Handlebars.templates.imageFallback();
                fallbackFrameBody.append(hiddenForm);
                
                data.extras = {
                    imageInput: $('#imageInput').clone(true, true)
                };
                
                fallbackFrameBody.find('#imageInput').replaceWith($('#imageInput'));
                $(window.document).on('iframeready', window.ctx.fileHandler.onFallbackLoad.bind(null, onSuccess, data));
                $(fallbackFrameBody).find('#fallbackImageForm').submit();
            }
            
            e.preventDefault();
        }
    };
    
}(jQuery.noConflict()));