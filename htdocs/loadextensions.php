<?php

// This file loads all extensions from ext/extensionname/extensionname.php

$extensions = array();

$extIterator = opendir(__DIR__ . '/ext');
while (false !== ($ext = readdir($extIterator))) {
	if (filetype(__DIR__ . '/ext/' . $ext) !== 'dir'
		||$ext === '.'||$ext === '..') {
		// skip entry if it's not a folder
		continue;
	}
	if (!file_exists(__DIR__ . '/ext/' . $ext . '/' . $ext . '.php')) {
		trigger_error('ext/' . $ext . ' has no ' . $ext . '.php');
	}
	include __DIR__ . '/ext/' . $ext . '/' . $ext . '.php';
	array_push($extensions,$ext);
}

return $extensions;
