<?php 

    require __DIR__ . '/../vendor/autoload.php';

    use Embryo\ServerSideRendering\SSR;

    $v8js = new \V8Js;
    $ssr = new SSR($v8js);

    echo $ssr
        ->context([
            'user' => [
                'name' => 'Davide'
            ]
        ])
        ->script("print(context.user.name)")
        ->render();