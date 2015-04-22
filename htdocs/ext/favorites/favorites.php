<?php

$router->respond('POST', '/favorites', function ($req,$res,$service,$app) {
	$app->guardian->requireLogin();

	$stmt = $app->db->prepare('DELETE FROM category WHERE user = :userid');
	$stmt->bindValue(':userid',$_SESSION['user']['id']);
	$stmt->execute();

	$categories = $req->categories;
	$catDict = array();
	$stmt = $app->db->prepare(
		'INSERT INTO category(label,user)
		VALUES (:label,:userid)');
	$stmt->bindValue(':userid',$_SESSION['user']['id']);
	foreach ($categories as $key => $label) {
		$stmt->bindValue(':label',$label);
		$stmt->execute();
		$catDict[$key] = $app->db->lastInsertId();
	}

	$stmt = $app->db->prepare(
		'INSERT INTO favorite(url,label,image,category)
		VALUES (:url,:label,:image,:category)');
	foreach ($req->favorites['label'] as $index => $id) {
		$category = $catDict[$req->favorites['category'][$index]];
		$stmt->bindValue(':category',$category);
		$stmt->bindValue(':url',$req->favorites['url'][$index]);
		$stmt->bindValue(':image',$req->favorites['image'][$index]);
		$stmt->bindValue(':label',$req->favorites['label'][$index]);
		$stmt->execute();
	}
});

$router->respond('/favorites', function ($req,$res,$service,$app) {

	$app->guardian->requireLogin();

	$stmt = $app->db->prepare(
		'SELECT
			favorite.id,
			favorite.url,
			favorite.label as label,
			favorite.image,
			category.id as categoryid,
			category.label as category
		FROM category
		LEFT JOIN favorite on favorite.category = category.id
		WHERE category.user = :userid');

	$stmt->bindValue(':userid',$_SESSION['user']['id']);
	$stmt->execute();

	$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$categories = array();
	foreach ($favorites as $favorite) {
		if (!array_key_exists($favorite['categoryid'],$categories)) {
			$categories[$favorite['categoryid']] = array(
				'id'=>$favorite['categoryid'],
				'label'=>$favorite['category'],
				'favorites'=>array()
				);
		}
		$categories[$favorite['categoryid']]['favorites'][] = $favorite;
	}
	$app->twigvars['categories'] = $categories;

	return $app->twig->render(
		'favorites/favorites.twig',
		$app->twigvars
	);
});
