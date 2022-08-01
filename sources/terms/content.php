<?php
if (empty($_GET['type']) || !isset($_GET['type'])) {
    header("Location: " . UrlLink(''));
    exit();
}
$pages = array('terms','privacy-policy','about-us');
if (!in_array($_GET['type'], $pages)) {
    header("Location: " . UrlLink(''));
    exit();
}
$ask->terms = GetTerms();

$ask->description  = $ask->config->description;
$ask->keyword   = $ask->config->keyword;
$ask->page        = 'terms';
$ask->title       = '';
$type = Secure($_GET['type']);

if ($type == 'terms') {
    $ask->title  = __('terms_of_use');
} else if ($type == 'about-us') {
    $ask->title  = __('about_us');
} else if ($type == 'privacy-policy') {
    $ask->title  = __('privacy_policy');
}

$page = 'terms/' . $type;
$ask->page_url_ = $ask->config->site_url.'/terms/'.$type;
$ask->title = $ask->config->name . ' | ' . $ask->title;
$ask->content  = LoadPage($page);