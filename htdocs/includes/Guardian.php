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

	public function isLoggedIn()
	{
		return isset($_SESSION['user']['id']);
	}

	public function requireLogin()
	{
		if ($this->isLoggedIn()) {
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
			'SELECT id, password, is_admin
			FROM user
			WHERE user.username = :username');

		$stmt->bindValue(':username',$username);
		$stmt->execute();

		$user = $stmt->fetch();
		if ($user&&password_verify($password,$user['password'])) {
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

	public function hasPerm($permission)
	{
		if ($this->user['is_admin']) {
			return true;
		}
	}

	public function requirePerm($permission)
	{
		if (!$this->hasPerm($permission)) {
			http_response_code(403);
			echo $this->app->twig->render(
				'core/errors/403.twig',
				$this->app->twigvars);
			exit;
		}
	}
}
