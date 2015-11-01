<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
         <meta http-equiv="X-UA-Compatible" content="IE=Edge">
         <meta name="robots" content="noindex,nofollow">
        <title>Film - <?php echo $context->movie->title ?></title>
    </head>
    <body>
        <script type="text/javascript" src="/views/templates/js/jquery-2.1.4.min.js" defer></script>
        <script type="text/javascript" src="/views/templates/js/handlebars.runtime-v4.0.2.js"></script>
        <h1 class="title"><?php echo $context->movie->title; ?></h1>
        <img src="<?php echo $context->movie->image; ?>" class="movieImg"/>
        <p class="movieDesc"><?php echo $context->movie->description; ?></p>
        <a href="<?php echo $context->movie->content; ?>" class="movieLink">watch</a>
    </body>
</html>
