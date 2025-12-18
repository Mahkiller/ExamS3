<?php
 
use flight\Engine;
use flight\net\Router;
use app\middlewares\SecurityHeadersMiddleware;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

    // Route principale
    $router->get('/', function() use ($app) {
        $app->render('welcome');
    });

    // Routes UI
    $router->get('/ui/courses', function() use ($app) {
        $app->render('courses');
    });

    $router->get('/ui/course/@id', function($id) use ($app) {
        $app->render('modification', ['id' => (int)$id]);
    });

    // Routes API pour les courses - CORRIGÉES
    $router->get('/courses', function() use ($app) {
        $controller = new \app\controllers\CourseController();
        $controller->getAll();
    });
    
    $router->get('/courses/@id', function($id) use ($app) {
        $controller = new \app\controllers\CourseController();
        $controller->getOne($id);
    });
    
    $router->post('/courses/create', function() use ($app) {
        $controller = new \app\controllers\CourseController();
        $controller->create();
    });
    
    $router->post('/courses/update/@id', function($id) use ($app) {
        $controller = new \app\controllers\CourseController();
        $controller->update($id);
    });
    
    $router->post('/courses/validate/@id', function($id) use ($app) {
        $controller = new \app\controllers\CourseController();
        $controller->validate($id);
    });
    
    $router->delete('/courses/delete/@id', function($id) use ($app) {
        $controller = new \app\controllers\CourseController();
        $controller->delete($id);
    });

    // Route de test
    $router->get('/hello-world/@name', function($name) use ($app) {
        $app->json(['message' => "Hello $name"]);
    });

}, [ SecurityHeadersMiddleware::class ]);