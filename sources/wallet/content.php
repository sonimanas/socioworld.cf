<?php
if (IS_LOGGED == false || $ask->config->user_ads !== 'on') {
    header("Location: " . UrlLink('login'));
    exit();
}
if (isset($_SESSION['replenished_amount']) && $_SESSION['replenished_amount'] > 0){
    $wo['replenishment_notif']  = __('replenishment_notif') . ' ' . GetCurrency($ask->config->ads_currency) .  $_SESSION['replenished_amount'];
    unset($_SESSION['replenished_amount']);
}
$payment_page = LoadPage('third-party/payment');
$ask->page_url_   = $ask->config->site_url.'/wallet';
$ask->title       = __('wallet') . ' | ' . $ask->config->title;
$ask->page        = "ads";
$ask->ap          = "wallet";
$ask->description = $ask->config->description;
$ask->keyword     = @$ask->config->keyword;
$ask->content     = LoadPage('ads/wallet', ['PAYMENT_LINK' => $payment_page]);