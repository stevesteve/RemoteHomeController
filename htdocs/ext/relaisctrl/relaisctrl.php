<?php

$router->respond(function ($req,$res,$service,$app) {
	$app->register('relais_board', function () use ($app) {
		require 'RelaisBoard.php';
		return new RelaisBoard($app->conf->relaisctrl->port);
	});
});

$router->respond('POST','/homecontrol', function ($req,$res,$service,$app) {
	$app->guardian->requirePerm('relais_label');

	$switches = array();
	foreach ($req->order as $order => $id) {
		$switches[$id] = array(
			'order'=>$order,
			'id'=>$id,
			'label'=>$req->labels[$id],
			'enabled'=>isset($req->enabled[$id])?1:0,
			);
	}
	$placehoders = array();
	$params = array();
	foreach ($switches as $switch) {
		$placehoders[] = '(?,?,?,?)';
		$params[] = $switch['id'];
		$params[] = $switch['label'];
		$params[] = $switch['order'];
		$params[] = $switch['enabled'];
	}
	$query = 'REPLACE INTO switch(`id`,`label`,`order`,`enabled`) VALUES';
	$query .= join(',',$placehoders) . ';';
	$stmt = $app->db->prepare($query);
	$stmt->execute($params);

	$app->twigvars['switches'] = $switches;
});
$router->respond('GET','/homecontrol', function ($req,$res,$service,$app) {
	$app->guardian->requirePerm('relais_read');

	$stmt = $app->db->prepare('SELECT * FROM switch ORDER BY `order` ASC;');
	$stmt->execute();
	$orderedSwitches = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$switches = array();
	foreach ($orderedSwitches as $switch) {
		$switches[$switch['id']] = $switch;
	}
	$app->twigvars['switches'] = $switches;

});

$router->respond('/homecontrol', function ($req,$res,$service,$app) {

	$app->guardian->requirePerm('relais_read');

	$rb = $app->relais_board;

	$switchstates = $rb->getSwitchStates();
	if (!$switchstates) {
		$app->twigvars['errors'][] = "Failed to connect to the relais board";
		return $app->twig->render(
			'relaisctrl/twig/settings.twig',
			$app->twigvars
		);
	};
	$switches = $app->twigvars['switches'];
	foreach ($switchstates as $id => $ison) {
		if (!array_key_exists($id,$switches)) {
			$switches[] = array(
				'id'=>$id,
				'label'=>'',
				'enabled'=>0,
				);
		}
		$switches[$id]['ison'] = $ison;
		$switches[$id]['board'] = floor($id/8)+1;
		$switches[$id]['physical_position'] = $id%8 + 1;
	}

	$app->twigvars['switches'] = $switches;

	return $app->twig->render(
		'relaisctrl/twig/settings.twig',
		$app->twigvars
	);
});

include_once __DIR__ . '/powerbar.php';
