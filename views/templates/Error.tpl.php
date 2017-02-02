<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title>Error</title>
    </head>
    <body>
        <div class="messsage"><?php echo $ex->getMessage(); ?></div>
        <div class="location">
            <span class="file"><?php echo $ex->getFile(); ?></span>
            <span class="line">(L<?php echo $ex->getLine(); ?>)</span>
        </div>
        <div class="trace"><?php echo $ex->getTraceAsString(); ?></div>
    </body>
</html>
