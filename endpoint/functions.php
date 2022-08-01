<?php
function GetFollowers($userid,$limit=20,$offset=0){
    global $db;
    $data = [];
    $sql   = 'SELECT * FROM `'.T_FOLLOWERS.'` ';
    $where = '
            WHERE
                `following_id` = '.secure($userid). '
            AND 
                `follower_id` NOT IN (SELECT `blocked_id` FROM `'.T_BLOCKS.'` WHERE `user_id` = '.secure($userid).')
            ';
    $order = ' ORDER BY `id` DESC ';
    $position = ' LIMIT '.$limit.';';
    $data['count'] = count($db->rawQuery($sql . $where));
    $data['data'] = $db->rawQuery($sql . $where . ' AND `id` > ' . $offset. $order . $position);
    foreach ($data['data'] as $key => $value){
        $data['data'][$key] = userData($value->follower_id);
        unset($data['data'][$key]->password);
    }
    return $data;
}
function GetMessagesApp($id, $data = array(),$limit = 50) {
    global $ask, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $chat_id = Secure($id);

    if (!empty($data['chat_user'])) {
        $chat_user = $data['chat_user'];
    } else {
        $chat_user = UserData($chat_id);
    }


    $where = "((`from_id` = {$chat_id} AND `to_id` = {$ask->user->id} AND `to_deleted` = '0') OR (`from_id` = {$ask->user->id} AND `to_id` = {$chat_id} AND `from_deleted` = '0'))";

    // count messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $data['last_id'] = Secure($data['last_id']);
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $data['first_id'] = Secure($data['first_id']);
        $db->where('id', $data['first_id'], '<');
    }

    $count_user_messages = $db->getValue(T_MESSAGES, "count(*)");
    $count_user_messages = $count_user_messages - $limit;
    if ($count_user_messages < 1) {
        $count_user_messages = 0;
    }

    // get messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $db->where('id', $data['first_id'], '<');
    }

    $get_user_messages = $db->orderBy('id', 'ASC')->get(T_MESSAGES, array($count_user_messages, $limit));

    $messages_html = [];

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }

    $update_seen = array();

    foreach ($get_user_messages as $key => $message) {
        $message_type = 'incoming';
        $get_user_messages[$key]->position = 'left';
        if ($message->from_id == $ask->user->id) {
            $message_type = 'outgoing';
            $get_user_messages[$key]->position = 'right';
        }
        if ($return_method == 'html') {
            $messages_html = [
                'ID' => $message->id,
                'AVATAR' => $chat_user->avatar,
                'NAME' => $chat_user->name,
                'TEXT' => MarkUp($message->text),
                'POSITION' => $message_type
            ];
        }
        $message->text = MarkUp($message->text);
        if ($message->seen == 0 && $message->to_id == $ask->user->id) {
            $update_seen[] = $message->id;
        }


        if(!empty($get_user_messages[$key]->media)){
            $get_user_messages[$key]->media = getMedia($get_user_messages[$key]->media);
        }
        $get_user_messages[$key]->time_text = Time_Elapsed_String($message->time);
    }

    if (!empty($update_seen)) {
        $update_seen = implode(',', $update_seen);
        $update_seen = $db->where("id IN ($update_seen)")->update(T_MESSAGES, array('seen' => time()));
    }
      // var_dump($get_user_messages);

    return (!empty($messages_html)) ? $messages_html : $get_user_messages;
}
function GetMessagesUserListApp($data = array()) {
    global $ask, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $db->where("user_one = {$ask->user->id}");

    if (isset($data['keyword'])) {
        $keyword = Secure($data['keyword']);
        $db->where("user_two IN (SELECT id FROM users WHERE username LIKE '%$keyword%' OR `name` LIKE '%$keyword%')");
    }

    $users = $db->orderBy('time', 'DESC')->get(T_CHATS, 20);

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }

    $users_html = '';
    $data_array = array();
    foreach ($users as $key => $user) {
        $user = UserData($user->user_two);
        if (!empty($user)) {
            $get_last_message = $db->where("((from_id = {$ask->user->id} AND to_id = $user->id AND `from_deleted` = '0') OR (from_id = $user->id AND to_id = {$ask->user->id} AND `to_deleted` = '0'))")->orderBy('id', 'DESC')->getOne(T_MESSAGES);
            $get_count_seen = $db->where("to_id = {$ask->user->id} AND from_id = $user->id AND `from_deleted` = '0' AND seen = 0")->orderBy('id', 'DESC')->getValue(T_MESSAGES, 'COUNT(*)');
            if ($return_method == 'html') {
                $users_html =  [
                    'ID' => $user->id,
                    'AVATAR' => $user->avatar,
                    'NAME' => $user->name,
                    'LAST_MESSAGE' => (!empty($get_last_message->text)) ? markUp( strip_tags($get_last_message->text) ) : '',
                    'COUNT' => (!empty($get_count_seen)) ? $get_count_seen : '',
                    'USERNAME' => $user->username
                ];
            } else {
                $data_array[$key]['user'] = $user;
                $data_array[$key]['get_count_seen'] = $get_count_seen;
                $data_array[$key]['get_last_message'] = $get_last_message;
            }
        }
    }
    $users_obj = (!empty($data_array)) ? ToObject($data_array) : array();
    return (!empty($users_html)) ? $users_html : $users_obj;
}