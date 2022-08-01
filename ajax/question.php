<?php
if (empty($_POST['user_id']) || !IS_LOGGED) {
    exit("Undefined Alien ಠ益ಠ");
}
$is_owner = false;
if ($_POST['user_id'] == $user->id || IsAdmin()) {
    $is_owner = true;
}

$user_id = Secure($_POST['user_id']);

if ($first == 'normal') {
    if (empty($_POST['question']) OR empty($_POST['profile_user_id']) OR strlen( trim($_POST['question']) ) > $ask->config->post_text_limit) {
        $errors[] = $error_icon . __('please_check_details');
        if(strlen( trim($_POST['question']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = $error_icon . str_replace('{{num}}',$ask->config->post_text_limit,__('character_limit_exceeded'));
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
        $errors = array();
        $errors[] = $error_icon . __('post_limit_exceeded');
    }
    else {
        $question_data = RenderQuestion(Secure($_POST['question']));
        $profile_user_id = Secure($_POST['profile_user_id']);
        $insert_data         = array(
            'user_id' => ($_POST['question_type'] == 'me_ask') ? $user_id : $profile_user_id,
            'shared_user_id' => 0,
            'shared_question_id' => 0,
            'ask_user_id' => ($_POST['question_type'] == 'me_ask') ? 0 : $user_id,
            'ask_question_id' => 0,
            'replay_user_id' => 0,
            'replay_question_id' => 0,
            'is_anonymously' => (isset($_POST['is_anonymously'])) ? 1 : 0,
            'question' => $question_data['question'],
            'photo' => null,
            'type' => 'question',
            'active' => 1,
            'public' => (isset($_POST['view_type'])) ? Secure($_POST['view_type']) : 0,
            'time' => time(),
            'lat' => $ask->user->lat,
            'lng' => $ask->user->lng,
            'postLinkTitle'   =>  (!empty($_POST['url_title'])) ? Secure($_POST['url_title']) : '',
            'postLink'        =>  (!empty($_POST['url_link'])) ? Secure($_POST['url_link']) : '',
            'postLinkImage'   =>  (!empty($_POST['url_image'])) ? Secure($_POST['url_image']) : '',
            'postLinkContent' =>  (!empty($_POST['url_content'])) ? Secure($_POST['url_content']) : '',
        );
	
        $create_question = $db->insert(T_QUESTIONS, $insert_data);
       

        if ($create_question) {
            $insert_data['id'] = $create_question;
            $insert_data['isowner'] = true;
            $qd = ToObject($insert_data);
            if (isset($question_data['mentions']) && is_array($question_data['mentions']) && !empty($question_data['mentions'])) {
                foreach ($question_data['mentions'] as $mention) {
                    $notif_data = array(
                        'notifier_id' => $user_id,
                        'recipient_id' => $mention,
                        'question_id' => $create_question,
                        'type' => 'mention_post',
                        'url' => ('@' . $ask->user->username),
                        'time' => time()
                    );
                    Notify($notif_data);
                }
            }
            if(isset($_POST['question_type']) && $_POST['question_type'] == 'user_ask'){
                $notif_data = array(
                    'notifier_id' => ($_POST['question_type'] == 'me_ask') ? $profile_user_id : $user_id,
                    'recipient_id' => ($_POST['question_type'] == 'me_ask') ? $user_id : $profile_user_id,
                    'question_id' => $create_question,
                    'type' => 'user_ask',
                    'url' => ('@' . $ask->user->username),
                    'time' => time()
                );
                Notify($notif_data);
            }
            $ask->mode = "all";
            $data = array(
                'status' => 200,
                'question_html' => LoadPage('timeline/partials/question', ['QUESTION_DATA' => QuestionData($qd)]),
                'message' => $success_icon . __('your_question_was_successfully_posted')
            );
        }

    }
}

if ($first == 'photo') {
    if (empty($_POST['question']) OR empty($_FILES['choice1_img']['tmp_name']) OR empty($_FILES['choice2_img']['tmp_name']) OR strlen( trim($_POST['question']) ) > $ask->config->post_text_limit) {
        $errors[] = $error_icon . __('please_check_details');
        if(strlen( trim($_POST['question']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = $error_icon . str_replace('{{num}}',$ask->config->post_text_limit,__('character_limit_exceeded'));
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
        $errors = array();
        $errors[] = $error_icon . __('post_limit_exceeded');
    }
    else {
        $question_data = RenderQuestion(Secure($_POST['question']));
        $photo_poll = array();
        if (!empty($_FILES['choice1_img']['tmp_name'])) {
            $file_info1 = array(
                'file' => $_FILES['choice1_img']['tmp_name'],
                'size' => $_FILES['choice1_img']['size'],
                'name' => $_FILES['choice1_img']['name'],
                'type' => $_FILES['choice1_img']['type'],
                'crop' => array('width' => 213, 'height' => 280)
            );
            $file_upload1 = ShareFile($file_info1,0);
            if (!empty($file_upload1['filename'])) {
                $photo_poll['choice1_id'] = substr(sha1(rand(111, 999)), 0, 20);
                $photo_poll['choice1_img'] = $file_upload1['filename'];
                $photo_poll['choice1_url'] = GetMedia($file_upload1['filename']);
            }
        }
        if (!empty($_FILES['choice2_img']['tmp_name'])) {
            $file_info2 = array(
                'file' => $_FILES['choice2_img']['tmp_name'],
                'size' => $_FILES['choice2_img']['size'],
                'name' => $_FILES['choice2_img']['name'],
                'type' => $_FILES['choice2_img']['type'],
                'crop' => array('width' => 213, 'height' => 280)
            );
            $file_upload2 = ShareFile($file_info2,0);
            if (!empty($file_upload2['filename'])) {
                $photo_poll['choice2_id'] = substr(sha1(rand(111, 999)), 0, 20);
                $photo_poll['choice2_img'] = $file_upload2['filename'];
                $photo_poll['choice2_url'] = GetMedia($file_upload2['filename']);
            }
        }

        $insert_data         = array(
            'user_id' => $user_id,
            'shared_user_id' => 0,
            'shared_question_id' => 0,
            'ask_user_id' => 0,
            'ask_question_id' => 0,
            'replay_user_id' => 0,
            'replay_question_id' => 0,
            'is_anonymously' => ($_POST['is_anonymously'] == 'true') ? 1 : 0,
            'question' => $question_data['question'],
            'photo' => json_encode($photo_poll),
            'type' => 'photo_poll',
            'active' => 1,
            'public' => (isset($_POST['view_type'])) ? Secure($_POST['view_type']) : 0,
            'time' => time(),
            'lat' => $ask->user->lat,
            'lng' => $ask->user->lng
        );
        $create_question = $db->insert(T_QUESTIONS, $insert_data);
        if ($create_question) {
            $insert_data['id'] = $create_question;
            $insert_data['isowner'] = true;
            $qd = ToObject($insert_data);
            if (isset($question_data['mentions']) && is_array($question_data['mentions']) && !empty($question_data['mentions'])) {
                foreach ($question_data['mentions'] as $mention) {
                    $notif_data = array(
                        'notifier_id' => $user_id,
                        'recipient_id' => $mention,
                        'question_id' => $create_question,
                        'type' => 'mention_post',
                        'url' => ('@' . $ask->user->username),
                        'time' => time()
                    );
                    Notify($notif_data);
                }
            }
            $ask->mode = "all";
            $data = array(
                'status' => 200,
                'question_html' => LoadPage('timeline/partials/question', ['QUESTION_DATA' => QuestionData($qd)]),
                'message' => $success_icon . __('your_question_was_successfully_posted')
            );
        }
    }
}

if ($first == 'delete') {
    if (empty($_POST['question_id'])) {
        $errors[] = $error_icon . __('error_while_delete_question');
    }

    else {
        $is_owner    = false;
        $question_id = Secure($_POST['question_id']);


        $question_data = $db->where('id', $question_id)->getOne(T_QUESTIONS);
        if (!empty($question_data)) {
            if( $question_data->user_id == $user_id || IsAdmin()){
                $is_owner = true;
            }
        }
        if ($is_owner === true) {

            if (DeleteQuestion($question_id)) {
                $data = array(
                    'status' => 200,
                    'message' => __('question_deleted_successfully')
                );
            }else{
                $errors[] = $error_icon . __('error_while_delete_question');
            }

        }else{
            $errors[] = $error_icon . __('error_while_delete_question');
        }
    }
}

if ($first == 'edit') {
    if (empty($_POST['question_id']) OR empty($_POST['question']) OR strlen( trim($_POST['question']) ) > $ask->config->post_text_limit) {
        $errors[] = $error_icon . __('please_check_details');
        if(strlen( trim($_POST['question']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = $error_icon . str_replace('{{num}}',$ask->config->post_text_limit,__('character_limit_exceeded'));
        }
    }

    else {
        $is_owner    = false;
        $question_id = Secure($_POST['question_id']);
        $question = RenderQuestion(Secure($_POST['question']),false);
        $question_data = $db->where('id', $question_id)->getOne(T_QUESTIONS);
        if (!empty($question_data)) {
            if( $question_data->user_id == $user_id || IsAdmin()){
                $is_owner = true;
            }
        }
        if ($is_owner === true) {
            $is_anonymously = ($_POST['is_anonymously'] == 'true') ? 1 : 0;
            $update_question = $db->where('id', $question_id)->update(T_QUESTIONS, array('question'=>$question['question'],'is_anonymously' => $is_anonymously));
            if ($update_question) {
                $question_data->question = $question['question'];
                $question_data->is_anonymously = $is_anonymously;
                $qd = ToObject($question_data);
                $question_data->isowner = true;
                if (isset($question['mentions']) && is_array($question['mentions']) && !empty($question['mentions'])) {
                    $db->where('notifier_id', $user_id)->where('question_id', $question_id)->where('type', 'mention_post')->delete(T_NOTIFICATIONS);
                    foreach ($question['mentions'] as $mention) {
                        $notif_data = array(
                            'notifier_id' => $user_id,
                            'recipient_id' => $mention,
                            'question_id' => $question_id,
                            'type' => 'mention_post',
                            'url' => ('@' . $ask->user->username),
                            'time' => time()
                        );
                        Notify($notif_data);
                    }
                }
                $ask->mode = 'single';
                $q = QuestionData($qd);
                $msg = '';
                if(in_array('reply',$q->post_type)) {
                    $msg = __('reply_updated_successfully');
                }else if(in_array('answer',$q->post_type)) {
                    $msg = __('answer_updated_successfully');
                }else{
                    $msg = __('question_updated_successfully');
                }
                $data = array(
                    'status' => 200,
                    'question_html' => LoadPage('timeline/partials/question', ['QUESTION_DATA' => $q]),
                    'message' => $msg
                );
            }
        }
    }
}

if ($first == 'report') {
    if (empty($_POST['report_text'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else {
        $question_id = Secure($_POST['question_id']);
        $report_text = Secure($_POST['report_text']);
        $insert_data         = array(
            'user_id' => $user_id,
            'question_id' => $question_id,
            'text' => $report_text,
            'time' => time()
        );
        $create_report = $db->insert(T_REPORTS, $insert_data);
        if ($create_report) {
            $data = array(
                'status' => 200,
                'message' => $success_icon . __('your_report_was_successfully_posted')
            );
        }
    }
}

if ($first == 'delete_report') {
    if (empty($_POST['question_id']) OR empty($_POST['user_id'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else {
        $is_owner    = false;
        $question_id = Secure($_POST['question_id']);
        $report_data = $db->where('question_id', $question_id)->where('user_id', $user_id)->getOne(T_REPORTS);
        if (!empty($report_data)) {
            if( $report_data->user_id == $user_id || IsAdmin()){
                $is_owner = true;
            }
        }
        if ($is_owner === true) {
            $delete_question_report = $db->where('question_id', $question_id)->where('user_id', $user_id)->delete(T_REPORTS);
            if ($delete_question_report) {
                $data = array(
                    'status' => 200,
                    'message' => __('report_deleted_successfully')
                );
            }
        }
    }
}

if( $first == 'like' ) {
    if (empty($_POST['question_id']) OR empty($_POST['like_user_id'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else {
        $question_id = Secure($_POST['question_id']);
        $u_id = Secure($_POST['like_user_id']);
        $is_liked = $db->where('question_id',$question_id)->where('user_id',$user_id)->getValue(T_LIKES, 'count(*)');
        $like_created = false;

//        if( (int)$user_id == (int)$u_id ){
//            $errors[] = $error_icon . __('please_check_details');
//        }

        //else{

            $notif_data = array(
                'notifier_id' => $user_id,
                'recipient_id' => $u_id,
                'question_id' => $question_id,
                'type' => 'like',
                'url' => ('@' . $ask->user->username),
                'time' => time()
            );

            if($is_liked === 0){
                $like_created = $db->insert(T_LIKES, array('user_id'=>$user_id,'question_id'=>$question_id));
                if( (int)$user_id !== (int)$u_id ) {
                    Notify($notif_data);
                }
            }else{
                $like_created = $db->where('question_id',$question_id)->where('user_id',$user_id)->delete(T_LIKES);
                $like_created = $db->where('recipient_id', $u_id)->where('type', 'like')->where('notifier_id', $user_id)->delete(T_NOTIFICATIONS);
            }

            if($like_created){
                $likes_count = $db->where('question_id',$question_id)->getValue(T_LIKES, 'count(*)');
                $data = array(
                    'status' => 200,
                    'likes_count' => $likes_count,
                    'mode' => ( $is_liked === 0 ) ? 'like' : 'dislike'
                );
            }

        //}

    }
}

if( $first == 'like_replay' ) {
    if (empty($_POST['question_id']) OR empty($_POST['like_user_id'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else {
        $question_id = Secure($_POST['question_id']);
        $u_id = Secure($_POST['like_user_id']);
        $is_liked = $db->where('question_id',$question_id)->where('user_id',$user_id)->getValue(T_LIKES, 'count(*)');
        $like_created = false;

//        if( (int)$user_id == (int)$u_id ){
//            $errors[] = $error_icon . __('please_check_details');
//        }

        //else{

        $notif_data = array(
            'notifier_id' => $user_id,
            'recipient_id' => $u_id,
            'question_id' => $question_id,
            'replay_id' => $question_id,
            'type' => 'like_replay',
            'url' => ('@' . $ask->user->username),
            'time' => time()
        );

        if($is_liked === 0){
            $like_created = $db->insert(T_LIKES, array('user_id'=>$user_id,'question_id'=>$question_id));
            Notify($notif_data);
        }else{
            $like_created = $db->where('question_id',$question_id)->where('user_id',$user_id)->delete(T_LIKES);
            $like_created = $db->where('recipient_id', $u_id)->where('type', 'like')->where('notifier_id', $user_id)->delete(T_NOTIFICATIONS);
        }

        if($like_created){
            $likes_count = $db->where('question_id',$question_id)->getValue(T_LIKES, 'count(*)');
            $data = array(
                'status' => 200,
                'likes_count' => $likes_count,
                'mode' => ( $is_liked === 0 ) ? 'like' : 'dislike'
            );
        }

        //}

    }
}

if( $first == 'likes_list' ) {
    if (empty($_POST['question_id']) OR empty($_POST['user_id'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else {

        $question_id = Secure($_POST['question_id']);
        $html = '';
        foreach (UserQuestionLikes($question_id) as $key => $user) {
            $html .= LoadPage('timeline/partials/people_suggestion_list',array('USER_DATA'=>$user));
        }

        $data = array(
            'status' => 200,
            'html' => $html
        );
    }
}

if( $first == 'share' ) {
    if (empty($_POST['question_id']) OR empty($_POST['question_user_id'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else {
        $question_id = Secure($_POST['question_id']);
        $question_user_id = Secure($_POST['question_user_id']);
        $question = $db->where('id',$question_id)->getOne(T_QUESTIONS,array('*'));

        $is_shared = $db->where('user_id',(int)$user_id)->where('shared_user_id',(int)$question_user_id)->where('shared_question_id',(int)$question_id)->getValue(T_QUESTIONS, 'count(*)');

        if( (int)$question_user_id == (int)$user_id ){
            $is_shared = 1;
        }

        if($is_shared === 0) {

            $q_share = array();
            foreach ($question as $key => $value) {
                $q_share[$key] = $value;
            }
            unset($q_share['id']);
            $q_share['user_id'] = (int)$user_id;
            $q_share['shared_user_id'] = (int)$question_user_id;
            $q_share['shared_question_id'] = (int)$question_id;
            $q_share['time'] = time();
            $new_photo_poll = array();
            if ($q_share['photo'] !== '') {
                $new_photo_poll = json_decode($q_share['photo'], true);

                if(is_array($new_photo_poll)) {
                    $new_photo_poll['choice1_id'] = substr(sha1(rand(111, 999)), 0, 20);
                    $new_photo_poll_choice1_img = str_replace('_image.jpg', rand(11111, 99999) . '_image.jpg', $new_photo_poll['choice1_img']);
                    @copy($ask->base_path . str_replace('/', $ask->directory_separator, $new_photo_poll['choice1_img']), $ask->base_path . str_replace('/', $ask->directory_separator, $new_photo_poll_choice1_img));
                    $new_photo_poll['choice1_img'] = $new_photo_poll_choice1_img;
                    if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on')) {
                        UploadToS3($new_photo_poll_choice1_img);
                    }
                    $new_photo_poll['choice1_url'] = GetMedia($new_photo_poll_choice1_img);

                    $new_photo_poll['choice2_id'] = substr(sha1(rand(111, 999)), 0, 20);
                    $new_photo_poll_choice2_img = str_replace('_image.jpg', rand(11111, 99999) . '_image.jpg', $new_photo_poll['choice2_img']);
                    @copy($ask->base_path . str_replace('/', $ask->directory_separator, $new_photo_poll['choice2_img']), $ask->base_path . str_replace('/', $ask->directory_separator, $new_photo_poll_choice2_img));
                    $new_photo_poll['choice2_img'] = $new_photo_poll_choice2_img;
                    if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on')) {
                        UploadToS3($new_photo_poll_choice2_img);
                    }
                    $new_photo_poll['choice2_url'] = GetMedia($new_photo_poll_choice2_img);

                    $q_share['photo'] = json_encode($new_photo_poll);
                }else{
                    $q_share['photo'] = '';
                }
            }


            $create_question = $db->insert(T_QUESTIONS, $q_share);
            if ($create_question) {
                $notif_data = array(
                    'notifier_id' => $user_id,
                    'recipient_id' => $question_user_id,
                    'question_id' => $create_question,
                    'type' => 'share',
                    'url' => ('post/' . $create_question),
                    'time' => time()
                );
                Notify($notif_data);
                $data = array(
                    'status' => 200,
                    'message' => $success_icon . __('question_shared_successfully')
                );
            }

        }else{
            $errors[] = $error_icon . __('you_already_shared_this_post');
        }
    }
}

if( $first == 'answer' ) {
    if (empty($_POST['question_id']) OR empty($_POST['question_user_id']) OR empty($_POST['answer']) OR strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit) {
        $errors[] = $error_icon . __('please_check_details');
        if(strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = $error_icon . str_replace('{{num}}',$ask->config->post_text_limit,__('character_limit_exceeded'));
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
        $errors = array();
        $errors[] = $error_icon . __('post_limit_exceeded');
    }
    else {

        if (Secure($_POST['question_user_id']) == $user_id) {
            $errors[] = $error_icon . __('please_check_details');
        }

        else{

            $question_id = Secure($_POST['question_id']);
            $question = $db->where('id',$question_id)->getOne(T_QUESTIONS,array('question','is_anonymously','type'));
            $question_user_id = Secure($_POST['question_user_id']);
            $answer_data = RenderQuestion(Secure($_POST['answer']));

            $insert_data         = array(
                'user_id' => $user_id,
                'ask_user_id' => $question_user_id,
                'ask_question_id' => $question_id,
                'shared_user_id' => 0,
                'shared_question_id' => 0,
                'replay_user_id' => 0,
                'replay_question_id' => 0,
                'is_anonymously' => $question->is_anonymously,
                'question' => htmlspecialchars_decode($answer_data['question']),
                'photo' => null,
                'type' => $question->type,
                'active' => 1,
                'time' => time(),
                'lat' => $ask->user->lat,
                'lng' => $ask->user->lng
            );

            $create_question = $db->insert(T_QUESTIONS, $insert_data);
            if ($create_question) {
                $insert_data['id'] = $create_question;
                $insert_data['isowner'] = true;
                $qd = ToObject($insert_data);

                if (isset($answer_data['mentions']) && is_array($answer_data['mentions']) && !empty($answer_data['mentions'])) {
                    foreach ($answer_data['mentions'] as $mention) {
                        $notif_data = array(
                            'notifier_id' => $user_id,
                            'recipient_id' => $mention,
                            'question_id' => $create_question,
                            'type' => 'mention_answer',
                            'url' => ('@' . $ask->user->username),
                            'time' => time()
                        );
                        Notify($notif_data);
                    }
                }

                $notif_data = array(
                    'notifier_id' => $user_id,
                    'recipient_id' => $question_user_id,
                    'question_id' => $create_question,
                    'type' => 'answer_question',
                    'url' => ('post/' . $create_question),
                    'time' => time()
                );
                Notify($notif_data);

                $data = array(
                    'status' => 200,
                    'question_html' => LoadPage('timeline/partials/answer', ['QUESTION_DATA' => QuestionData($qd)]),
                    'message' => $success_icon . __('your_answer_was_successfully_posted')
                );
            }

        }

    }
}

if( $first == 'replay' ) {
    if (empty($_POST['question_id']) OR empty($_POST['question_user_id']) OR empty($_POST['answer']) OR empty($_POST['ask_question_id']) OR strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit) {
        $errors[] = $error_icon . __('please_check_details');
        if(strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = $error_icon . str_replace('{{num}}',$ask->config->post_text_limit,__('character_limit_exceeded'));
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
        $errors = array();
        $errors[] = $error_icon . __('post_limit_exceeded');
    }
    else {

        $question_id = Secure($_POST['question_id']);
        $question = $db->where('id',$question_id)->getOne(T_QUESTIONS,array('question','is_anonymously','type'));
        $question_user_id = Secure($_POST['question_user_id']);

        $ask_user_id = Secure($_POST['ask_user_id']);
        $ask_question_id = Secure($_POST['ask_question_id']);

        $answer_data = RenderQuestion(Secure($_POST['answer']));

        $insert_data         = array(
            'user_id' => $user_id,
            'ask_user_id' => 0,//$ask_user_id,
            'ask_question_id' => 0,//$ask_question_id,
            'replay_user_id' => $user_id,
            'replay_question_id' => $question_id,
            'shared_user_id' => 0,
            'shared_question_id' => 0,
            'is_anonymously' => $question->is_anonymously,
            'question' => htmlspecialchars_decode($answer_data['question']),
            'photo' => null,
            'type' => $question->type,
            'active' => 1,
            'time' => time()
        );

        $create_question = $db->insert(T_QUESTIONS, $insert_data);
        if ($create_question) {
            $insert_data['id'] = $create_question;
            $insert_data['isowner'] = true;
            $insert_data['is_replay'] = true;
            $qd = ToObject($insert_data);

            if (isset($answer_data['mentions']) && is_array($answer_data['mentions']) && !empty($answer_data['mentions'])) {
                foreach ($answer_data['mentions'] as $mention) {
                    $notif_data = array(
                        'notifier_id' => $user_id,
                        'recipient_id' => $mention,
                        'question_id' => $question_id,
                        'replay_id' => $create_question,
                        'type' => 'mention_replay',
                        'url' => ('@' . $ask->user->username),
                        'time' => time()
                    );
                    Notify($notif_data);
                }
            }

            $notif_data = array(
                'notifier_id' => $user_id,
                'recipient_id' => $question_user_id,
                'question_id' => $question_id,
                'replay_id' => $create_question,
                'type' => 'replay_question',
                'url' => ('post/' . $create_question),
                'time' => time()
            );
            Notify($notif_data);

            $ask->mode = 'single';
            $data = array(
                'status' => 200,
                'question_html' => LoadPage('timeline/partials/question', ['QUESTION_DATA' => QuestionData($qd)]),
                'message' => $success_icon . __('done')
            );
        }

    }
}

if ($first == 'search') {
    if (empty($_POST['keyword_search'])) {
        $errors[] = $error_icon . __('please_check_details');
    }
    else {
        $keyword_search = Secure($_POST['keyword_search']);
        $db->where('keyword',$keyword_search)->update(T_KEYWORD_SEARCH,array('hits'=>$db->inc(1)));
        $cnt = $db->where('keyword',$keyword_search)->getValue(T_KEYWORD_SEARCH, "count(id)");
        if($cnt==0){
            // $top_ids = $db->rawQuery('SELECT GROUP_CONCAT(`id`) AS x FROM (SELECT `id` FROM `'.T_RECENT_SEARCH.'` ORDER BY `time` DESC LIMIT 10) AS rows');
            // if( $top_ids[0]->x !== NULL ){
            //     $db->rawQuery('DELETE FROM '.T_RECENT_SEARCH.' WHERE `id` NOT IN ('.$top_ids[0]->x.')');
            // }
            $db->insert(T_KEYWORD_SEARCH,array('keyword'=>$keyword_search,'hits'=>1,'time'=>time()));
            $db->insert(T_RECENT_SEARCH,array('keyword'=>$keyword_search,'hits'=>1,'time'=>time()));
        }
        $users = $db->where('username','%'.$keyword_search.'%', 'like')
                    ->Where('active', '1')
                    ->orWhere('first_name','%'.$keyword_search.'%', 'like')
                    ->orWhere('last_name','%'.$keyword_search.'%', 'like')
                    ->orWhere('about','%'.$keyword_search.'%', 'like')
                    ->orderBy('id', 'DESC')
                    ->get(T_USERS, 9,array('id'));
        $questions = $db->where('question','%'.$keyword_search.'%', 'like')
                        ->Where('active', '1')
                        ->orderBy('id', 'DESC')
                        ->get(T_QUESTIONS, 5,array('*'));

        $questions_html = '';
        foreach ($questions as $key => $question){
            $ask->mode = "all";
            if($question->user_id == $user->id){
                $question->isowner = true;
            }else{
                $question->isowner = false;
            }
            $questions_html .= LoadPage('timeline/partials/question', ['QUESTION_DATA' => QuestionData($question)]);
        }

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

        $recent_search = $db->orderBy('time', 'DESC')->get(T_RECENT_SEARCH, 10, array('id','keyword'));
        $recent_search_html = LoadPage('search/recent_search' , ['RECENT_SEARCH' => $recent_search]);

        $data = array(
            'status' => 200,
            'keyword_search' => $keyword_search,
            'users' => $users_html,
            'questions' => $questions_html,
            'recent_search_html' => $recent_search_html,
            'message' => $success_icon . __('done')
        );
    }
}

if ($first == 'clear_recent_search') {
    $db->rawQuery('DELETE FROM '.T_RECENT_SEARCH.' ORDER BY time DESC limit 11');
    $data = array(
        'status' => 200,
        'message' => $success_icon . __('done')
    );
}

if( $first == 'promote' ) {
    if (empty($_POST['question_id']) || empty($_POST['user_id']) || $is_owner === false) {
        $errors[] = $error_icon . __('please_check_details');
    }
    else {

        $is_question_owner    = false;
        $question_id = Secure($_POST['question_id']);
        $question_data = $db->where('id', $question_id)->getOne(T_QUESTIONS);
        if (!empty($question_data)) {
            if( $question_data->user_id == $user_id || IsAdmin()){
                $is_question_owner = true;
            }
        }
        if( $ask->user->wallet < $ask->config->promote_question_cost ){
            $is_question_owner = false;
        }
        if ($is_question_owner === true) {
            $update_question = $db->where('id', $question_id)->update(T_QUESTIONS, array('promoted' => time()));
            if ($update_question) {
                $db->where('id', Secure($_POST['user_id']))->update(T_USERS, array('wallet' => $db->dec($ask->config->promote_question_cost)));
                $data = array(
                    'status' => 200,
                    'message' => __('question_promoted_successfully')
                );
            }
        }else{
            $errors[] = $error_icon . __('please_check_details');
        }

    }
}

if( $first == 'unpromote' ) {

    if (empty($_POST['question_id']) || empty($_POST['user_id']) || $is_owner === false) {
        $errors[] = $error_icon . __('please_check_details');
    }
    else {

        $is_question_owner    = false;
        $question_id = Secure($_POST['question_id']);
        $u_id = (int)Secure($_POST['user_id']);
        $question_data = $db->where('id', $question_id)->getOne(T_QUESTIONS);
        if (!empty($question_data)) {
            if( $question_data->user_id == $u_id || IsAdmin()){
                $is_question_owner = true;
            }
        }

        if ($is_question_owner === true) {
            $update_question = $db->where('id', $question_id)->update(T_QUESTIONS, array('promoted' => 0));
            if ($update_question) {
                //in case if you want to refund user
                //$db->where('id', Secure($_POST['user_id']))->update(T_USERS, array('wallet' => $db->inc($ask->config->promote_question_cost)));
                $data = array(
                    'status' => 200,
                    'message' => __('question_unpromoted_successfully')
                );
            }
        }else{
//
//            var_dump($is_owner);
//            var_dump($_POST['user_id']);
//            var_dump($user->id);
//            var_dump(IsAdmin());
//            var_dump(empty($_POST['question_id']));
//            var_dump(empty($_POST['user_id']));
//            var_dump($is_owner === false);

            $errors[] = $error_icon . __('please_check_details');
        }

    }
}

if( $first == 'vote' ){
    if ( ( empty($_POST['question_id']) || empty($_POST['choice_id']) || empty($_POST['other_choice_id']) || empty($_POST['user_id']) ) ) {
        $errors[] = $error_icon . __('please_check_details');
    }
    else {

        $question_id = Secure($_POST['question_id']);
        $choice_id = Secure($_POST['choice_id']);
        $other_choice_id = Secure($_POST['other_choice_id']);
        if (IsQuestionVoted($question_id) === false) {
            $insert_vote = $db->insert(T_QUESTIONS_VOTES, array('question_id' => $question_id,'choice_id' => $choice_id , 'user_id' => $user_id ,'vote_time' => time()));
            if ($insert_vote) {
                //in case if you want to refund user
                //$db->where('id', Secure($_POST['user_id']))->update(T_USERS, array('wallet' => $db->inc($ask->config->promote_question_cost)));
                $data = array(
                    'status' => 200,
                    'votes_count' => QuestionVotes($question_id),
                    'new_percentages' => GetVotePercentages($choice_id, $other_choice_id),
                    'message' => __('question_voted_successfully')
                );
            }
        }else{
            $errors[] = $error_icon . __('please_check_details');
        }

    }
}





header("Content-type: application/text");
if (isset($errors)) {
    echo json_encode(array(
        'errors' => $errors
    ));
    exit();
}
