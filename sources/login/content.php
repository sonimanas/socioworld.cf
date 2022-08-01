<?php
if (IS_LOGGED == true) {
    header("Location: " . UrlLink(''));
    exit();
}
$ask->page        = 'login';
$ask->title       = __('login') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->page_url_   = $ask->config->site_url . '/login';
$ask->content     = LoadPage('auth/login/content', array());