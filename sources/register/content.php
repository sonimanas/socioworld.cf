<?php
if (IS_LOGGED == true) {
    header("Location: " . UrlLink(''));
    exit();
}

if ($ask->config->user_registration == 'off' && (empty($_GET['invite']) || (!IsAdminInvitationExists($_GET['invite'])) )) {
	header("Location: " . UrlLink(''));
	    exit();
}
 else {

if (!empty($_GET['invite'])){

$code = Secure($_GET['invite']);
$db->where('code',$code)->update(T_INVITATIONS,array('status' => 1));	
}


$ask->page          = 'login';
$ask->title         = __('register') . ' | ' . $ask->config->title;
$ask->description   = $ask->config->description;
$ask->keyword       = $ask->config->keyword;
$ask->page_url_     = $ask->config->site_url . '/register';
$recaptcha   = '<script src="https://www.google.com/recaptcha/api.js" async defer></script><div class="g-recaptcha" data-sitekey="' . $ask->config->recaptcha_key . '"></div>';
if ($ask->config->recaptcha != 'on') {
    $recaptcha = '';
}
if ($ask->config->recaptcha != 'on') {
    $recaptcha = '';
}
$ask->content     = LoadPage('auth/register/content', array(
    'RECAPTCHA' => $recaptcha
));

}
