<?php
if (IS_LOGGED == false || $ask->config->user_ads !== 'on') {
    header("Location: " . UrlLink('login'));
    exit();
}
if (isset($_GET['id'])) {
    $ad_data = GetUserAdData($_GET['id']);
    if (!empty($ad_data) && IsAdsOwner($ad_data['id'])) {

        $ask->page_url_   = $ask->config->site_url.'/ads/edit/'.$ad_data['id'];
        $ask->title       = __('edit_ads') . ' | ' . $ask->config->title;
        $ask->page        = "ads";
        $ask->ap          = "ads";
        $ask->ads         = GetMyAds();
        $ask->description = $ask->config->description;
        $ask->keyword     = @$ask->config->keyword;
        $ask->audience     = $ask->countries_name;
        $ask->content     = LoadPage('ads/edit',['AD_DATA'=>$ad_data]);
    }
}