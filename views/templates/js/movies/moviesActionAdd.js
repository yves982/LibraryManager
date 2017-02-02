'use strict';
(function($){
    var self = {};
    function handleMovieAdded(data) {
        var lastRow = $('div.gallery div.row:last-child');
        data.hasChangeRights = true;
        var movieContent = Handlebars.templates.movieBox(data);
        var currentLetter = $('.currentLetter').val() ? $('.currentLetter').val() : 'a';
        if(data.title[0].toLowerCase() == currentLetter) {
            if(lastRow.length > 0 && lastRow.children('.movie').length < 4) {
                lastRow.append(movieContent);
            } else {
                var movieRow = Handlebars.templates.movieBoxInRow(data);
                $('div.gallery').append(movieRow);
            }
        }
        
        ctx.data.movies.push(data);
        
        ctx.jbox.destroy();
        delete ctx.jbox;
        
        $('div.gallery').trigger('movieAdded', { id: data.id });
    }

    function sendRequest() {
        var addData = {
            title: $('#titleInput').val(),
            author: $('#authorInput').val(),
            year: $('#yearInput').val(),
            content: $('#contentInput').val(),
            description: $('#descriptionText').val(),
            image: 'data:' +self.imageType+';base64,' +self.imageEncodedContent
        };

        if(self.imageEncodedContent == null) {
            addData.image = null;
        }

        var config = {
            url: ctx.host + '/admin/movie',
            dataType: 'json',
            contentType: 'json',
            method: 'POST',
            data: JSON.stringify(addData)
        };

        $.ajax(config)
        .then(handleMovieAdded);
    }


    function setImagePreview() {
        if(self.image == null) {
           $('#movieImg').attr('src', 'data:' +self.imageType+ ';base64,' +self.imageEncodedContent);
        } else {
            var content = window.URL.createObjectURL(self.image);
            $('#movieImg').attr('src', content);
            window.URL.revokeObjectURL(content);
        }
    }

    function setAddMovieFormHandlers() {
        $('#addMovieBtn').click(sendRequest);
        $('#movieAddForm').submit(sendRequest);
    }

    function onAddClick(e) {
        var addForm = Handlebars.templates.movieAdd();
        
        if(ctx.jbox == null) {
            ctx.jbox = new jBox('Modal', {
                title: 'Ajouter Un Film <span id="closeAdd" class="glyphicon glyphicon-off pull-right" style="cursor:default;"></span>',
                width: 350,
                overlay: false,
                content : addForm,
                draggable: 'title',
                fade: 600,
                closeOnClick: false,
                closeOnEsc: false,
                closeButton: false
            });
        } else {
            ctx.jbox.setContent(addForm);
            ctx.jbox.setTitle('Ajouter un Film <span id="closeAdd" class="glyphicon glyphicon-off pull-right" style="cursor:default;"></span>');
        }
        ctx.jbox.open();
        
        $('#closeAdd').click(ctx.jbox.close.bind(ctx.jbox));
        
        $('#imageInput').change(ctx.fileHandler.readImage.bind(null, setImagePreview, self));
        setAddMovieFormHandlers();
        e.preventDefault();
    }
    
    ctx.actionHandlers.add = function() {
        $('#addBtn').off('click').on('click', onAddClick);
    };
}(jQuery.noConflict()));
    