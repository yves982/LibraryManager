<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title></title>
    </head>
    <body>
        <img src="<?php echo $context->src ?>" id="imageFallback">
        <script type="text/javascript">
            window.parent.jQuery(window.parent.document).trigger('iframeready');
        </script>
    </body>
</html>
