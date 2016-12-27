<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views/',
));

// ... definitions

$app['debug'] = true;

/*------- ROUTES ---------- */
$app->get('/', function() {
    return "wululu";
});

//route for rating toilet
$app->get('/rate/{id}', function (Silex\Application $app, $id) {

    $bathroom = $id;

    return $app['twig']->render('rate.twig', array(
        'bathroom' => $bathroom,
    ));
})->assert('id', '\d+');

//start server
$app->run();

