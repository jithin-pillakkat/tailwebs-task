<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */



$routes->group('', ['filter' => 'auth:Guest'], static function ($routes) {
    $routes->get('/', 'TeacherController::login', ['as' => 'login']);
    $routes->post('login', 'TeacherController::loginHandler', ['as' => 'login.handler']);
});

$routes->group('', ['filter' => 'auth:Teacher'], static function ($routes) {
    $routes->get('logout', 'TeacherController::logout', ['as' => 'logout']);
    
    $routes->get('students', 'StudentController::index', ['as' => 'student.index']);
    $routes->post('student-save', 'StudentController::save', ['as' => 'student.save']);
    $routes->post('normal-action', 'StudentController::action', ['as' => 'student.action']);
});
