<?php

use App\Controllers\_Auth_Controller;
use App\Controllers\_Blog_Controller;
use App\Controllers\_CS_Controller;
use App\Controllers\_Install_Controller;
use App\Controllers\_Lead_Controller;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['adminRender'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['adminTemplates']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['db'] = function($c){
	$capsule = new \Illuminate\Database\Capsule\Manager;
	$capsule->addConnection($c['settings']['db']);
	$capsule->setAsGlobal();
	$capsule->bootEloquent();
	return $capsule;
};

$container['theme_directory'] = __DIR__ . '/../themes/';
$container['upload_directory'] = __DIR__ . '/../uploads/';
$container['sql_directory'] = __DIR__ . '/../sql/';

$container['App\Controllers\_Auth_Controller'] = function ($c) {
	return new _Auth_Controller($c->get('logger'), $c->get('db')->table('user'));
};

$container['App\Controllers\_Install_Controller'] = function ($c) {
	return new _Install_Controller($c->get('adminRender'), $c->get('sql_directory'));
};

$container['App\Controllers\_Blog_Controller'] = function ($c) {
	return new _Blog_Controller($c->get('logger'), $c->get('db')->table('posts'), $c->get('renderer'), $c->get('adminRender'), $c->get('upload_directory'));
};

$container['App\Controllers\_Lead_Controller'] = function ($c) {
	return new _Lead_Controller($c->get('logger'), $c->get('db')->table('leads'), $c->get('renderer'), $c->get('adminRender'));
};

$container['App\Controllers\_CS_Controller'] = function ($c) {
	return new _CS_Controller($c->get('logger'), $c->get('db')->table('case_studies'), $c->get('renderer'), $c->get('adminRender'), $c->get('upload_directory'));
};

