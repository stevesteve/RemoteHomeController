<?php

$router->respond('/favorites', function ($req,$res,$service,$app) {
	return $app->twig->render(
		'favorites/favorites.twig',
		$app->twigvars
	);
});
