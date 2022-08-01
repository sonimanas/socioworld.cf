<?php
if (IS_LOGGED == false) {
    header("Location: " . UrlLink('login'));
    exit();
}
$user_id                = $user->id;
$ask->is_admin          = IsAdmin();
$ask->is_settings_admin = false;
if (isset($_GET['user']) && !empty($_GET['user']) && ($ask->is_admin === true)) {
    if (empty($db->where('username', Secure($_GET['user']))->getValue(T_USERS, 'count(*)'))) {
        header("Location: " . UrlLink(''));
        exit();
    }
    $user_id               = $db->where('username', Secure($_GET['user']))->getValue(T_USERS, 'id');
    $ask->is_settings_admin = true;
}

$ask->settings     = UserData($user_id);
$ask->isowner = false;

if (IS_LOGGED == true) {
    if ($ask->settings->id == $user->id) {
        $ask->isowner = true;
    }
}

$ask->setting_page = 'general';
$pages_array = [
    'general',
    'profile',
    'notifications',
    'account',
    'blocked',
    'password',
    'delete',
    'verification',
    'two-factor'
];
if ($ask->settings->id == $user->id) {
    $pages_array = [
        'general',
        'profile',
        'notifications',
        'account',
        'blocked',
        'password',
        'delete',
        'verification',
        'two-factor'
    ];
}

$ask->page_url_   = $ask->config->site_url.'/settings';

if (!empty($_GET['page'])) {
    if (in_array($_GET['page'], $pages_array)) {
        $ask->setting_page = $_GET['page'];
        $ask->page_url_ = $ask->config->site_url.'/settings/'.$ask->setting_page.'/'.$ask->settings->username;
    }
}

if( $ask->setting_page == 'delete' && $ask->config->delete_account == 'off' ){
    header("Location: " . UrlLink('settings/general/'.$ask->user->username));
    exit();
}

$countries = '';
foreach ($countries_name as $key => $value) {
    $selected = ($key == $ask->settings->country_id) ? 'selected' : '';
    $countries .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
}


$ask->page        = 'settings';
$ask->title       = __('settings') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->content     = LoadPage('settings/content', [
    'USER_DATA' => $ask->settings,
    'TRENDING_HASHTAG'  => LoadPage('timeline/partials/trending', []),
    'SETTINGSPAGE' => LoadPage("settings/pages/$ask->setting_page", [
        'USER_DATA' => $ask->settings,
        'COUNTRIES_LAYOUT' => $countries,
         'ADMIN_LAYOUT' => LoadPage('settings/pages/admin', array(
        'USER_DATA' => $ask->settings
        ))
    ]),
    'PEOPLE_SUGGESTION' => LoadPage('timeline/partials/people_suggestion', []),
    'SIDEBAR_FOOTER'    => LoadPage('timeline/partials/sidebar_footer', [
        'ACTIVE_LANG'       => $ask->language,
        'ACTIVE_LANGNAME'   => ucfirst($ask->language),
        'LANGS_RIGHT'       => GetLang(true),
        'LANGS_LEFT'        => GetLang(false),
       
    ])
]);