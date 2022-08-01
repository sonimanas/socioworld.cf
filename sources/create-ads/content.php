<?php
if (IS_LOGGED == false || $ask->config->user_ads !== 'on') {
    header("Location: " . UrlLink('login'));
    exit();
}

$ask->page_url_   = $ask->config->site_url.'/ads/create';
$ask->title       = __('advertising') . ' | ' . $ask->config->title;
$ask->page        = "ads";
$ask->ap          = "create";
$ask->ads         = GetMyAds();
$ask->description = $ask->config->description;
$ask->keyword     = @$ask->config->keyword;
$ask->audience     = $ask->countries_name;
$ask->content     = LoadPage('ads/create');