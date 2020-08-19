<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * authentication routes
 */

$router->group(['prefix' => 'api/auth'], function () use ($router) {
    $router->post('register', ['uses' => 'AuthController@register']);
    $router->post('login', ['uses' => 'AuthController@login']);
    $router->post('logout', ['uses' => 'AuthController@logout']);
    $router->get('/user', ['uses' => 'UserController@showLoggedUser']);
});

/**
 * users routes
 */
$router->group(['prefix' => 'api/users'], function () use ($router) {
    $router->get('/', ['uses' => 'UserController@index']);
    $router->get('/{id}', ['uses' => 'UserController@show']);
    $router->post('/update', ['uses' => 'UserController@update']);
});

/**
 * projects routes
 */
$router->group(['prefix' => 'api/projects'], function () use ($router) {
    $router->get('/', ['uses' => 'ProjectController@index']);
    $router->post('/', ['uses' => 'ProjectController@create']);
    $router->get('/{id}', ['uses' => 'ProjectController@show']);
    $router->post('/update', ['uses' => 'ProjectController@update']);
    $router->post('/members', ['uses' => 'MemberController@create']);
    $router->post('/members/{id}', ['uses' => 'MemberController@delete']);
});


/**
 * backlogs routes
 */
$router->group(['prefix' => 'api/backlogs'], function () use ($router) {
    $router->get('/', ['uses' => 'BacklogController@index']);
    $router->post('/', ['uses' => 'BacklogController@create']);
    $router->get('/{id}', ['uses' => 'BacklogController@show']);
    $router->get('/project/{project}', ['uses' => 'BacklogController@showByProjectId']);
    $router->post('/update', ['uses' => 'BacklogController@update']);
});

/**
 * comments routes
 */
$router->group(['prefix' => 'api/comments'], function () use ($router) {
    $router->get('/{id}', ['uses' => 'CommentController@index']);
    $router->post('/{id}', ['uses' => 'CommentController@create']);
});


/**
 * assignees routes
 */
$router->group(['prefix' => 'api/assignees'], function () use ($router) {
    $router->post('/', ['uses' => 'AssignController@create']);
    $router->post('/{id}', ['uses' => 'AssignController@delete']);
});

/**
 * sprints routes
 */
$router->group(['prefix' => 'api/sprints'], function () use ($router) {
    $router->get('/', ['uses' => 'SprintController@index']);
    $router->post('/', ['uses' => 'SprintController@create']);
    $router->post('/{id}', ['uses' => 'SprintController@show']);
});



/**
 * utilities routes
 */
$router->group(['prefix' => 'api/utilities'], function () use ($router) {
    $router->get('/users/{id}', ['uses' => 'UtilityController@index']);
    $router->get('/members/{id}', ['uses' => 'UtilityController@membersById']);
    $router->get('/backlogs/{id}', ['uses' => 'UtilityController@backlogs']);
    $router->get('/project/members/{id}', ['uses' => 'UtilityController@assigneesIndex']);
    $router->get('/backlog/assignees/{id}', ['uses' => 'UtilityController@getAssignees']);
    $router->post('/backlog/update', ['uses' => 'UtilityController@backlogUpdate']);
});


/**
 * error reporting routes
 */
 $router->group(['prefix' => 'api/errors'], function () use ($router) {
    $router->post('/', ['uses' => 'IssueController@report']);
});

/**
 * search routes
 */
 $router->group(['prefix' => 'api/search'], function () use ($router) {
    $router->post('/project', ['uses' => 'SearchController@project']);
    $router->post('/user', ['uses' => 'SearchController@user']);
    $router->post('/backlog', ['uses' => 'SearchController@backlog']);
});

