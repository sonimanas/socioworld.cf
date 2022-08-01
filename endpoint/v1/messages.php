<?php
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}
if ($option == 'upload_video') {

    $id = Secure($_POST['to']); 
    if (!empty($_FILES['video']['tmp_name'])) {
    if (!IsAdmin()) {
        if ($ask->user->user_upload_limit != '0') {
            if ($ask->user->user_upload_limit != 'unlimited') {
                if (($ask->user->uploads + $_FILES['video']['size']) >= $ask->user->user_upload_limit) {
                    $max  = size_format($ask->user->user_upload_limit);
                    $data = array('status' => 402,'message' => ($lang->file_is_too_big .": $max"));
                    echo json_encode($data);
                    exit();
                }
            }
        }
        else{
            if ($ask->config->upload_system_type == 'on') {
                if ($ask->config->max_upload_all_users != '0' && ($ask->user->uploads + $_FILES['video']['size']) >= $ask->config->max_upload_all_users) {
                    $max  = size_format($ask->config->max_upload_all_users);
                    $data = array('status' => 402,'error' => ($lang->file_is_too_big .": $max"));
                    echo json_encode($data);
                    exit();
                }
            }
        
        }
    }

    if ($_FILES['video']['size'] > $ask->config->max_upload) {
        $max  = size_format($ask->config->max_upload);
        $data = array('status' => 402,'error' => ($lang->file_is_too_big .": $max"));
        echo json_encode($data);
        exit();
    }

    $allowed           = 'mov';

    $new_string        = pathinfo($_FILES['video']['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
      
        $data = array('status' => 400, 'error' => $lang->file_not_supported);
        echo json_encode($data);
        exit();
    }

      $chat_exits = $db->where("user_one", $ask->user->id)->where("user_two", $id)->getValue(T_CHATS, 'count(*)');
            if (!empty($chat_exits)) {
                $db->where("user_two", $ask->user->id)->where("user_one", $id)->update(T_CHATS, array('time' => time()));
                $db->where("user_one", $ask->user->id)->where("user_two", $id)->update(T_CHATS, array('time' => time()));
                if ($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)') == 0) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            } else {
                $db->insert(T_CHATS, array('user_one' => $ask->user->id, 'user_two' => $id,'time' => time()));
                if (empty($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)'))) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            }

    $file_info = array(
        'file' => $_FILES['video']['tmp_name'],
        'size' => $_FILES['video']['size'],
        'name' => $_FILES['video']['name'],
        'type' => $_FILES['video']['type'],
        'allowed' => 'mp4,mov'
    );

    $file_upload = ShareFile($file_info);

     if (!empty($file_upload['filename'])) {
                    $thumbnail = secure($file_upload['filename'], 0);
                    $new_message = '<video controls width="250"><source src="{{CONFIG site_url}}/'.$thumbnail.'"type="video/mp4"/></video>';
                    $insert_message = array(
                        'from_id' => $ask->user->id,
                        'to_id' => $id,
                        'text' => $new_message,
                        'time' => time()
                    );
                    $msg_exits = $db->where("from_id", $ask->user->id)->where("to_id", $id)->where("text", $new_message)->getValue(T_MESSAGES, 'count(*)');
                    if (empty($msg_exits)) {
                        $insert = $db->insert(T_MESSAGES, $insert_message);
                 
                        if ($insert) {
                            $ask->message = GetMessageData($insert);
                            $data = array(
                                'status' => 200,
                                'message_id' => secure($_POST['message_id']),
                                'message' => LoadPage('messages/ajax/outgoing', array(
                                    'ID' => $ask->message->id,
                                    'TEXT' => $ask->message->text
                                ))
                            );
                        }
                    }
                }

            else if (!empty($file_upload['error'])) {
                $data = array('status' => 400, 'error' => $file_upload['error']);
            }
    }

}
if ($option == 'upload_media') {

    if ( empty($_POST['to']) && empty($_POST['message_id']) && empty($_FILES['media']) ) {
            $data       = array(
                    'status'     => '400', 
                    'error'         => 'Bad Request, Invalid or missing parameter'
                );
    }else {
        $id = Secure($_POST['to']);
        if ((int)$id !== $ask->user->id) {

            $chat_exits = $db->where("user_one", $ask->user->id)->where("user_two", $id)->getValue(T_CHATS, 'count(*)');
            if (!empty($chat_exits)) {
                $db->where("user_two", $ask->user->id)->where("user_one", $id)->update(T_CHATS, array('time' => time()));
                $db->where("user_one", $ask->user->id)->where("user_two", $id)->update(T_CHATS, array('time' => time()));
                if ($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)') == 0) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            } else {
                $db->insert(T_CHATS, array('user_one' => $ask->user->id, 'user_two' => $id,'time' => time()));
                if (empty($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)'))) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            }
          

            if (!empty($_FILES['media']['tmp_name'])) {
                $file_info   = array(
                    'file' => $_FILES['media']['tmp_name'],
                    'size' => $_FILES['media']['size'],
                    'name' => $_FILES['media']['name'],
                    'type' => $_FILES['media']['type'],
                    'crop' => array(
                        'width' => 600,
                        'height' => 600
                    )
                );
                $file_upload = ShareFile($file_info);
                if (!empty($file_upload['filename'])) {
                    $thumbnail = secure($file_upload['filename'], 0);
                    $new_message = '[img]'.$thumbnail.'[/img]';

                    //`seen`, `from_deleted`, `to_deleted`, `sent_push`, `notification_id`, `type_two`,`mediaFileNames`

                    $mediaFilename = $file_upload['filename'];
                    $mediaName     = $file_upload['name'];

                    $insert_message = array(
                        'from_id' => $ask->user->id,
                        'to_id' => $id,
                        'text' => $new_message,
                        'media' => Secure($mediaFilename),
                        'mediaFileName' => Secure($mediaName), 
                        'time' => time()
                    );
                    $msg_exits = $db->where("from_id", $ask->user->id)->where("to_id", $id)->where("text", $new_message)->getValue(T_MESSAGES, 'count(*)');
                    if (empty($msg_exits)) {
                        $insert = $db->insert(T_MESSAGES, $insert_message);                
                        if ($insert) {
                            $ask->message = GetMessageData($insert);
                            $ask->message->media = getmedia($ask->message->media);
                            $data = array(
                                'status' => 200,
                                'message_id' => secure($_POST['message_id']),
                                'message' =>  $ask->message
                            );
                        }else{
                            $data       = array(
                                'status'     => '400', 
                                'error'         =>  'Error while saving message',
                                'id' => $insert_message
                            );
                        }
                    }
                }
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'error',
                    'm' => $_FILES['media']['tmp_name'],
                    'error'         =>  'Bad Request, Invalid  ID or missing parameter'
                );
            }

        } else {

             $data       = array(
                'status'     => '400', 
                'error'         =>  'Bad Request, Invalid  ID or missing parameter'
            );
        }
    }

}
if ($option == 'new') {
    if (!empty($_POST['id']) && !empty($_POST['new-message'])) {
        $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
        $i          = 0;
        preg_match_all($link_regex, Secure($_POST['new-message']), $matches);
        foreach ($matches[0] as $match) {
            $match_url           = strip_tags($match);
            $syntax              = '[a]' . urlencode($match_url) . '[/a]';
            $_POST['new-message'] = str_replace($match, $syntax, $_POST['new-message']);
        }
        $new_message = Secure($_POST['new-message']);
        $id = Secure($_POST['id']);
        if ($id != $ask->user->id) {
            $chat_exits = $db->where("user_one", $ask->user->id)->where("user_two", $id)->getValue(T_CHATS, 'count(*)');
            if (!empty($chat_exits)) {
                $db->where("user_two", $ask->user->id)->where("user_one", $id)->update(T_CHATS, array('time' => time()));
                $db->where("user_one", $ask->user->id)->where("user_two", $id)->update(T_CHATS, array('time' => time()));
                if ($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)') == 0) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            } else {
                $db->insert(T_CHATS, array('user_one' => $ask->user->id, 'user_two' => $id,'time' => time()));
                if (empty($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)'))) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            }
            $insert_message = array(
                'from_id' => $ask->user->id,
                'to_id' => $id,
                'text' => $new_message,
                'time' => time()
            );
            $insert = $db->insert(T_MESSAGES, $insert_message);
            if ($insert) {
                $ask->message = GetMessageData($insert);
                $data = array(
                    'status' => 200,
                    'message_id' => $_POST['message_id'],
                    'message' => $ask->message
                );
            } else {
                    $data       = array(
                        'status'     => '400',
                        'error'         => 'Fatal Error message not sent'
                    );
                }
        }
    }
}
if ($option == 'fetch') {
    if (empty($_POST['last_id'])) {
        $_POST['last_id'] = 0;
    }
    if (empty($_POST['id'])) {
        $_POST['id'] = 0;
    }
    if (empty($_POST['first_id'])) {
        $_POST['first_id'] = 0;
    }
    $messages_html = [];
    $messages_html = GetMessagesApp($_POST['id'], ['last_id' => $_POST['last_id'], 'first_id' => $_POST['first_id'], 'return_method' => 'obj']);
    if (!empty($messages_html)) {
        $html =  $messages_html;
    } else {
        $html = "no messages ";
    }
    $users_html = [];

    $users_html = GetMessagesUserListApp(array('return_method' => 'html'));

    if (!empty($messages_html) || !empty($users_html)) {
        $data = array(
            'status' => 200,
            'message' => $messages_html, 
            //'users' => $users_html
        );
    }
}
if ($option == 'delete_chat') {
    if (!empty($_POST['id'])) {
        $id = Secure($_POST['id']);
        $messages = $db->where("(from_id = {$ask->user->id} AND to_id = {$id}) OR (from_id = {$id} AND to_id = {$ask->user->id})")->get(T_MESSAGES);
     if (!empty($messages)) {
        $update1 = array();
        $update2 = array();
        $erase = array();
        $images = array();
        foreach ($messages as $key => $message) {
            if ($message->from_deleted == 1 || $message->to_deleted == 1) {
                $erase[] = $message->id;

                $img = $message->text;
                if( substr($img, 0, 5) == '[img]' && substr($img, -6) == '[/img]'){
                    $img = str_replace(array('[img]','[/img]'), '', $img);
                    if( @file_exists( $img ) ){
                        $images[] = $img;
                    }
                }

            } else {
                if ($message->to_id == $ask->user->id) {
                    $update2[] = $message->id;
                } else {
                    $update1[] = $message->id;
                }
            }
        }
        if (!empty($erase)) {
            $erase = implode(',', $erase);
            $final_query = "DELETE FROM " . T_MESSAGES . " WHERE id IN ($erase)";
            $db->rawQuery($final_query);
            if(!empty($images)){
                foreach ($images as $image){
                    @unlink($image);
                }
            }
        }
        if (!empty($update1)) {
            $update1 = implode(',', $update1);
            $final_query = "UPDATE " . T_MESSAGES . " set `from_deleted` = '1' WHERE `id` IN({$update1}) ";
            $db->rawQuery($final_query);
        }
        if (!empty($update2)) {
            $update2 = implode(',', $update2);
            $final_query = "UPDATE " . T_MESSAGES . " set `to_deleted` = '1' WHERE `id` IN({$update2}) ";
            $db->rawQuery($final_query);
        }
        $delete_chats = $db->rawQuery("DELETE FROM " . T_CHATS . " WHERE user_one = {$ask->user->id} AND user_two = $id");
                $data     = array(
                    'status'   => '200',
                    'type' => 'delete_user_messages',
                    'message'    => 'Your messages successfully deleted.'
                );
       }

       else {
                $data     = array(
                    'status'   => '200',
                    'type' => 'delete_user_messages',
                    'message'    => 'No message to delete.'
                );

           }
    }    
      else {
                $data     = array(
                    'status'   => '200',
                    'type' => 'delete_user_messages',
                    'message'    => 'Recepient Id Should be  number'
                );

           }
}
if ($option == 'get-user-chat-list'){
    $users = [];
    $offset             = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
    $limit              = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;
    $users_obj = GetMessagesUserList(array('return_method' => 'obj', 'offset' => $offset ,'limit' => $limit, 'api' => true));
    if (empty($users_obj)) {
        $users_obj = new stdClass();
    }
    foreach ($users_obj as $key => $value){
        $users[$key]['user'] = $value->user;
        $users[$key]['get_count_seen'] = $value->get_count_seen;
        $users[$key]['get_last_message'] = $value->get_last_message;
    }
    $data     = array(
        'status'   => '200',
        'list' => $users
    );
}
?>