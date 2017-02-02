'use strict';
(function($) {
    function handleRightLoss() {
        $('section.rightActions').remove();

        var currentLetter = $('.currentLetter').val().toLowerCase();
        var letterHash = '';
        if(currentLetter != '') {
            letterHash = '#' +currentLetter;
        }
        window.location.href='/movies/' +letterHash;
    }

    function onLogoutClick(e) {
        $.getJSON(ctx.host + '/admin/logout')
         .then(handleRightLoss);
        
        e.preventDefault();
    }
    
    ctx.actionHandlers.logout = function() {
        $('#logoutBtn').off('click').on('click', onLogoutClick);
    };
    
}(jQuery.noConflict()));