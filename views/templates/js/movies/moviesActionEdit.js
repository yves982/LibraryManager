'use strict';
(function($){
    var self = {};
    function handleMovieEdited(data) {
        data.hasChangeRights = true;
        data.mode='edit';
        ctx.editedMovies = ctx.editedMovies || [];
        ctx.editedMovies.push(data.id);
        var movieContent = Handlebars.templates.movieBox(data);
        $('.movie > input.movieId[value="' +data.id+ '"]')
            .parent().replaceWith(movieContent);
        delete data.hasChangeRights;
        var matchingMovies = ctx.data.movies.filter(function(elt, ind){ 
            var matched = elt.id == data.id;
            if(matched) {
                ctx.data.matchedIndex = ind;
            }
            return matched;
        });
        if(ctx.data.matchedIndex !== undefined) {
            ctx.data.movies[ctx.data.matchedIndex] = data;
            delete ctx.data.matchedIndex;
        }
        ctx.jbox.destroy();
        ctx.actionHandlers.edit();
        delete ctx.jbox;
    }
    
    function getEditData() {
        var editData = {};
        var newData = {
            id: self.originalMovie.id,
            title: $('#titleInput').val(),
            author: $('#authorInput').val(),
            year: $('#yearInput').val(),
            content: $('#contentInput').val(),
            description:$('#descriptionText').val()
        };
        
        if(self.imageType !== undefined) {
            newData.image = 'data:' +self.imageType+ ';base64,' +self.imageEncodedContent;
        }
        
        for(var attr in self.originalMovie) {
            if(['image', 'year', 'id', 'hasChangeRights'].indexOf(attr) == -1 && (
                        self.originalMovie[attr] && newData[attr] && self.originalMovie[attr].toLowerCase() !== newData[attr].toLowerCase() 
                        || [null, ""].indexOf(self.originalMovie[attr]) != -1 
                )
            ) {
                editData[attr] = newData[attr];
            } else if (!newData[attr] && newData[attr] !== editData[attr]) {
                editData[attr] = '';
            }
        }
        
        if(self.originalMovie.image !== newData.image && newData.image !== '') {
            editData.image = newData.image;
        }
        
        if(self.originalMovie.year != newData.year && newData.year != null) {
            editData.year = newData.year;
        }
        
        return editData;
    }
    
    function sendRequest() {
        var editData = getEditData();

        var config = {
            url: ctx.host + '/admin/movie/' +self.originalMovie.id,
            dataType: 'json',
            contentType: 'json',
            method: 'PATCH',
            data: JSON.stringify(editData)
        };

        $.ajax(config)
        .then(handleMovieEdited);
    }
    
    

    function setImagePreview() {
        if(self.image) {
            var content = window.URL.createObjectURL(self.image);
            $('#movieImg').attr('src', content);
            window.URL.revokeObjectURL(content);
        } else {
            var content = 'data:' +self.imageType+ ';base64,' +self.imageEncodedContent;
            $('#movieImg').attr('src', content);
        }
        
    }

    function setEditMovieFormHandlers() {
        $('#editMovieBtn').click(sendRequest);
        $('#movieEditForm').submit(sendRequest);
    }

    function onEditClick(e) {
        ctx.currentMovie = null;
        var id = $(e.target).parent().attr('id').substring(7);
        var matchingMovies = ctx.data.movies.filter(function(elt, ind) { 
            return elt.id == id; 
        });
        if(matchingMovies.length > 0) {
            var editForm = Handlebars.templates.movieEdit(matchingMovies[0]);
            if(ctx.jbox == null) {
                ctx.jbox = new jBox('Modal', {
                    title: 'Editer Un Film <span id="closeEdit" class="glyphicon glyphicon-off pull-right" style="cursor:default;"></span>',
                    theme: 'ModalBorder',
                    width: 350,
                    overlay: false,
                    content : editForm,
                    draggable: 'title',
                    fade: 600,
                    closeOnClick: false,
                    closeButton: false,
                    closeOnEsc: false
                });
            } else {
                ctx.jbox.setContent(editForm);
                ctx.jbox.setTitle('Editer un Film <span id="closeEdit" class="glyphicon glyphicon-off pull-right" style="cursor:default;"></span>');
            }
            ctx.jbox.open();
            
            $('#closeEdit').click(ctx.jbox.close.bind(ctx.jbox));
            
            self.originalMovie = matchingMovies[0];
            $('#imageInput').change(ctx.fileHandler.readImage.bind(null, setImagePreview, self));
            setEditMovieFormHandlers();
            e.preventDefault();
        }
    }
    
    ctx.actionHandlers.edit = function() {
        $('.editBtn').off('click').on('click', onEditClick);
    };
}(jQuery.noConflict()));

