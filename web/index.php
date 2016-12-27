<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once('../config.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$database = new PDO('mysql:'
    . 'host=' . $config['db']['hostname'] . ';'
    . 'dbname=' . $config['db']['database'],
    $config['db']['username'],
    $config['db']['password']);

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views/',
));

// ... definitions

$app['debug'] = true;

/*------- ROUTES ---------- */
$app->get('/', function(Silex\Application $app) {
    $db = db();

    $restrooms = $db->query('SELECT * FROM Restrooms INNER JOIN Ratings ON Restrooms.id=Ratings.r_id;');

    return $app['twig']->render('list.twig', ['restrooms' => $restrooms]);
});

//route for rating toilet
$app->get('/{id}', function (Silex\Application $app, $id) {

    $bathroom = $id;

    return $app['twig']->render('rate.twig', array(
        'bathroom' => $bathroom,
    ));
})->assert('id', '\d+');

$app->post('/rate/{id}', function (Request $req, Silex\Application $app, $id) use ($database) {

    $bathroom = $id;

    $database->query('INSERT INTO
      Ratings (r_id, timestamp, rating)
      VALUES (' . $id . ', ' . time() . ', ' . (int)$req->get('satisfied') . ')');

    return $app['twig']->render(
        'thanks.twig',
        [
            'id' => $id,
            'satisfied' => $req->satisfied,
        ]
    );
})->assert('id', '\d+');

function db() {
    $db = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
    return $db;
}

//start server
$app->run();

