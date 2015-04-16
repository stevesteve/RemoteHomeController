<?php


// autoload composer files (see https://getcomposer.org/)
require_once 'vendor/autoload.php';


// start session
session_start();


// loading settings from config.json
$conf = require 'includes/loadconfiguration.php';


if ($conf->core->debug) {
	// pretty error handling
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
}


// initiate the router
$router = new \Klein\Klein();


// setup, executed on every request
$router->respond(function ($req,$res,$service,$app) {

	// loading and initiating the database
	$app->register('db', function () use($app) {
		$db = require 'includes/database.php';
		return $db;
	});

	// initiating the permission system
	$app->register('guardian', function () use($app) {
		require 'includes/Guardian.php';
		return new Guardian($app);
	});

	// initiating the templating engine
	$app->register('twig', function () use($app) {
		$twig_loader = new Twig_Loader_Filesystem('ext');
		return new Twig_Environment($twig_loader);
	});

	$app->conf = require 'includes/loadconfiguration.php';

	// variables to be injected into twig templates
	$app->twigvars = array(
		"requestUri" => $_SERVER['REQUEST_URI'],
		"errors" => array()
		);

});

$router->respond('/login', function ($req,$res,$service,$app) {
	if ($app->guardian->login($req->username,$req->password)) {
		$res->redirect($app->conf->core->homepage);
	} else {
		$app->twigvars['errors'][] = 'login_failed';
	}
});

$router->respond(function ($req,$res,$service,$app) {
	$app->guardian->requireLogin();
});


// loading extensions from ext/name/name.php
$extensions = require 'includes/loadextensions.php';


$router->dispatch();
