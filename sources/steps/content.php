<?php
if (IS_LOGGED == false || empty($_SESSION['steps_type']) || !isset($_SESSION['steps_type'])) {
    header("Location: " . UrlLink('login'));
    exit();
}

$user_id          = $user->id;
$ask->is_admin    = IsAdmin();
$ask->user_data   = UserData($user_id);


$countries = '';
foreach ($countries_name as $key => $value) {
    $countries .= '<option value="' . $key . '">' . $value . '</option>';
}

$ask->step_type   = Secure($_SESSION['steps_type']);
$ask->page        = 'steps';
$ask->title       = __('profile') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->page_url_   = $ask->config->site_url;
$ask->content     = LoadPage('steps/content', array(
    'USER_DATA' => $ask->user_data,
    'COUNTRIES_LAYOUT' => $countries
));