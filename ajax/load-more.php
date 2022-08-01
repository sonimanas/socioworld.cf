<?php
if (empty($_GET['first']) || empty($_POST['last_id'])) {
    $data = array('status' => 404);
}

else {
    $type = Secure($_GET['first']);
    $id = Secure($_POST['last_id']);
    $final = '';
    $user_id = 0;
    if (!empty($_POST['user_id'])) {
        $user_id = Secure($_POST['user_id']);
    }
    if ($type == 'questions') {
        $final = '';
        $sql = '';
        $loadmore_mode = Secure($_POST['loadmore_mode']);
        $questions_data = array();
        if($loadmore_mode == 'timeline'){
            $ids = $_POST['ids'];
            $query_otin = '';
            if(!empty($ids) && is_array($ids)){
                $query_otin .= "`id` NOT IN (" . implode(',',$ids) .") ";
            }
            $questions_data = $db->where('user_id', $user_id)
                                 ->where('is_anonymously','0')
                                 ->where('replay_question_id','0')
                                 ->where('public','1')
                                 ->where('id', $id, '<')
                                 ->where($query_otin)
                                 ->orderBy('promoted', 'DESC')
                                 ->orderby('id','DESC')->get(T_QUESTIONS, 20);
        }else if($loadmore_mode == 'home'){
            $ids = $_POST['ids'];
            $questions_data = GetQuestions(['page' => 'home', 'after_post_id' => $id, 'ids' => $ids]);
        }else if($loadmore_mode == 'trending'){
            $ids = $_POST['ids'];
            $questions_data = GetQuestions(['page' => 'trending', 'before_post_id' => $id, 'ids' => $ids]);


        }else if($loadmore_mode == 'hashtag'){
            if(isset($_POST['hashtag']) && !empty($_POST['hashtag'])){
                $ids = $_POST['ids'];
                $questions_data = GetHashtagPosts(Secure($_POST['hashtag']),$id, 5, null,$ids);
            }
        }else if($loadmore_mode == 'search'){
            if(isset($_POST['hashtag']) && !empty($_POST['hashtag'])){
                $ids = $_POST['ids'];
                $ask->mode = "all";
                $questions_data = GetSearchPosts(Secure($_POST['hashtag']),$id, 5, null,$ids);
            }
        }
        foreach ($questions_data as $key => $question){
            if($question->user_id == $user->id){
                $question->isowner = true;
            }else{
                $question->isowner = false;
            }
            $ask->mode = 'all';
            $final .= LoadPage('timeline/partials/question', ['QUESTION_DATA' => QuestionData($question)]);
        }
        $data = array('status' => 200, 'html' => $final);
    }
    if ($type == 'trending') {
        $questions_data = GetHashtagPosts(Secure($_GET['hashtag']));
    }
    if ($type == 'followers') {
        $followers_get = $db->where('user_id', $user_id)->where('follower_id', $id, '<')->orderby('follower_id', 'DESC')->get(T_FOLLOWERS, 20);
        if (!empty($followers_get)) {
            $len = count($followers_get);
            foreach ($followers_get as $key => $follower) {
                $ask->last_follower = false;
                if ($key == $len - 1) {
                    $ask->last_follower = true;
                }
            }
            $final = GetFollowersHtml($followers_get);
            $data = array('status' => 200, 'html' => $final);
        }else{
            $data = array('status' => 404, 'html' => $final);
        }
    }
    if ($type == 'following') {
        $following_get = $db->where('follower_id', $user_id)->where('user_id', $id, '<')->orderby('user_id', 'DESC')->get(T_FOLLOWERS, 20);
        if (!empty($following_get)) {
            $len = count($following_get);
            foreach ($following_get as $key => $following) {
                $ask->last_following = false;
                if ($key == $len - 1) {
                    $ask->last_following = true;
                }
            }
            $final = GetFollowingsHtml($following_get);
            $data = array('status' => 200, 'html' => $final);
        }else{
            $data = array('status' => 404, 'html' => $final);
        }
    }
    if ($type == 'user_suggestions') {
        $html = '';
        foreach (UserSuggestions(4) as $key => $user) {
            $html .= LoadPage('timeline/partials/people_suggestion_list',array('USER_DATA'=>$user));
        }
        $data = array(
            'status' => 200,
            'html' => $html
        );
    }
    if ($type == 'search_users') {

        $keyword_search = '';
        if(isset($_POST['hashtag']) && !empty($_POST['hashtag'])){
            $keyword_search = Secure($_POST['hashtag']);

            $sql = "SELECT `id` FROM ".T_USERS." WHERE `id` NOT IN (" . implode(',', Secure($_POST['ids'])) . ") AND ( `username` LIKE '%".$keyword_search."%' OR `first_name` LIKE '%".$keyword_search."%' OR `last_name` LIKE '%".$keyword_search."%' OR `about` LIKE '%".$keyword_search."%' ) ORDER BY `id` DESC LIMIT 12";

//            //$db->Where(T_USERS.'.id < '.$id);
//            //if( isset($_POST['ids']) ){
//                $db->Where(T_USERS.'.id NOT IN (' . implode(',', $_POST['ids']) . ')');
//            //}
//
//            $db->orWhere('username','%'.$keyword_search.'%', 'like')
//                ->orWhere('active', '1')
//                ->orWhere('first_name','%'.$keyword_search.'%', 'like')
//                ->orWhere('last_name','%'.$keyword_search.'%', 'like')
//                ->orWhere('about','%'.$keyword_search.'%', 'like')
//                ->orderBy('id', 'DESC');
            $users = $db->rawQuery($sql);// $db->get(T_USERS, 3,array('id'));
            $users_html = '';
            foreach ($users as $key => $user){
                $user_data = UserData($user->id);
                $users_html .= LoadPage('search/user', ['SEARCHUSER_DATA' => [
                    'ID' => $user_data->id,
                    'USERNAME' => $user_data->username,
                    'NAME' => $user_data->name,
                    'AVATAR' => $user_data->avatar,
                    'FOLLOWER_BUTTON' => GetFollowButton($user_data->id)
                ]]);
            }
            $data = array('status' => 200, 'html' => $users_html, 'sql' => $sql);
        }

    }
    if ($type == 'nearby') {
        $ids = $_POST['ids'];
        $questions_data = GetQuestions(['page' => 'nearby', 'before_post_id' => $id, 'ids' => $ids]);
        foreach ($questions_data as $key => $question){
            if($question->user_id == $user->id){
                $question->isowner = true;
            }else{
                $question->isowner = false;
            }
            $ask->mode = 'all';
            $final .= LoadPage('timeline/partials/question', ['QUESTION_DATA' => QuestionData($question)]);
        }
        $data = array('status' => 200, 'html' => $final);
    }
}