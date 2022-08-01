<?php
$ask->isowner = false;
if (IS_LOGGED == true) {
    $ask->isowner = true;
}
$ask->mode        = 'all';
$ask->page        = 'home';
$ask->title       = $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = @$ask->config->keyword;
$pro_users        = array();
$ask->page_url_   = $ask->config->site_url;

if (IS_LOGGED == true) {
    $questions_data = GetQuestions(['page' => 'home']);
    $trend_search = $db->orderBy('hits', 'DESC')->get(T_KEYWORD_SEARCH, 10, array('id','keyword'));
    /* Get active Announcements */
    $announcement_html = '';
    if (IS_LOGGED === true) {
        $announcement          = get_announcments();
        if(!empty($announcement)) {
            $announcement_html =  LoadPage("announcements/content",array(
                'ANN_ID'       => $announcement->id,
                'ANN_TEXT'     => Decode($announcement->text),
            ));
        }
    }

    $promoted_question = GetQuestions(['page' => 'promoted']);

    /* Get active Announcements */
    $ask->content     = LoadPage('home/loggedin', [
        'ANNOUNCEMENT'     => $announcement_html,
        'USER_DATA'         => $user,
        'PEOPLE_SUGGESTION' => LoadPage('timeline/partials/people_suggestion', []),
        'USER_INFO_CARD'    => LoadPage('timeline/partials/userinfo_card', [
            'QUESTIONS' => number_format($db->where('user_id', $user->id)->getValue(T_QUESTIONS, "count(*)")),
            'FOLLOWERS' => number_format($db->where('user_id', $user->id)->getValue(T_FOLLOWERS, "count(*)")),
        ]),
        'PUBLISHER_BOX'     => LoadPage('timeline/partials/publisher_box', [
            'USER_DATA'         => $user,
        ]),
        'POST_AREA'         => LoadPage('timeline/partials/post_area', [
            'USER_DATA' => $user,
            'QUESTIONS_DATA' => $questions_data,
            'PROMOTED_DATA' => $promoted_question
        ]),
        'TRENDING_HASHTAG'  => LoadPage('timeline/partials/trending', []),
        'STATS'             => LoadPage('timeline/partials/stats', []),
        'ABOUT_ME'          => LoadPage('timeline/partials/about_me', []),
        'SIDEBAR_FOOTER'    => LoadPage('timeline/partials/sidebar_footer', [
            'ACTIVE_LANG'       => $ask->language,
            'ACTIVE_LANGNAME'   => ucfirst($ask->language),
            'LANGS_RIGHT'       => GetLang(true),
            'LANGS_LEFT'        => GetLang(false)
        ]),
        'SIDE_AD' => GetAd('side_bar'),
        'FOOTER_AD' => ($ask->page != 'login') ? GetAd('footer') : '',
        'HEADER_AD' => GetAd('header')
    ]);
}
else{
    $random_user_data = array();
    $random_user = $db->orderBy('rand()')->get(T_USERS,12,array('id'));
    if($random_user){
        foreach ($random_user as $user){
            $random_user_data[] = UserData($user->id);
        }
    }
    $ask->content     = LoadPage('home/content', [
        'RANDOM_USERS_DATA' => $random_user_data
    ]);
}
