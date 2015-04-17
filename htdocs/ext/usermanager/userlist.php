<?php

$router->respond('/users', function ($req,$res,$service,$app) {
	$app->guardian->requirePerm('user_read');

	$app->twigvars['userlist'] = getUserList($app->db);

	return $app->twig->render(
		'usermanager/twig/users.twig',
		$app->twigvars);
});

$router->respond('/user/[:urlid]/delete', function ($req,$res,$service,$app) {
	$app->guardian->requirePerm('user_delete');

	$stmt = $app->db->prepare('DELETE FROM user WHERE user.id = :userid');
	$stmt->bindValue(':userid',$req->urlid);
	$stmt->execute();

	$app->twigvars['userlist'] = getUserList($app->db);
	$app->twigvars['success'][] = "User deleted";

	return $app->twig->render(
		'usermanager/twig/users.twig',
		$app->twigvars
		);
});

function getUserList($db) {
	$stmt = $db->prepare(
		'SELECT
			id,username,lastname,firstname,is_admin
		FROM user');
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
