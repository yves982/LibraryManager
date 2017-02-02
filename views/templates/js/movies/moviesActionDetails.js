'use strict';
(function($){
    
    function handleBoxClosed() {
        $('div.gallery').trigger('detailsClosed');
    }
   
    
    function onDetailsClick(e) {
        var movieDiv = $(e.target).parent().parent();
        var movieId = movieDiv.find('.movieId').val();
        var movie = ctx.data.movies.filter(function(elt, ind){ 
            return elt.id == movieId; 
        })[0];
        
        if($(e.target).find('img[href*="?"]').length > 0) {
            movie.mode = 'edit';
        }
       
       
        var movieContent = Handlebars.templates.movieDetails(movie);
        var box = new jBox('Modal', {
                title: movie.title+'<span id="closeTitle" class="glyphicon glyphicon-off pull-right" style="cursor:default;"></span>',
                width: 600,
                draggable: 'title',
                content : movieContent,
                fade: 600,
                overlay: false,
                closeButton: false,
                closeOnClick: false,
                closeOnEsc: false
            });
        box.onClose = handleBoxClosed;
        box.open();
        
        $('#closeTitle').click(box.destroy.bind(box));
        
        e.preventDefault();
    }
    
    ctx.actionHandlers.details = function() {
        $('.movie > a').off('click').on('click', onDetailsClick);
    };
}(jQuery.noConflict()));


