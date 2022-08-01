<?php
if (IS_LOGGED == false) {
    header("Location: " . UrlLink('login'));
    exit();
}



$ask->mode        = 'all';
$ask->page        = 'hashtag';
$ask->title       = $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = @$ask->config->keyword;
$ask->page_url_   = $ask->config->site_url;

$questions_data = array();
if (!empty($_GET['hashtag'])) {
    $ask->title       = Secure($_GET['hashtag']) . ' | ' . $ask->config->title;
    $ask->page_url_ = $ask->config->site_url.'/hashtag/'.Secure($_GET['hashtag']);
    $questions_data = GetHashtagPosts(Secure($_GET['hashtag']));
}

$ask->content     = LoadPage('hashtag/content', [
    'USER_DATA'         => $user,
    'PEOPLE_SUGGESTION' => LoadPage('timeline/partials/people_suggestion', []),
    'POST_AREA'         => LoadPage('timeline/partials/post_area', [
        'USER_DATA' => $user,
        'QUESTIONS_DATA' => $questions_data,
    ]),
    'TRENDING_HASHTAG'  => LoadPage('timeline/partials/trending', []),
    'STATS'             => LoadPage('timeline/partials/stats', []),
    'SIDEBAR_FOOTER'    => LoadPage('timeline/partials/sidebar_footer', [
        'ACTIVE_LANG'       => $ask->language,
        'ACTIVE_LANGNAME'   => ucfirst($ask->language),
        'LANGS_RIGHT'       => GetLang(true),
        'LANGS_LEFT'        => GetLang(false)
    ]),
]);
