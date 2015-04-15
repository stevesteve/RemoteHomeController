<?php


// autoload composer files (see https://getcomposer.org/)
require_once 'vendor/autoload.php';


// loading settings from config.json
$conf = require 'includes/loadconfiguration.php';


// loading the permission system
require 'includes/Guardian.php';


if ($conf->debug) {
	// pretty error handling
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
}


// initiate the router
$router = new \Klein\Klein();


// setup, executed on every request
$router->respond(function ($req,$res,$service,$app) {

	$app->register('db', function () {
		$db = require 'includes/database.php';
	});

	// initiating the permission system
	$app->guardian = new Guardian($app);

	// initiating the templating engine
	$twig_loader = new Twig_Loader_Filesystem('ext');
	$app->twig = new Twig_Environment($twig_loader);

	// variables to be injected into twig templates
	$app->twigvars = array(
		"requestUri" => $_SERVER['REQUEST_URI']
		);

	$app->guardian->requireLogin();

	$app->conf = require 'includes/loadconfiguration.php';
});


// loading extensions from ext/name/name.php
$extensions = require 'includes/loadextensions.php';


$router->dispatch();
