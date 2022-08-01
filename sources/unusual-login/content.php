<?php 
 if (empty($_SESSION['code_id'])) {
	header("Location: " . UrlLink(''));
	exit();
}


$ask->page        = 'unusual-login';
$ask->title       = __('unusual-login') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->content     = LoadPage('home/unusual-login', array());