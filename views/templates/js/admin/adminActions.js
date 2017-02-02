(function($){
    $(function(){
        
        
        function onLogoutClick(e) {
            $.getJSON(ctx.host + '/admin/logout')
             .then(handleLogout);
            $('#addMovieBtn').remove();
            $('#logoutBtn').remove();
        }
        
        function handleLogout(data) {
            data.ctx = ctx;
            var adminPanel = Handlebars.templates.admin(data);
            $('a[href=#logout]').remove();
            $('#userMsg').replaceWith(adminPanel);
            $('#submitBtn').click(onSubmitClick);
            
        }
        
        function loadAdmin(data) {
            if(data.login) {
                data.ctx = ctx;
                var adminPanel = Handlebars.templates.admin(data);
                $('#loginForm').replaceWith(adminPanel);
            } else {
                $('#login').val('');
                $('#password').val('');
            }
        }
        
        function setLogoutClickHandler() {
            $('#logoutBtn').click(onLogoutClick);
        }
        
        function onLoginSuccess() {
            window.location.href = '/admin/movies/';
        }
        
        function onLoginSubmit(e) {
            var data = { login: $('input#login').val(), password: $('input#password').val() };
            
            var config = {
                url: ctx.host + '/admin/login',
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(data)
            };
            $.ajax(config).then(loadAdmin)
                    .then(setLogoutClickHandler)
                    .then(onLoginSuccess);
            e.preventDefault();
        };
        
        if(window.location.href.indexOf('logout') == -1 && ctx.admin != null) {
            onLoginSuccess();
        } else {
            var adminPanel = Handlebars.templates.admin(ctx.admin);
            $(document.body).append(adminPanel);
            $('#loginForm').submit(onLoginSubmit);
        }
    });
}(jQuery.noConflict()));