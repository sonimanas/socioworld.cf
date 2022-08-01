<?php
if (IS_LOGGED == false) {
    header("Location: " . UrlLink('login'));
    exit();
}

$ask->mode = "all";
$search_tag = '';
$ask->page        = 'search';
$ask->title       = __('search') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->page_url_   = $ask->config->site_url . '/search';

if( isset($_GET['id']) ){
    $search_tag = Secure($_GET['id']);
    $ask->page_url_   = $ask->config->site_url . '/search/'.$search_tag;
}

$recent_search = $db->orderBy('time', 'DESC')->get(T_RECENT_SEARCH, 10, array('id','keyword'));
$trend_search = $db->orderBy('hits', 'DESC')->get(T_KEYWORD_SEARCH, 10, array('id','keyword'));

$ask->content     = LoadPage('search/content', [
    'SEARCH_TAG' => $search_tag,
    'RECENT_SEARCH' => $recent_search,
    'TREND_SEARCH' => $trend_search,
    'SIDEBAR_FOOTER'    => LoadPage('timeline/partials/sidebar_footer', [
        'ACTIVE_LANG'       => $ask->language,
        'ACTIVE_LANGNAME'   => ucfirst($ask->language),
        'LANGS_RIGHT'       => GetLang(true),
        'LANGS_LEFT'        => GetLang(false)
    ]),
]);