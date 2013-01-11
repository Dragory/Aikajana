<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/

Route::get('/', ['as' => 'index', 'uses' => 'front@index']);
Route::get('/chart/(:any)', ['as' => 'chart', 'uses' => 'front@chart']);

/**
 * Charts
 * -> The chart's groups
 *    -> The group's events
 */
Route::get('/admin',       ['as' => 'admin_login',  'uses' => 'admin@login']);
Route::post('/logout_post',       ['as' => 'admin_logout',  'uses' => 'admin@logout', 'before' => 'csrf']);
Route::post('/login_post', ['as' => 'admin_login_post', 'uses' => 'admin@login_post', 'before' => 'csrf']);

// Admin index or chart listing page
Route::get('/admin/charts', ['as' => 'admin_charts', 'uses' => 'admin@charts']);

// Charts
Route::get('/admin/chart/(:any)',            ['as' => 'admin_chart', 'uses' => 'admin@chart']);
Route::post('/admin/chart/(:any)/save_post', ['as' => 'admin_chart_save_post', 'uses' => 'admin@chart_save_post', 'before' => 'csrf']);

Route::get('/admin/chart_add',       ['as' => 'admin_chart_add', 'uses' => 'admin@chart_add']);
Route::post('/admin/chart_add_post', ['as' => 'admin_chart_add_post', 'uses' => 'admin@chart_add_post', 'before' => 'csrf']);

Route::post('/admin/chart/(:any)/delete', ['as' => 'admin_chart_delete', 'uses' => 'admin@chart_delete', 'before' => 'csrf']);

// Groups
Route::get('/admin/chart/(:any)/(:num)',            ['as' => 'admin_group', 'uses' => 'admin@group']);
Route::post('/admin/chart/(:any)/(:num)/save_post', ['as' => 'admin_group_save_post', 'uses' => 'admin@group_save_post', 'before' => 'csrf']);

Route::get('/admin/chart/(:any)/add',       ['as' => 'admin_group_add', 'uses' => 'admin@group_add']);
Route::post('/admin/chart/(:any)/add_post', ['as' => 'admin_group_add_post', 'uses' => 'admin@group_add_post', 'before' => 'csrf']);

Route::post('/admin/chart/(:any)/(:num)/delete', ['as' => 'admin_group_delete', 'uses' => 'admin@group_delete', 'before' => 'csrf']);

// Events
Route::get('/admin/chart/(:any)/(:num)/(:num)',            ['as' => 'admin_event', 'uses' => 'admin@event']);
Route::post('/admin/chart/(:any)/(:num)/(:num)/save_post', ['as' => 'admin_event_save_post', 'uses' => 'admin@event_save_post', 'before' => 'csrf']);

Route::get('/admin/chart/(:any)/(:num)/add',        ['as' => 'admin_event_add', 'uses' => 'admin@event_add']);
Route::post('/admin/chart/(:any)/(:num)/add_post',  ['as' => 'admin_event_add_post', 'uses' => 'admin@event_add_post', 'before' => 'csrf']);

Route::post('/admin/chart/(:any)/(:num)/(:num)/delete', ['as' => 'admin_event_delete', 'uses' => 'admin@event_delete', 'before' => 'csrf']);

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});