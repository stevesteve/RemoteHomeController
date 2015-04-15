<?php

class Guardian
{

	private $permissions;
	private $app;
	
	function __construct($app)
	{
		$this->app = $app;
	}

	public function requireLogin()
	{
		if (!isset($_SESSION['userid'])) {
			echo $this->app->twig->render('core/twig/login.twig');
			exit;
		}
	}
}
