<?php
header("HTTP/1.0 404 Not Found");
$ask->page_url_ = $ask->config->site_url.'/404';
$ask->page = '404';
$ask->title = '404 | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword = $ask->config->keyword;
$ask->content = LoadPage('404/content');