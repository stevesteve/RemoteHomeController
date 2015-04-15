<?php


// autoload composer files (see https://getcomposer.org/)
require_once __DIR__ . '/vendor/autoload.php';


// loading settings from config.json
$conf = require __DIR__ . '/loadconfiguration.php';


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

	// initiating the templating engine
	$twig_loader = new Twig_Loader_Filesystem(__DIR__ . '/ext');
	$app->twig = new Twig_Environment($twig_loader);

	// variables to be injected into twig templates
	$app->twigvars = array();

	$app->conf = require __DIR__ . '/loadconfiguration.php';
});


// loading extensions from ext/name/name.php
$extensions = require 'loadextensions.php';


$router->dispatch();
