<?php


if (!function_exists('password_hash')) {
	require 'includes/password_hash.php';
}
return json_decode(file_get_contents('config.json'));
