<?php
if( $option !== 'get-trending'){
    if (empty($_POST['user_id']) || !IsLogged()) {
            $data       = array(
                'status'     => '400',
                'error'         => 'You are not logged in'
            );
        echo json_encode($data);
        exit();
    }
}
$is_owner = false;
  
if ($_POST['user_id'] == $user->id || IsAdmin()) {
    $is_owner = true;
}
$ask->user = UserData($_POST['user_id']);

$user_id = Secure($_POST['user_id']);

if ($option == 'normal') {
    if (empty($_POST['question']) || empty($_POST['profile_user_id']) || strlen( trim($_POST['question']) ) > $ask->config->post_text_limit) {
         $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
        if(strlen( trim($_POST['question']) ) > $ask->config->post_text_limit){

            $errors[] = str_replace('{{num}}',$ask->config->post_text_limit,"character_limit_exceeded");
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
         $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
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
            'postLinkTitle'   =>  Secure($_POST['url_title']),
            'postLink'        =>  Secure($_POST['url_link']),
            'postLinkImage'   =>  Secure($_POST['url_image']),
            'postLinkContent' =>  Secure($_POST['url_content'])

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
                'message' => 'your question was successfully posted',
                'data' => QuestionData(GetQestionByID($create_question), true)
            );
        }

    }
}

if ($option == 'photo') {
    if (empty($_POST['question']) OR empty($_FILES['choice1_img']['tmp_name']) OR empty($_FILES['choice2_img']['tmp_name']) OR strlen( trim($_POST['question']) ) > $ask->config->post_text_limit) {
         $data       = array(
            'status'     => '400',
            'errors'         => array(
                'error_id'   => '1',
                'error_text' => 'Bad Request, Invalid or missing parameter'
            )
        );
        if(strlen( trim($_POST['question']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = str_replace('{{num}}',$ask->config->post_text_limit, "character_limit_exceeded");
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
          $data       = array(
            'status'     => '400',
            'error'         => 'Post limit exceeded'
        );
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
                'message' => "your question was successfully posted",
                'data' => QuestionData(GetQestionByID($create_question), true)
            );
        }
    }
}

if ($option == 'delete') {
    if (empty($_POST['question_id'])) {
            $data       = array(
                        'status'     => '400',
                        'error'         => 'error while delete question'
             );
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
                    'message' => "question deleted successfully"
                );
            }else{
               $data       = array(
                    'status'     => '400',
                    'error'         => 'error while delete question'
              );
            }

        }else{
                 $data       = array(
                    'status'     => '400',
                    'error'         => 'error while delete question'
             );
        }
    }
}

if ($option == 'edit') {
    if (empty($_POST['question_id']) OR empty($_POST['question']) OR strlen( trim($_POST['question']) ) > $ask->config->post_text_limit) {
          $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
        if(strlen( trim($_POST['question']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = str_replace('{{num}}',$ask->config->post_text_limit,"character_limit_exceeded");
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
                    $msg = "Reply updated successfully";
                }else if(in_array('answer',$q->post_type)) {
                    $msg = "Answer_updated_successfully";
                }else{
                    $msg = "Question_updated_successfully";
                }
                $data = array(
                    'status' => 200,
                    'message' => $msg,
                    'data' => QuestionData(GetQestionByID($question_id),true)
                );
            }
        }
    }
}

if ($option == 'report') {
    if (empty($_POST['report_text'])) {
          $data       = array(
                'status'     => '400',
                'error'         => 'Bad Request, Invalid or missing parameter'
        );
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
                'message' => " your report was successfully posted"
            );
        }
    }
}

if ($option == 'delete_report') {
    if (empty($_POST['question_id']) OR empty($_POST['user_id'])) {
          $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
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
                    'message' =>  "report deleted successfully"
                );
            }
        }
    }
}

if( $option == 'like' ) {
    if (empty($_POST['question_id']) OR empty($_POST['like_user_id'])) {
         $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
    }

    else {
        $question_id = Secure($_POST['question_id']);
        $u_id = Secure($_POST['like_user_id']);
        $is_liked = $db->where('question_id',$question_id)->where('user_id',$user_id)->getValue(T_LIKES, 'count(*)');
        $like_created = false;

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
                    'publisher_data' => UserData($u_id),
                    'likes_count' => $likes_count,
                    'mode' => ( $is_liked === 0 ) ? 'like' : 'dislike'
                );
            }

        //}

    }
}

