<?php
if (IS_LOGGED == false || $ask->config->user_ads !== 'on') {
    header("Location: " . UrlLink('login'));
    exit();
}
if (isset($_GET['id'])) {
    $ad_data = GetUserAdData($_GET['id']);
    if (!empty($ad_data) && IsAdsOwner($ad_data['id'])) {

        $ask->page_url_ = $ask->config->site_url . '/ads/chart/'.$ad_data['id'];
        $ask->title = __('advertising') . ' | ' . $ask->config->title;
        $ask->page = "ads";
        $ask->ap = "ads";
        $ask->ads = $ad_data;
        $ask->audience     = $ask->countries_name;
        $ask->description = $ask->config->description;
        $ask->keyword = @$ask->config->keyword;
        $ask->clicks	   = array();
        $ask->views	   = array();
        $ask->ad_data	   = $ad_data;

        $sqlclicks = "SELECT DATE(dt) DateOnly, SUM(clicks) AS ADClicks , SUM(spend) AS Spend FROM `" . T_USERADS_DATA ."` WHERE user_id = ". Secure($ad_data['user_id'])." AND ad_id = " . Secure($_GET['id']) . " AND clicks > 0 GROUP BY DateOnly ORDER BY dt DESC LIMIT 30";
        $queryclicks = mysqli_query($sqlConnect, $sqlclicks);
        while ($fetched_data = mysqli_fetch_assoc($queryclicks)) {
            $ask->clicks[] = $fetched_data;
        }

        $sqlviews = "SELECT DATE(dt) DateOnly, SUM(views) AS ADviews , SUM(spend) AS Spend FROM `" . T_USERADS_DATA ."` WHERE user_id = ". Secure($ad_data['user_id'])." AND ad_id = " . Secure($_GET['id']) . " AND views > 0 GROUP BY DateOnly ORDER BY dt DESC LIMIT 30";
        $queryviews = mysqli_query($sqlConnect, $sqlviews);
        while ($fetched_data = mysqli_fetch_assoc($queryviews)) {
            $ask->views[] = $fetched_data;
        }

        $sqlSpends = "SELECT DATE(dt) DateOnly, SUM(spend) AS Spend FROM `" . T_USERADS_DATA ."` WHERE user_id = ". Secure($ad_data['user_id'])." AND ad_id = " . Secure($_GET['id']) . " GROUP BY DateOnly ORDER BY dt ASC LIMIT 30";
        $querySpends = mysqli_query($sqlConnect, $sqlSpends);
        while ($fetched_data = mysqli_fetch_assoc($querySpends)) {
            $ask->spends[] = $fetched_data;
        }

        $ask->content = LoadPage('ads/chart');

    }
}