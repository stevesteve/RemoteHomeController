<?php

$router->respond('POST','/settings', function ($req,$res,$service,$app) {

	$stmt = $app->db->prepare(
		'SELECT id, password, is_admin
		FROM user
		WHERE user.id = :userid');

	$stmt->bindValue(':userid',$_SESSION['user']['id']);
	$stmt->execute();

	$user = $stmt->fetch();
	if (!$user||!password_verify($req->oldpassword,$user['password'])) {
		$app->twigvars['errors'][] = 'Wrong password';
	} else if($req->newpassword!==$req->newpasswordconfirm) {
		$app->twigvars['errors'][] = 'New passwords don\'t match';
	} else if (strlen($req->newpassword)<1) {
		$app->twigvars['errors'][] = 'Please enter a password';
	} else {
		$password = password_hash($req->newpassword,PASSWORD_DEFAULT);
		$stmt = $app->db->prepare(
			'UPDATE user SET password=:password WHERE id = :userid;');
		$stmt->bindValue(':userid',$_SESSION['user']['id']);
		$stmt->bindValue(':password',$password);
		$stmt->execute();
		$app->twigvars['success'][] = 'Password changed';
	}

});
$router->respond('/settings', function ($req,$res,$service,$app) {
	return $app->twig->render(
		'core/twig/settings.twig',
		$app->twigvars
		);
});