if( $option == 'like_replay' ) {
    if (empty($_POST['question_id']) OR empty($_POST['like_user_id'])) {
          $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
    } 

    else {
        $question_id = Secure($_POST['question_id']);
        $u_id = Secure($_POST['like_user_id']);
        $is_liked = $db->where('question_id',$question_id)->where('user_id',$user_id)->getValue(T_LIKES, 'count(*)');
        $like_created = false;


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

if( $option == 'likes_list' ) {
    if (empty($_POST['question_id']) OR empty($_POST['user_id'])) {
          $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
    }

    else {

        $offset             = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
        $limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;
        $offset_text = '';
        if ($offset > 0) {
            $offset_text = ' AND `id` > ' . $offset;
        }
        $limit_text = '';
        if ($limit > 0) {
            $limit_text = ' limit ' . $limit;
        }


        $question_id = Secure($_POST['question_id']);
        $likes = [];
        $query_one = " SELECT `user_id` FROM " . T_LIKES . " WHERE `question_id` = '".$question_id."' ". $offset_text ." ORDER BY `id` " . $limit_text;
        $sql = mysqli_query($sqlConnect, $query_one);
        while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $likes[] = UserData($fetched_data['user_id']);
        }

        $data = array(
            'status' => 200,
            'users' => $likes
        );
    }
}

if( $option == 'share' ) {
    if (empty($_POST['question_id']) OR empty($_POST['question_user_id'])) {
              $data       = array(
                'status'     => '400',
                'error'         => 'Bad Request, Invalid or missing parameter'
            );
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
                    'message' => "question shared successfully"
                );
            }

        }else{
              $data       = array(
                'status'     => '400',
                'error'         => 'You already shared this post'
        );
        }
    }
}

if( $option == 'answer' ) {
    if (empty($_POST['question_id']) OR empty($_POST['question_user_id']) OR empty($_POST['answer']) OR strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit) {
        $errors[] = "please check details";
        if(strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = str_replace('{{num}}',$ask->config->post_text_limit, "character limit exceeded");
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
           $data    = array(
                'status'  => '400',
                'error' => 'Post limit exceeded'
        );
    }
    else {

        if (Secure($_POST['question_user_id']) == $user_id) {
            $data    = array(
                'status'  => '400',
                'error' => 'Bad Request, Invalid or missing parameter'
            );
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
                    'message' => "your answer was successfully posted"
                );
            }

        }

    }
}

if( $option == 'replay' ) {
    if (empty($_POST['question_id']) OR empty($_POST['question_user_id']) OR empty($_POST['answer']) OR empty($_POST['ask_question_id']) OR strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit) {
        $data    = array(
                'status'  => '400',
                'error' => 'Bad Request, Invalid or missing parameter'
            );
        if(strlen( trim($_POST['answer']) ) > $ask->config->post_text_limit){
            $errors = array();
            $errors[] = str_replace('{{num}}',$ask->config->post_text_limit, "character limit exceeded");
        }
    }
    else if (CheckIfUserCanPost($ask->config->max_post_per_hour) === false) {
       $data    = array(
            'status'  => '400',
            'error' => 'Post Limit exceeded'
        );
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
                'message' => "Reply submitted"
            );
        }

    }
}

if ($option == 'search') {
    if (empty($_POST['keyword_search'])) {
        $data       = array(
        'status'     => '400',
        'error'         => 'Bad Request, Invalid or missing parameter'
    );
    }
    else {
        $users_offset      = (isset($_POST['users_offset']) && is_numeric($_POST['users_offset']) && $_POST['users_offset'] > 0) ? secure($_POST['users_offset']) : 0;
        $users_limit       = (isset($_POST['users_limit']) && is_numeric($_POST['users_limit']) && $_POST['users_limit'] > 0) ? secure($_POST['users_limit']) : 20;

        $questions_offset  = (isset($_POST['questions_offset']) && is_numeric($_POST['questions_offset']) && $_POST['questions_offset'] > 0) ? secure($_POST['questions_offset']) : 0;
        $questions_limit   = (isset($_POST['questions_limit']) && is_numeric($_POST['questions_limit']) && $_POST['questions_limit'] > 0) ? secure($_POST['questions_limit']) : 20;

        $users_offset_text = '';
        if ($users_offset > 0) {
            $users_offset_text = ' AND `id` > ' . $users_offset;
        }

        $questions_offset_text = '';
        if ($questions_offset > 0) {
            $questions_offset_text = ' AND `id` > ' . $questions_offset;
        }

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
                    ->Where('id', $users_offset, '>')
                    ->orWhere('first_name','%'.$keyword_search.'%', 'like')
                    ->orWhere('last_name','%'.$keyword_search.'%', 'like')
                    ->orWhere('about','%'.$keyword_search.'%', 'like')
                    ->orderBy('id', 'DESC')
                    ->get(T_USERS, $users_limit,array('id'));
        $questions = $db->where('question','%'.$keyword_search.'%', 'like')
                        ->Where('active', '1')
                        ->Where('id', $questions_offset, '>')
                        ->orderBy('id', 'DESC')
                        ->get(T_QUESTIONS, $questions_limit,array('*'));

        $questions_html = [];
        foreach ($questions as $key => $question){
            $ask->mode = "all";
            if($question->user_id == $user->id){
                $question->isowner = true;
            }else{
                $question->isowner = false;
            }
            $qd = QuestionData($question, true);
            if( $qd ) {
                $questions_html[] = $qd;
            }
        }

        $users_html = [];
        foreach ($users as $key => $user){

            $user_data = UserData($user->id);
            $user_data->FOLLOWER_BUTTON = GetFollowButton($user_data->id);

            $users_html[] = $user_data;

        }

        $recent_search = $db->orderBy('time', 'DESC')->get(T_RECENT_SEARCH, 20, array('id','keyword'));
        $recent_search_html = $recent_search;

        $data = array(
            'status' => 200,
            'keyword_search' => $keyword_search,
            'questions'      => $questions_html,
            'users'     => $users_html,
            'recent_search_html' => $recent_search_html,
            'message' => "done"
        );
    }
}

