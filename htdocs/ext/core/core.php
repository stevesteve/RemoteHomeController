<?php


$router->respond('/', function ($req,$res,$service,$app) {
	return $res->redirect($app->conf->core->homepage);
});

$router->respond('/logout', function ($req,$res,$service,$app) {
	$app->guardian->logout();
	return $res->redirect('/');
});

$router->onHttpError(function ($code, $router) {
	$app = $router->app();
	switch ($code) {
		case '404':
			$router->response()->body(
				$app->twig->render(
					'core/errors/404.twig',
					$app->twigvars));
			break;
		case '403':
			$router->response()->body(
				$app->twig->render(
					'core/errors/403.twig',
					$app->twigvars));
			break;
	}
});

require_once __DIR__ . '/settings.php';
