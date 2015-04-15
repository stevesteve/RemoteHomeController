<?php


$router->respond('/', function ($req,$res,$service,$app) {
	$res->redirect($app->conf->core->homepage);
});


$router->onHttpError(function ($code, $router) {
	$app = $router->app();
	switch ($code) {
		case '404':
			$router->response()->body(
				$app->twig->render('core/twig/404.twig',$app->twigvars)
				);
			break;
	}
});
