<?php
if (IS_LOGGED == false) {
    header("Location: " . UrlLink(''));
    exit();
}
$ask->page        = 'nearby-questions';
$ask->title       = __('near_by_questions') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->page_url_   = $ask->config->site_url . '/nearby-questions';


$questions_data = GetQuestions(['page' => 'nearby']);
$ask->mode        = 'all';

$ask->content     = LoadPage('nearby-questions/content', [
    'PEOPLE_SUGGESTION' => LoadPage('timeline/partials/people_suggestion', []),
    'POST_AREA'         => LoadPage('timeline/partials/post_area', [
        'USER_DATA' => $user,
        'QUESTIONS_DATA' => $questions_data,
    ]),
    'TRENDING_HASHTAG'  => LoadPage('timeline/partials/trending', []),
    'SIDEBAR_FOOTER'    => LoadPage('timeline/partials/sidebar_footer', [
        'ACTIVE_LANG'       => $ask->language,
        'ACTIVE_LANGNAME'   => ucfirst($ask->language),
        'LANGS_RIGHT'       => GetLang(true),
        'LANGS_LEFT'        => GetLang(false)
    ]),
]);