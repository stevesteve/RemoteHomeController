<?php

$router->respond('/ajax/relaisctrl/powerbar', function ($req,$res,$service,$app) {
	if (!$app->guardian->hasPerm('relais_read')) {
		return;
	}

	$stmt = $app->db->prepare(
		'SELECT * FROM switch
		WHERE switch.enabled = 1
		ORDER BY `order` ASC;');
	$stmt->execute();
	return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
});
$router->respond('GET','/ajax/relaisctrl/state', function ($req,$res,$service,$app) {
	$app->guardian->requirePerm('relais_read');
	$rb = $app->relais_board;
	$switchStates = $rb->getSwitchStates();
	return json_encode($switchStates);
});
$router->respond('POST','/ajax/relaisctrl/toggle/[i:id]',
	function ($req,$res,$service,$app) {

	$app->guardian->requirePerm('relais_set');

	$rb = $app->relais_board;
	$success = $rb->toggleById($req->id);
	if (!$success) {
		$res->code(500);
	}
	return json_encode($success);
});

