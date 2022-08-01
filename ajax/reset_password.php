<?php
$data['status'] = 400;
if (IS_LOGGED == true) {
    $data['message'] = $error_icon . __('you_already_loggedin');
}

$post_check = (empty($_POST['code']) || empty($_POST['password']) || empty($_POST['c_password']) );
if ($post_check === true) {
    $data['message'] = $error_icon . __('please_check_details');
}

else{
    $code            = Secure($_POST['code']);
    $password        = Secure($_POST['password']);
    $c_password      = Secure($_POST['c_password']);
    $password_hashed = sha1($password);
    if ($password != $c_password) {
        $errors[] = __('password_not_match');
    } else if (strlen($password) < 4 || strlen($password) > 32) {
        $errors[] = __('password_is_short');
    }
    if (empty($errors)) {
        $email_code = sha1(time() + rand(111,999));
        $db->where('email_code', $code);
        $user_id = $db->getValue(T_USERS, 'id');

        $db->where('id', $user_id);
        $db->where('email_code', $code);
        $update_data = array('password' => $password_hashed, 'email_code' => $email_code);
        $update = $db->update(T_USERS, $update_data);
        if ($update) {
            $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime());
            $insert_data         = array(
                'user_id' => $user_id,
                'session_id' => $session_id,
                'time' => time()
            );
            $insert              = $db->insert(T_SESSIONS, $insert_data);
            $data = array(
                'status' => 200,
                'session_id' => $session_id
            );
        }else{
            $data['message'] = $error_icon . __('no_user_found_with_this_data');
        }
    }else{
        $errors_text = '';
        foreach ($errors as $key => $value) {
            $errors_text .= $error_icon . $value . "<br>";
        }
        $data['message'] = $errors_text;
    }
}