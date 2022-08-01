<?php
$color1      = '2ec0bc';
$color2      = '8ef9f6';
$errors      = array();
$erros_final = '';
$username    = '';
$email       = '';
$success     = '';
$defaulGender = 'selectGender';


$post_check = (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['c_password']) || (empty($_POST['gender'])) );
$data['status'] = 400;

if ($post_check === true) {
    $data['message'] = $error_icon . __('please_check_details');
}

else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $data['message'] = $error_icon . __('email_invalid_characters');
}

else{
    $username        = Secure($_POST['username']);
    $password        = Secure($_POST['password']);
    $c_password      = Secure($_POST['c_password']);
    $password_hashed = sha1($password);
	      
    $email           = Secure($_POST['email']);
    if (CheckIfUserCanRegister($ask->config->max_user_reg_hour) === false) {
        $errors[] = __('limit_exceeded');
    }
    if (UsernameExists($_POST['username'])) {
        $errors[] = __('username_is_taken');
    }
    if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
        $errors[] = __('username_characters_length');
    }
    if (!preg_match('/^[\w]+$/', $_POST['username'])) {
        $errors[] = __('username_invalid_characters');
    }
    if (UserEmailExists($_POST['email'])) {
        $errors[] = __('email_exists');
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = __('email_invalid_characters');
    }
    if ($password != $c_password) {
        $errors[] = __('password_not_match');
    }
    if (strlen($password) < 4) {
        $errors[] = __('password_is_short');
    }
	if ($defaulGender == ($_POST['gender'])) {
        $errors[] = __('please_check_details');
    }
    if ($ask->config->recaptcha == 'on') {
        if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
            $errors[] = __('reCaptcha_error');
        }
    }
    if (empty($_POST['terms'])) {
        $errors[] = __('terms_accept');
    } elseif ($_POST['terms'] != 'on') {
        $errors[] = __('terms_accept');
    }
    $active = ($ask->config->validation == 'on') ? 0 : 1;

    if (empty($errors)) {
        $email_code = sha1(time() + rand(111,999));
        $insert_data = array(
            'username' => $username,
            'password' => $password_hashed,
            'email' => $email,
            'ip_address' => get_ip_address(),
            'active' => $active,
            'email_code' => $email_code,
            'last_active' => time(),
            'registered' => date('Y') . '/' . intval(date('m')),
            'user_upload_limit' => 0
        );
        $insert_data['language'] = $ask->config->language;
        if (!empty($_SESSION['lang'])) {
            if (in_array($_SESSION['lang'], $langs)) {
                $insert_data['language'] = $_SESSION['lang'];
            }
        }
        $user_id = $db->insert(T_USERS, $insert_data);
      
		 $gender       = 'male';
            $gender_array = array( 'male', 'female');
            if (!empty($_POST['gender'])) {
                if (in_array($_POST['gender'], $gender_array)) {
                    $gender = $_POST['gender'];
                }
            }
            $update_data = array( 'gender' => Secure($gender) );
            $update = $db->where('id', $user_id)->update(T_USERS, $update_data);

             
            }
		
        if (!empty($user_id)) {
            if (!empty($ask->config->auto_friend_users)) {
                $autoFollow = AutoFollow($user_id);
            }
            if ($ask->config->validation == 'on') {
                $link = $email_code . '/' . $email;
                $data['EMAIL_CODE'] = $link;
                $data['USERNAME']   = $username;
                $send_email_data = array(
                    'from_email' => $ask->config->email,
                    'from_name' => $ask->config->name,
                    'to_email' => $email,
                    'to_name' => $username,
                    'subject' => 'Confirm your account',
                    'charSet' => 'UTF-8',
                    'message_body' => LoadPage('emails/confirm-account', $data),
                    'is_html' => true
                );
                $send_message = SendMessage($send_email_data);
                $data = array(
                    'status' => 200,
                    'mode' => 'wait_activate',
                    'message' => $success_icon . __('successfully_joined_desc')
                );
            }
            else {
                $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime());
                $insert_data         = array(
                    'user_id' => $user_id,
                    'session_id' => $session_id,
                    'time' => time()
                );
                $insert              = $db->insert(T_SESSIONS, $insert_data);
                $data = array(
                    'status' => 200,
                    'session_id' => $session_id,
                    'mode' => 'done',
                    'message' => $success_icon . __('successfully_joined_desc')
                );
            }
        }
    else{
        $errors_text = '';
        foreach ($errors as $key => $value) {
            $errors_text .= $error_icon . $value . "<br>";
        }
        $data['message'] = $errors_text;
    } }
