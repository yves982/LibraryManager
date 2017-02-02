'use strict';
(function($){
    $(function() {
        function registerActions() {
            ctx.actionHandlers.add();
            ctx.actionHandlers.edit();
            ctx.actionHandlers.logout();
            ctx.actionHandlers.delete();
            ctx.actionHandlers.search();
            ctx.actionHandlers.details();
        }

        function onMoviesReceived(data) {

            $(data.movies).each(function(ind, elt){
                if(ctx.editedMovies && ctx.editedMovies.indexOf(elt.id) !== -1) {
                    elt.mode = 'edit';
                }
            });

            ctx.data = data;
            var galleryContent = Handlebars.templates.moviesList(data);
            var childCnt = $('div.gallery').children().length;
            if(childCnt > 0 ) {
                $('div.gallery').children().remove();
            }
            $('div.gallery').append(galleryContent);
            registerActions();
        }

        function getHistoUrl(firstLetter) {
            var hashPos = window.location.href.lastIndexOf('#');
            var histoUrl = window.location.href;
            if(hashPos != -1) {
                histoUrl = histoUrl.substring(0, hashPos) +'#'+ firstLetter;
            }
            return histoUrl;
        }

        function pushHistoUrl(firstLetter) {
            if(window.history.pushState) {
                window.history.pushState({ firstLetter: firstLetter }, '', '#' +firstLetter);
            } else {
                window.location.hash = firstLetter;
            }
        }

        function onLetterClick(e) {
            var firstLetter = $(e.target).text().toLowerCase();
            loadFirstLetterBloc(firstLetter);
            pushHistoUrl(firstLetter);
            e.preventDefault();
        }

        function loadFirstLetter(firstLetter) {
            var prefix = '';
            if(ctx.hasChangeRights) {
                prefix = '/admin';
            }
            var url = ctx.host +prefix+ '/movies/firstLetter/' +firstLetter;
            if(firstLetter == '') {
                url = url.substring(0, url.length - 12);
            }
            $.getJSON(url)
            .then(onMoviesReceived);
            $('#firstLetterForm').remove();
            $('.currentLetter').val(firstLetter);
        }


        function loadFirstLetterBloc(firstLetter, blocNum) {
            updateNbElementsMax();

            var nbElementsMax = ctx.nbElementsMax;
            if(!blocNum) {
                blocNum = 1;
            }

            var prefix = '';
            if(ctx.hasChangeRights) {
                prefix = '/admin';
            }
            var url = ctx.host +prefix+ '/movies/firstLetter/' +firstLetter+'/' +nbElementsMax+'/'+blocNum;
            if(firstLetter == '') {
                url = url.substring(0, url.length - 12);
            }
            $.getJSON(url)
            .then(onMoviesReceived)
            .then(updatePagination);
            $('#firstLetterForm').remove();
            $('.currentLetter').val(firstLetter);
        }

        function onFirstLetterClick(e){
            var firstLetter = $('#firstLetterInput').val().toLowerCase();
            loadFirstLetterBloc(firstLetter);
            pushHistoUrl(firstLetter);
            e.preventDefault();
        }

        function onFirstLetterFormSubmit(e) {
            var firstLetter = $('#firstLetterInput').val().toLowerCase();
            $('#firstLetterForm').attr('action', '#' +firstLetter);
            $('#firstLetterBtn').click();
            e.preventDefault();
        }

        function onFirstLetterOtherClick(e) {
            if($('#firstLetterBtn').length == 0) {
                var firstLetterForm = Handlebars.templates.moviesListFirstLetterOther();
                $(e.target).after(firstLetterForm);
                $('#firstLetterBtn').click(onFirstLetterClick);
                $('#firstLetterInput').focus();
                $('#firstLetterForm').submit(onFirstLetterFormSubmit);
            }
            e.preventDefault();
        }

        function addActionPanel() {
            var actionPanel = Handlebars.templates.actionPanel(ctx);
            $('div.main-panel').append(actionPanel);
        }

        function addSearchBar() {
            var searchBar = Handlebars.templates.searchBar();
            $('div#row-nav1').append(searchBar);
        }

        function addDisclaimerSwitch() {
            var disclaimerSwitch = Handlebars.templates.disclaimerSwitch();
            $('div#row-nav1').append(disclaimerSwitch);
        }

        function handleUrlHash() {
            var hashPos = window.location.href.lastIndexOf('#');
            if(hashPos != -1) {
                var requestedLetter = window.location.href.substring(hashPos + 1, hashPos + 2);
                if($('.currentLetter').val() != requestedLetter) {
                    loadFirstLetterBloc(requestedLetter);
                }
            } else {
                loadFirstLetterBloc('');
            }

        }

        function addLoginIfNecessary() {
            if(!ctx.hasChangeRights) {
                var loginCtx = { secureHost: ctx.host.replace('http:', 'https:') };
                var loginSection = Handlebars.templates.loginSection(loginCtx);
                $('div.gallery').after(loginSection);
            }
        }

        function addNavLinks() {
            var navContent = Handlebars.templates.moviesListNav();
            $('section[role=navigation]').first().append(navContent);
        }

        function setLetterHandlers() {
            $('a.firstLetter').click(onLetterClick);
            $('#firstLetterOther').click(onFirstLetterOtherClick);
        }

        function handleBackHistory(e) {
            handleUrlHash();
        }

        function setBackHandler() {
            if(window.history.pushState) {
                $(window).bind('popstate', handleBackHistory);
            } else {
                $(window).bind('hashchange', handleBackHistory);
            }
        }

        function setCallbackEvents() {
            $('div.gallery').on('movieAdded', registerActions);
            $('div.gallery').on('moviesDeleted', registerActions);
            $('div.gallery').on('searchPerformed', registerActions);
            $('div.gallery').on('detailsClosed', registerActions);
        }

        function closeDisclaimer() {
            $('#disclaimer-footer').toggleClass('hide', true);
        }

        function toggleDisclaimer() {
            $('#disclaimer-footer').toggleClass('hide');
        }

        function setDisclaimerHandlers() {
            $('#closeDisclaimer').click(closeDisclaimer);
            $('#disclaimerSwitch').change(toggleDisclaimer);
        }

        function updatePagination() {
            var firstLetter = $('.currentLetter').val();
            updateNbElementsMax();
            var pageCnt = Math.floor(ctx.data.cnt / ctx.nbElementsMax)
             + (ctx.data.cnt % ctx.nbElementsMax == 0 ? 0 : 1);
            var pageNumbers = [];
            for(var i=0; i < pageCnt; i++) {
                var pageNum = i+1;
                pageNumbers.push(pageNum);
            }
            var data = {pages: pageNumbers, firstLetter: firstLetter, nbElementsMax: ctx.nbElementsMax };
            var pagination = Handlebars.templates.pagination(data);

            if($('.pagination').length == 0) {
                $('div.gallery').after(pagination);
            } else {
                $('.pagination').replaceWith(pagination);
            }
            setPaginationHandlers();
        }

        function updateNbElementsMax() {
            ctx.nbElementsMax = Number.parseInt($('#nbElementsMax').val());

            if(!ctx.nbElementsMax) {
                ctx.nbElementsMax = 3;
            }
        }

        function paginationClicked(e) {
            $('.search').val('');
            var blocNum = Number.parseInt($(e.target).text());
            var firstLetter = $('.currentLetter').val();
            loadFirstLetterBloc(firstLetter, blocNum);
        }

        function setPaginationHandlers() {
            $('#nbElementsMax').change(updateNbElementsMax);
            $('ul.pagination li > a').click(paginationClicked);
        }

        function moviesListInit() {
            addNavLinks();
            setLetterHandlers();

            addActionPanel();
            addSearchBar();
            addDisclaimerSwitch();
            registerActions();
            handleUrlHash();
            addLoginIfNecessary();
            setBackHandler();
            setCallbackEvents();
            setDisclaimerHandlers();
        }

        moviesListInit();
    });
}(jQuery.noConflict()));
