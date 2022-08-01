<?php
if (IS_LOGGED == false || $ask->config->user_ads !== 'on') {
    header("Location: " . UrlLink('login'));
    exit();
}

if ($ask->user->wallet == 0 || $ask->user->wallet == '0.00') {
    $user_id = $ask->user->id;
    $query = mysqli_query($sqlConnect, "UPDATE " . T_USER_ADS . " SET status = 0 WHERE user_id = '$user_id'");
}
$ask->page_url_   = $ask->config->site_url.'/ads';
$ask->title       = __('advertising') . ' | ' . $ask->config->title;
$ask->page        = "ads";
$ask->ap          = "ads";
$ask->ads         = GetMyAds();
$ask->description = $ask->config->description;
$ask->keyword     = @$ask->config->keyword;
$ask->content     = LoadPage('ads/content');