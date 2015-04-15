<?php


$router->respond('/', function ($req,$res,$service,$app) {
	$res->redirect($app->conf->core->homepage);
});


$router->onHttpError(function ($code, $router) {
	switch ($code) {
		case '404':
			$router->response()->body(
				$router->app()->twig->render('core/twig/404.twig')
				);
			break;
	}
});
