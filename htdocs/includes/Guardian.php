<?php

class Guardian
{

	private $user;
	private $allPermissions = array();
	private $app;
	
	function __construct($app)
	{
		$this->app = $app;

		// collect all permissions into $this->allPermissions
		foreach ($app->conf as $ext => $extconf) {
			if (property_exists($extconf, 'permissions')) {
				$this->allPermissions = array_merge(
					$this->allPermissions, 
					$extconf->permissions
				);
			}
		}
	}

	public function requireLogin()
	{
		if (isset($_SESSION['user']['id'])) {
			$this->user = $_SESSION['user'];
		} else {
			echo $this->app->twig->render(
				'core/twig/login.twig',
				$this->app->twigvars);
			exit;
		}
	}

	public function login($username,$password)
	{
		$stmt = $this->app->db->prepare(
			'SELECT id, is_admin
			FROM user
			WHERE user.username = :username
			AND user.password = :password');

		$stmt->bindValue(':username',$username);
		$stmt->bindValue(':password',$password);
		$stmt->execute();

		if ($user = $stmt->fetch()) {
			$_SESSION['user'] = $user;
			$this->user = $user;
			return true;
		}
		return false;
	}

	public function logout()
	{
		$_SESSION = array();
		session_destroy();
		session_start();
	}

	public function requirePerm($name)
	{
		if ($this->user['is_admin']) {
			return true;
		}
	}
}
