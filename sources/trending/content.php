<?php
if (IS_LOGGED == false) {
    header("Location: " . UrlLink('login'));
    exit();
}
$ask->mode        = 'all';
$ask->page        = 'trending';
$ask->title       = __('trending') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$ask->page_url_   = $ask->config->site_url . '/trending';

$questions_data = GetQuestions(['page' => 'trending']);


foreach ($questions_data as $key => $question){
    if($question->user_id == $user->id){
        $question->isowner = true;
    }else{
        $question->isowner = false;
    }
}

$ask->content     = LoadPage('trending/content', [
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