<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app = new Silex\Application();


$config = require_once('../config.php');

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views/',
));

$db = new PDO('mysql:'
    . 'host=' . $config['db']['hostname'] . ';'
    . 'dbname=' . $config['db']['database'],
    $config['db']['username'],
    $config['db']['password']);
// ... definitions

$app['debug'] = true;

/*------- ROUTES ---------- */
$app->get('/', function(Silex\Application $app) use ($db ) {
    $restrooms = $db->query('SELECT Restrooms.id, Restrooms.name, (SELECT count(Ratings.id) FROM Ratings WHERE Ratings.rating = 1 AND Ratings.r_id=Restrooms.id) as positive, (SELECT count(Ratings.id) FROM Ratings INNER JOIN Restrooms ON Restrooms.id=Ratings.r_id) as total FROM Restrooms;');

    return $app['twig']->render('list.twig', ['restrooms' => $restrooms]);
});

//route for toilet
$app->get('/{id}', function (Silex\Application $app, $id) use($db) {
    $restroom = $db->query('SELECT Restrooms.id, Restrooms.name, (SELECT count(Ratings.id) FROM Ratings WHERE Ratings.rating = 1 AND Ratings.r_id='.$id.') as positive, (SELECT count(Ratings.id) FROM Ratings INNER JOIN Restrooms ON Restrooms.id='.$id.') as total FROM Restrooms WHERE Restrooms.id='.$id);

    return $app['twig']->render('single.twig', array(
        'restroom' => $restroom->fetch(0),
    ));
})->assert('id', '\d+');

$app->get('/rate/{id}', function (Request $req, Silex\Application $app, $id) use ($db) {

    $restroom = $db->query('SELECT * FROM Restrooms WHERE id='.$id);

    return $app['twig']->render(
        'rate.twig',
        [
            'name' => $restroom->name,
            'id'    => $id,
        ]
    );
})->assert('id', '\d+');

$app->post('/rate/{id}', function (Request $req, Silex\Application $app, $id) use ($db) {

    $bathroom = $id;

    $db->query('INSERT INTO
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

//start server
$app->run();

