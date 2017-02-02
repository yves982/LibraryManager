<!doctype HTML>
<html>
    <head>
        <meta charset="utf-8" />
         <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
         <meta name="viewport" content="width=device-width, initial-scale=1">
         <meta name="robots" content="noindex,nofollow">
        <title>Login</title>
        <link rel="stylesheet" href="/views/templates/dist/css/admin/admin.min.css">
    </head>
    
    <body>
        
        <script type="text/javascript">
            var ctx = {
                host: '<?php echo Config::BASE_HOST; ?>'
            };
        </script>
        
        <?php if(empty($context->admin)): ?>
            <script type="text/javascript">
                ctx.admin = null;
            </script>
            <noscript>
                <form id="loginForm" action="/admin/login" method="POST">
                    <input type="text" id="login" name="login">
                    <input type="password" id="password" name="password">
                    <input type="hidden" name="access" value="true">
                    <button type="submit" id="submitBtn">login</button>
                </form>
            </noscript>
        <?php else: ?>
            <script type="text/javascript">
                ctx.admin = <?php echo json_encode($context->admin); ?>;
            </script>
            <noscript>
                <span class="user">Bienvenue <?php echo $context->admin->login ?></span>
                <a href='<?php echo Config::BASE_HOST; ?>/admin/logout'>
                    <button type="button" class="logout" id ="logoutBtn">Déconnexion</button>
                </a>
            </noscript>
        <?php endif; ?>
        <script type="text/javascript" src="/views/templates/dist/js/admin/admin.min.js"></script>
    </body>
</html>