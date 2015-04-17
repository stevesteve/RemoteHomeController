<?php

$router->respond('/user/[i:urlid]', function ($req,$res,$service,$app) {
	
	$app->guardian->requirePerm('user_update');

	$stmt = $app->db->prepare(
		'SELECT id, username, firstname, lastname, is_admin
		FROM user
		WHERE user.id = :userid');
	$stmt->bindValue(':userid',$req->urlid);
	$stmt->execute();
	if ($user = $stmt->fetch()) {
		$app->twigvars['user'] = $user;
	} else {
		$app->twigvars['errors'][] = "User not found";
	}

	return $app->twig->render(
		'usermanager/twig/user.twig',
		$app->twigvars
		);
});

$router->respond('/user/?', function ($req,$res,$service,$app) {
	$app->guardian->requirePerm('user_create');

	return $app->twig->render(
		'usermanager/twig/user.twig',
		$app->twigvars
		);
});

$router->respond('/user/submit', function ($req,$res,$service,$app) {
	
	$app->guardian->requirePerm('user_update');

	$user = array(
		'id'=>$req->userid,
		'username'=>$req->username,
		'password'=>$req->password,
		'firstname'=>$req->firstname,
		'lastname'=>$req->lastname,
		'is_admin'=>$req->is_admin?1:0,
		);

	$v = new Valitron\Validator($user);
	$v->rule('required', ['username'])->message('');

	$v->rule('lengthBetween', [
			'username',
			'firstname',
			'lastname'
		], 1,255);

	if (!$user['id']||$req->setPassword) {
		$v->rule('required', ['password']);
	}
	if($v->validate()) {
		if (!$user['id']||$req->setPassword) {
			$user['password'] =
				password_hash($user['password'],PASSWORD_DEFAULT);
		}

		if ($user['id']) {
			// update a user
			$query_set = 
				'UPDATE user
					SET 
						user.username = :username,
						user.lastname = :lastname,
						user.firstname = :firstname,
						user.is_admin = :is_admin';
			$query_where = ' WHERE user.id = :userid';
			if ($req->setPassword) {
				$query_set .= ', user.password = :password';
			}

			$stmt = $app->db->prepare($query_set . $query_where);
			$stmt->bindValue(':userid',$user['id']);
			if ($req->setPassword) {
				$stmt->bindValue(':password',$user['password']);
			}
		} else {
			// create a new user
			$stmt = $app->db->prepare(
				'INSERT INTO user(username,password,lastname,firstname,is_admin)
				VALUES (:username,:password,:lastname,:firstname,:is_admin)');
			$stmt->bindValue(':password',$user['password']);
		}
		$stmt->bindValue(':username',$user['username']);
		$stmt->bindValue(':firstname',$user['firstname']);
		$stmt->bindValue(':lastname',$user['lastname']);
		$stmt->bindValue(':is_admin',$user['is_admin']);
		$success = $stmt->execute();

		

		if ($success) {
			if (!$user['id']) {
				$user['id'] = $app->db->lastInsertId();
			}
			$app->twigvars['success'][] = "User saved";
		} else {
			$app->twigvars['errors'][] = "User exists";
		}
	} else {
		$app->twigvars['formErrors'] = $v->errors();
	}
	$app->twigvars['user'] = $user;
	return $app->twig->render(
		'usermanager/twig/user.twig',
		$app->twigvars
		);
});

