<?php
if (IS_LOGGED == true) {
    header("Location: " . UrlLink(''));
    exit();
}
$recaptcha   = '<script src="https://www.google.com/recaptcha/api.js" async defer></script><div class="g-recaptcha" data-sitekey="' . $ask->config->recaptcha_key . '"></div>';
if ($ask->config->recaptcha != 'on') {
    $recaptcha = '';
}
$ask->page        = 'login';
$ask->title       = __('reset_password') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->page_url_   = $ask->config->site_url . '/forget_password';
$ask->content     = LoadPage('auth/forgot_password/content', array(
'RECAPTCHA' => $recaptcha
));