(function($){
    var self = {};
    
    function fail() {
        self.deletedMovies = [];
    }
    
    function handleSelectionCreated(data) {
        if(data && data.ids) {
            self.selectedMovies = data.ids;
            var promise = $.ajax({
                url: ctx.host + '/admin/movies/set/' +data.id,
                method: 'DELETE',
                dataType: 'json',
                contentType: 'application/json'
            });
            return promise;
        }
    }
    
    function handleMoviesDeleted(requestData) {
        if(requestData !== undefined) {
            ctx.data.movies = ctx.data.movies.filter(function(elt, ind){
                return self.deletedMovies.indexOf(elt) == -1;
            });
            var data = ctx.data;
            data.hasChangeRights = ctx.data.hasChangeRights;
            var galleryContent = Handlebars.templates.moviesList(data);

            var childCnt = $('div.gallery').children().length;
            if(childCnt > 0 ) {
                $('div.gallery').children().remove();
            }

            $('div.gallery').append(galleryContent);
            $('div.gallery').trigger('moviesDeleted');
        }
    }

    function onDeleteClick(e) {
        var deletedIds = [];
        $('.deleteCheck:checked').each(function(ind, elt) {
            deletedIds.push(parseInt(elt.id.substring(11)));
        });
        
        var data = {
            ids: deletedIds
        };
        
        self.deletedMovies = ctx.data.movies.filter(function(elt, ind) { return data.ids.indexOf(elt.id) !== -1; });
        
        $.ajax({
            url: ctx.host +'/admin/movies/set',
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(data)
        }).then(handleSelectionCreated, fail)
          .then(handleMoviesDeleted, fail);
        
        e.preventDefault();
    }
    
    ctx.actionHandlers.delete = function() {
        // makes sure there's only one handler, for this event
        $('.deleteBtn').off('click').on('click', onDeleteClick);
    };
}(jQuery));