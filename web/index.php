<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once('config.php');

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views/',
));

// ... definitions

$app['debug'] = true;

/*------- ROUTES ---------- */
$app->get('/', function(Silex\Application $app) {
    $db = db();

    $restrooms = $db->query('SELECT * FROM Restrooms;');

    return $app['twig']->render('list.twig', ['restrooms' => $restrooms]);
});

//route for rating toilet
$app->get('/rate/{id}', function (Silex\Application $app, $id) {

    $bathroom = $id;

    return $app['twig']->render('rate.twig', array(
        'bathroom' => $bathroom,
    ));
})->assert('id', '\d+');

$app->post('/rate/{id}', function (Silex\Application $app, $id) {

    $db = db();

    return $app['twig']->render('rate.twig', []
    );
})->assert('id', '\d+');

function db() {
    $db = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
    return $db;
}

//start server
$app->run();

