<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// ... definitions

$app->run();

$app['debug'] = true;

//route for rating toilet
$app->get('/rate/{id}', function (Silex\Application $app, $id) use ($bathrooms) {

    return $output;
})->assert('id', '\d+');