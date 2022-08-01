<?php
$ask->page_url_   = $ask->config->site_url.'/contact-us';
$ask->title       = __('contact_us') . ' | ' . $ask->config->title;
$ask->page        = "contact_us";
$ask->description = $ask->config->description;
$ask->keyword     = @$ask->config->keyword;
$ask->content     = LoadPage('contact/content');