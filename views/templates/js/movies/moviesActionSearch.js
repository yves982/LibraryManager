'use strict';
(function($){
    var self = {};
    function titleMatch(title, search) {
        return title.toLowerCase().indexOf(search.toLowerCase()) == 0;
    }
    
    function authorMatch(author, search) {
        return author.toLowerCase().indexOf(search.toLowerCase()) == 0;
    }
    
    function isEmpty(search) {
        // charCode == 8 -> backspace, 46 -> delete
        return search.length == 0;
    }
    
    function onKeyDown(e) {
        var search = $('#searchInput').val();
        // 44 -> + | 45 -> , | 32 -> space | _ | ' | : | ; | . | " | backspace | delete
        if(e.which >= 65 && e.which <=90 || [44,45,32,56,52,58,59,46,51, 8, 46].indexOf(e.which) != -1) {
            if([8,46].indexOf(e.which) != -1) {
                search = search.substring(0, search.length-1);
            } else if ([51,52,56].indexOf(e.which) != -1) {
                var charInd = [51,52,56].indexOf(e.which);
                search = search + ['"', "'", "_"][charInd];
            } else {
                search = search + String.fromCharCode(e.which);
            }
            
            if(!isEmpty(search)) {
                self.matchingElts = ctx.data.movies.filter(function(elt, ind){
                return authorMatch(elt.author, search)
                        || titleMatch(elt.title, search);
                });
            } else {
                self.matchingElts = ctx.data.movies;
            }
        } 

        if(self.matchingElts !== undefined) {
            var data = { movies: self.matchingElts, hasChangeRights: ctx.hasChangeRights };
            
            var galleryContent = Handlebars.templates.moviesList(data);
            var childCnt = $('div.gallery').children().length;
            if(childCnt > 0 ) {
                $('div.gallery').children().remove();
            }
            $('div.gallery').append(galleryContent);
            $('div.gallery').trigger('searchPerformed');
        }
        
    }
    
    ctx.actionHandlers.search = function() {
      $('#searchInput').off('keydown').on('keydown', onKeyDown);  
    };
}(jQuery.noConflict()));