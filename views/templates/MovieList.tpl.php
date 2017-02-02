<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title>Movies</title>
        <link rel="stylesheet" href="/views/templates/dist/css/movies/movies.min.css">
    </head>
    <body>
        <script type="text/javascript">
            var ctx = {
                host: '<?php echo Config::BASE_HOST; ?>',
                data: { movies: [] },
                hasChangeRights: <?php
                if($context->hasChangeRights):
                    echo 'true';
                else :
                    echo 'false';
                endif; ?>
            };
        </script>

        <section role="navigation">
            <noscript>
            <nav>
                <?php
                $linkBase = '/movies/firstLetter/';
                if ($context->hasChangeRights):
                    $linkBase = '/admin' . $linkBase;
                endif;
                for($i=1; $i<26; $i++):
                    $content = chr(64+$i);
                    $id = 'firstLetter' . $content;
                    $link = $linkBase . chr(95+$i);
                    $nextJoin = '|';
                    if($i == 25) {
                        $nextJoint = '';
                    }
                ?>
                    <a href="<?php echo $link; ?>" id="<?php echo $id; ?>"><?php echo $content; ?></a> <?php echo $nextJoin; ?>
                <?php
                endfor; ?>
            </nav>
            </noscript>
        </section>
        <?php if($context->hasChangeRights) : ?>
            <noscript>
            <section class="rightActions">
                <a class='logoutBtn' id='logoutBtn' href='#logout'><span class='glyphicon glyphicon-log-out'></span></a>
                <a class='addBtn' id='addBtn' href='#movieAddForm'><span class='glyphicon glyphicon-plus-sign'></span></a>
            </section>
            </noscript>
        <?php
        endif; ?>
       <?php
       foreach ($context->components as $comp) :
           echo $comp->render();
       endforeach;
        ?>
        <div class="container-fluid gallery">
        <?php foreach($context->moviesBlocs as $i => $movie): ?>
            <script type="text/javascript">
                ctx.data.movies.push({
                    <?php
                    $movieJson = json_encode($movie);
                    echo substr($movieJson, 1, strlen($movieJson)-2);
                    ?>
                });
            </script>
            <?php if($i%4 == 0) :?>
            <div class="row">
            <?php endif; ?>
            <div class="col-md-3 movie">
                    <div class="title"><a href="/movie/<?php echo $movie->id; ?>"><?php echo $movie->title; ?></a></div>
                    <?php if($context->hasChangeRights) {?>
                        <section class='editActions'>
                            <a class='editBtn' id='editBtn<?php echo $movie->id; ?>' href='#edit'><span class='glyphicon glyphicon-pencil'></span></a>
                            <a class='deleteBtn' id='deleteBtn<?php echo $movie->id; ?>' href='#delete'><span class='glyphicon glyphicon-trash'></span></a>
                        </section>
                    <?php }?>
                    <a href="<?php echo $movie->content; ?>">
                        <image src="<?php echo $movie->image; ?>" />
                    </a>
                </div>
            <?php if( (($i+1)%4 == 0) ): ?>
            </div>
            <?php endif; ?>
        <?php
        endforeach; ?>
        <?php if(count($context->moviesBlocs) % 4 != 0): ?>
            </div>
        <?php endif; ?>
        </div>

        <?php if(!$context->hasChangeRights): ?>
        <noscript>
            <section class="takeRights">
                <a href="<?php echo str_replace('http:', 'https:', Config::BASE_HOST); ?>/admin/login" target="_self">Se connecter</a>
            </section>
        </noscript>
        <?php endif; ?>
        <input type="hidden" class="currentLetter" value="">
        <footer class="col-md-4 col-md-offset-4" id="disclaimer-footer">
                <a class="close" id="closeDisclaimer">x</a>
                <div class="panel panel-default footer-panel">
                    <div class="panel-heading text-center">
                        <h4>Disclaimer</h4>
                    </div>
                    <div class="panel-body">
                        <p>All content on this site was freely taken from <a href="http://www.imdb.com/">imdb</a>.
                           It's intended to serve solely for a school project to show a gallery demo.
                           Please note, there is no guarantee on the accuracy of any information on this website
                           as it's a demo, hence subject to changes and tests.
                        </p>
                    </div>
                </div>
            </footer>
        <script type="text/javascript" src="/views/templates/dist/js/movies/movies.min.js"></script>
    </body>
</html>
