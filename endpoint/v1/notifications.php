<?php
if (IS_LOGGED == false) {
    $data = array('status' => 400, 'error' => 'You ain\'t logged in!');
    echo json_encode($data);
    exit();
}

$data['status'] = 400;

if ($option == 'get') {
	$user_id   = $ask->user->id;
    $type      = (!empty($_GET['t'])) ? Secure($_GET['t']) : 'all';
    $show_all  = (!empty($_GET['sa'])) ? Secure($_GET['sa']) : false;
    $offset            = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
    $limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;

    $html      = "";
    $t_notif   = T_NOTIFICATIONS;
    $notif_set = GetNotification(array(
        'recipient_id' => $user_id,
        'type' => $type,
        'limit' => $limit,
        'offset' => $offset
    ));
    if ($type == 'new' && !empty($notif_set) && is_numeric($notif_set)) {
        $data['status'] = 200;
        $data['new']    = intval($notif_set);
    } else if (( $type == 'all' || $type == 'only_new' ) && count($notif_set) > 0) {
        $data['status'] = 200;
        $update = array();
        $new    = 0;
        foreach ($notif_set as $data_row) {
            $data_row['notifier'] = UserData($data_row['notifier_id']);
            $question = $db->arrayBuilder()->where('id',$data_row['question_id'])->getOne(T_QUESTIONS,array('is_anonymously'));
            if( $question['is_anonymously'] == 1 ){
                $data_row['notifier']->avatar = GetMedia('upload/photos/d-avatar.jpg');
                $data_row['notifier']->name = __('someone');
                $data_row['url'] = '';
            }
            $icon  = $ask->notif_data[$data_row['type']]['icon'];
            $title  = $ask->notif_data[$data_row['type']]['text'];
            $action  = $ask->notif_data[$data_row['type']]['action'];
            $data_load  = $ask->notif_data[$data_row['type']]['data_load'];
            $question_data = null;
            ///$question_data_obj = $db->arrayBuilder()->where('id',$data_row['question_id'])->getOne(T_QUESTIONS,array('*'));
            $question_data_obj = $db->where('id', $data_row['question_id'])->getOne(T_QUESTIONS);
            if(!empty($question_data_obj)){
                //$qd = ToObject($question_data_obj);
                $question_data = QuestionData($question_data_obj, true);
            }
            $data['notifications'][] = [
                'ID' => $data_row['id'],
                'USER_DATA' => $data_row['notifier'],
                'QUESTION_DATA' => $question_data,
                'TITLE' => $title,
                'TEXT' => $data_row['text'],
                'TYPE' => $data_row['type'],
                'URL' => UrlLink($data_row['url']),
                'TIME' => Time_Elapsed_String($data_row['time']),
                'SEEN' => $data_row['seen'],
                'ICON' => $icon,
                'ACTION' => strtolower($action),
                'DATA_LOAD' => str_replace('{{ID}}',$data_row['question_id'],$data_load)
            ];
            $update[] = $data_row['id'];
            if (empty($data_row['seen'])) {
                $new++;
            }
        }
        //if (!empty($show_all)) {
        $db->where('recipient_id', $ask->user->id)->update($t_notif,array('seen' => time()));
        $data['count_messages'] = $db->where('to_id', $user->id)->where('seen', 0)->getValue(T_MESSAGES, "COUNT(*)");
    }else{
        $data['status'] = 304;
    }
}
?>