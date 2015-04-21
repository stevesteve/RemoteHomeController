<?php

$router->respond(function ($req,$res,$service,$app) {
	$app->register('relais_board', function () use ($app) {
		require 'RelaisBoard.php';
		return new RelaisBoard($app->conf->relaisctrl->port);
	});
});

$router->respond('GET','/ajax/relaisctrl/state', function ($req,$res,$service,$app) {
	$rb = $app->relais_board;
	$switchStates = $rb->getSwitchStates();
	return json_encode($switchStates);
});
$router->respond('POST','/ajax/relaisctrl/toggle/[i:id]',
	function ($req,$res,$service,$app) {

	$rb = $app->relais_board;
	$success = $rb->toggleById($req->id);
	if (!$success) {
		$res->code(500);
	}
	return json_encode($success);
});