if ($option == 'clear_recent_search') {
    $db->rawQuery('DELETE FROM '.T_RECENT_SEARCH);
    $data = array(
        'status' => 200,
        'message' => "done"
    );
}

if( $option == 'promote' ) {
    if (empty($_POST['question_id']) || empty($_POST['user_id']) || $is_owner === false) {
        $data    = array(
                'status'  => '400',
                'error' => 'Bad Request, Invalid or missing parameter'
            );
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
                    'message' => "question promoted successfully"
                );
            }else{
                 $data = array(
                    'status' => 400,
                    'error' => "Bad request or Invalid operation"
                );
            }
        }else{
            $data = array(
                'status' => 400,
                'error' => "You are not question owner, so you can not promote it"
            );
        }

    }
}

if( $option == 'unpromote' ) {

    if (empty($_POST['question_id']) || empty($_POST['user_id']) || $is_owner === false) {
        $data    = array(
                'status'  => '400',
                'error' => 'Bad Request, Invalid or missing parameter'
            );
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
                    'message' => "question unpromoted successfully"
                );
            }
        }else{


           $data    = array(
                'status'  => '400',
                'error' => 'You cant promote this question'
            );
        }

    }
}

if( $option == 'vote' ){
    if ( ( empty($_POST['question_id']) || empty($_POST['choice_id']) || empty($_POST['other_choice_id']) || empty($_POST['user_id']) ) ) {
        $data    = array(
                'status'  => '400',
                'error' => 'Bad Request, Invalid or missing parameter'
            );
    }
    else {

        $question_id = Secure($_POST['question_id']);
        $choice_id = Secure($_POST['choice_id']);
        $other_choice_id = Secure($_POST['other_choice_id']);
        if (IsQuestionVoted($question_id) === false) {
            $insert_vote = $db->insert(T_QUESTIONS_VOTES, array('question_id' => $question_id,'choice_id' => $choice_id , 'user_id' => $user_id ,'vote_time' => time()));
            if ($insert_vote) {

                $data = array(
                    'status' => 200,
                    'votes_count' => QuestionVotes($question_id),
                    'new_percentages' => GetVotePercentages($choice_id, $other_choice_id),
                    'message' => "question voted successfully"
                );
            }
        }else{
           $data    = array(
                'status'  => '400',
                'error' => 'You have already cast your vote'
            );
        }

    }
}

if( $option == 'get-questions-by-id'){
    if (empty($_POST['question_id'])) {
        $data       = array(
            'status'     => '400',
            'error'         => 'Bad Request, Invalid or missing parameter'
        );
    }
    else {
        $question_id = Secure($_POST['question_id']);
        $question = $db->rawQuery('SELECT * FROM `'.T_QUESTIONS.'` WHERE `'.T_QUESTIONS.'`.id = '. $question_id);
        if($question[0]){
            $question = QuestionData(ToObject($question[0]), true);
            $data = array(
                'status' => 200,
                'data' => $question
            );
        }else{
            $data = array(
                'status' => 400,
                'error'         => 'Question not found'
            );
        }
    }
}

if( $option == 'nearby-questions'){
    $questions = GetQuestions(['page' => 'nearby']);
    $data = array(
        'status'        => 200,
        'questions'     => $questions
    );
}

if( $option == 'timeline-questions'){
    $offset             = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
    $limit              = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;
    $questions = GetQuestions(['page' => 'timeline','user_data' => $user_id, 'after_post_id' => $offset ,'limit' => $limit]);
    $data = array(
        'status'        => 200,
        'questions'     => $questions
    );
}

if( $option == 'get-questions-by-user-id'){
    if (empty($_POST['user_id']) OR empty($_POST['user_id'])) {
        $data       = array(
           'status'     => '400',
           'error'         => 'Bad Request, Invalid or missing parameter'
       );
    } else {
        $offset             = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
        $limit              = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;
        $uid = Secure($_POST['user_id']);
        $questions = GetQuestions(['page' => 'uid', 'order'=> 'ASC', 'user_data' => $uid, 'after_post_id' => $offset ,'limit' => $limit]);
        $data = array(
            'status'        => 200,
            'questions'     => $questions
        );
    }
}

if( $option == 'get-trending'){
    $questions = GetQuestions(['page' => 'trending','user_data' => $user_id]);
    $data = array(
        'status'        => 200,
        'questions'     => $questions
    );
}

header("Content-type: application/text");
if (isset($errors)) {
    echo json_encode(array(
        'error' => $errors
    ));
    exit();
}
