<?php

$is_owner = false;
if (empty($_REQUEST['user_id'])) {
    exit();
}
if ($_REQUEST['user_id'] == $user->id || IsAdmin()) {
    $is_owner = true;
}

if ($first == 'general') {
    if (empty($_POST['username']) OR empty($_POST['email'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else {
        $user_data = UserData($_POST['user_id']);
        if (!empty($user_data->id)) {
            if ($_POST['email'] != $user_data->email) {
                if (UserEmailExists($_POST['email'])) {
                    $errors[] = $error_icon . __('email_exists');
                }
            }
            if ($_POST['username'] != $user_data->username) {
                $is_exist = UsernameExists($_POST['username']);
                if ($is_exist) {
                    $errors[] = $error_icon . __('username_is_taken');
                }
            }
            if (in_array($_POST['username'], $ask->site_pages)) {
                $errors[] = $error_icon . __('username_invalid_characters');
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = $error_icon . __('email_invalid_characters');
            }
            if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
                $errors[] = $error_icon . __('username_characters_length');
            }
            if (!preg_match('/^[\w]+$/', $_POST['username'])) {
                $errors[] = $error_icon . __('username_invalid_characters');
            }
            $active = $user_data->active;
            if (!empty($_POST['activation']) && IsAdmin()) {
                if ($_POST['activation'] == '1') {
                    $active = 1;
                } else {
                    $active = 2;
                }
                if ($active == $user_data->active) {
                    $active = $user_data->active;
                }
            }
            $type = $user_data->admin;
            if (!empty($_POST['type']) && IsAdmin()) {
                if ($_POST['type'] == '2') {
                    $type = 1;
                }

                else if ($_POST['type'] == '1') {
                    $type = 0;
                }
                if ($type == $user_data->admin) {
                    $type = $user_data->admin;
                }
            }
            $gender       = 'male';
            $gender_array = array(
                'male',
                'female'
            );
            if (!empty($_POST['gender'])) {
                if (in_array($_POST['gender'], $gender_array)) {
                    $gender = $_POST['gender'];
                }
            }
            if (empty($errors)) {
                $update_data = array(
                    'first_name' => Secure($_POST['first_name']),
                    'last_name' => Secure($_POST['last_name']),
                    'birth_date' => Secure($_POST['birth_date']),
                    'username' => Secure($_POST['username']),
                    'email' => Secure($_POST['email']),
                    'gender' => Secure($gender),
                    'country_id' => Secure($_POST['country']),
                    'active' => Secure($active),
                    'admin' => Secure($type)
                );

                  $limit_array = array('0','2000000','6000000','12000000','24000000','48000000','96000000','256000000','512000000','1000000000','10000000000','unlimited');
                if (isset($_POST['user_upload_limit']) && IsAdmin()) {
                    if (in_array($_POST['user_upload_limit'], $limit_array)) {
                        $update_data['user_upload_limit'] = Secure($_POST['user_upload_limit']);
                    } 
                }
                
                if (!empty($_POST['verified'])) {
                    if ($_POST['verified'] == 'verified') {
                        $verification = 1;
                    } else {
                        $verification = 0;
                    }
                    if ($verification == $user_data->verified) {
                        $verification = $user_data->verified;
                    }
                    $update_data['verified'] = $verification;
                }
                if ($is_owner == true) {
                    $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                    if ($update){
                        $data = array(
                            'status' => 200,
                            'message' => $success_icon . __('setting_updated')
                        );
                    }
                }
            }
        }
    }
}

if ($first == 'profile') {
    $user_data = UserData($_POST['user_id']);
    if (!empty($user_data->id)) {
        if (empty($errors)) {
            $update_data = array(
                'about' => Secure($_POST['about']),
                'location' => Secure($_POST['location']),
                'website' => Secure($_POST['website']),
                'facebook' => Secure($_POST['facebook']),
                'google' => Secure($_POST['google']),
                'twitter' => Secure($_POST['twitter']),
                'instagram' => Secure($_POST['instagram'])
            );
            if ($is_owner == true) {
                $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                if ($update) {
                    $data = array(
                        'status' => 200,
                        'message' => $success_icon . __('setting_updated')
                    );
                }
            }
        }
    }
}

if ($first == 'change-pass') {
    $user_data = UserData($_POST['user_id']);
    if (!empty($user_data->id)) {
        if ( !IsAdmin() && empty($_POST['current_password']) ) {
            $errors[] = $error_icon . __('please_check_details');
        }
        else if ( empty($_POST['new_password']) || empty($_POST['confirm_new_password'])) {
            $errors[] = $error_icon . __('please_check_details');
        } else {
            if ( !IsAdmin() ) {
                if ($user_data->password != sha1($_POST['current_password'])) {
                    $errors[] = $error_icon . __('current_password_dont_match');
                }
            }
            if (strlen($_POST['new_password']) < 4) {
                $errors[] = $error_icon . __('password_is_short');
            }
            if ($_POST['new_password'] != $_POST['confirm_new_password']) {
                $errors[] = $error_icon . __('new_password_dont_match');
            }
            if (empty($errors)) {
                $update_data = array(
                    'password' => sha1($_POST['new_password'])
                );
                if ($is_owner == true || IsAdmin()) {
                    $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                    if ($update) {
                        $data = array(
                            'status' => 200,
                            'message' => $success_icon . __('setting_updated')
                        );
                    }
                }
            }
        }
    }
}

if ($first == 'delete' && $ask->config->delete_account == 'on') {
    $user_data = UserData($_POST['user_id']);
    if (!empty($user_data->id)) {
        if ($user_data->password != sha1($_POST['current_password'])) {
            $errors[] = $error_icon . __('current_password_dont_match');
        }
        if (empty($errors) && $is_owner == true) {
            $delete = DeleteUser($user_data->id);
            if ($delete) {
                $data = array(
                    'status' => 200,
                    'message' => $success_icon . __('your_account_was_deleted'),
                    'url' => UrlLink('/logout')
                );
            }
        }
    }
}

if ($first == 'update-notifications') {
    $user_data = UserData($_POST['user_id']);
    if (!empty($user_data->id)) {
        $notifications_array = [
            'notification_on_answered_question',
            'notification_on_visit_profile',
            'notification_on_like_question',
            'notification_on_shared_question'
        ];
        $update_data = [];
        if(empty($_POST['notifications'])){
            foreach ($notifications_array as $key){
                $update_data[$key] = 0;
            }
        }else{
            foreach ($notifications_array as $key){
                $update_data[$key] = (int)in_array($key, $_POST['notifications']);
            }
        }
        if ($is_owner == true) {
            $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
            if ($update) {
                $data = array(
                    'status' => 200,
                    'message' => $success_icon . __('setting_updated')
                );
            }
        }
    }
}

if ($first == 'step_info') {
    if (empty($_POST['user_id'])) {
        $errors[] = $error_icon . __('please_check_details');
    }

    else{
        $user_data = UserData($_POST['user_id']);
        if (!empty($user_data->id)) {
            $gender       = 'male';
            $gender_array = array(
                'male',
                'female'
            );
            if (!empty($_POST['gender'])) {
                if (in_array($_POST['gender'], $gender_array)) {
                    $gender = $_POST['gender'];
                }
            }
            $update_data = array(
                'first_name' => Secure($_POST['first_name']),
                'last_name' => Secure($_POST['last_name']),
                'gender' => Secure($gender),
                'country_id' => Secure($_POST['country']),
                'startup' => 2
            );
            if ($is_owner == true) {
                $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                if ($update){
                    $data = array(
                        'status' => 200,
                        'message' => $success_icon . __('setting_updated')
                    );
                }
            }
        }
    }
}

if ($first == 'avatar') {
    $user_data = UserData($_POST['user_id']);
    $update_data = array();
    if (!empty($user_data->id)) {
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $file_info = array(
                'file' => $_FILES['avatar']['tmp_name'],
                'size' => $_FILES['avatar']['size'],
                'name' => $_FILES['avatar']['name'],
                'type' => $_FILES['avatar']['type'],
                'crop' => array('width' => 400, 'height' => 400),
                'mode' => 'avatar'
            );
            $file_upload = ShareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $update_data['avatar'] = $file_upload['filename'];
            }
        }
        if (!empty($_FILES['cover']['tmp_name'])) {
            $file_info = array(
                'file' => $_FILES['cover']['tmp_name'],
                'size' => $_FILES['cover']['size'],
                'name' => $_FILES['cover']['name'],
                'type' => $_FILES['cover']['type'],
                'crop' => array('width' => 1000, 'height' => 550)
            );
            $file_upload = ShareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $update_data['cover'] = $file_upload['filename'];
            }
        }
    }
    if ($is_owner == true) {
        if(isset($_POST['mode']) && $_POST['mode'] == 'step' ){
            $update_data['startup'] = 1;
        }
        $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
        if ($update) {
            $data = array(
                'status' => 200,
                'message' => $success_icon . __('avatar_uploaded_successfully')
            );
            if(isset($update_data['avatar'])){
                $data['avatar_url'] = GetMedia($update_data['avatar']);
            }
            if(isset($update_data['cover'])){
                $data['cover_url'] = GetMedia($update_data['cover']);
            }
        }
    }
}

if ($first == 'mention') {
    $data = GetFollowingSug(5, $_GET['term']);
}

if ($first == 'save_user_location' && isset($_POST['lat']) && isset($_POST['lng'])) {
    $lat          = Secure($_POST['lat']);
    $lng          = Secure($_POST['lng']);
    $update_array = array(
        'lat' => (is_numeric($lat)) ? $lat : 0,
        'lng' => (is_numeric($lng)) ? $lng : 0,
        'last_location_update' => (strtotime("+1 week"))
    );
    $data         = array(
        'status' => 304
    );
    if (UpdateUserData($user->id, $update_array)) {
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($first == 'update_two_factor') {
    $s = '';
    if (isset($_GET['s'])) {
        $s = Secure($_GET['s'], 0);
    }
    $error = '';

    if ($s == 'enable') {
        $is_phone = false;
        if (!empty($_POST['phone_number']) && ($ask->config->two_factor_type == 'both' || $ask->config->two_factor_type == 'phone')) {
            preg_match_all('/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|
                            2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|
                            4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/', $_POST['phone_number'], $matches);
            if (!empty($matches[1][0]) && !empty($matches[0][0])) {
                $is_phone = true;
            }
        }
        if ((empty($_POST['phone_number']) && $ask->config->two_factor_type == 'phone') || empty($_POST['two_factor']) || $_POST['two_factor'] != 'enable') {
            $error = lang('Please check your details.');
        }
        elseif (!empty($_POST['phone_number']) && ($ask->config->two_factor_type == 'both' || $ask->config->two_factor_type == 'phone') && $is_phone == false) {
            $error = lang('Phone number should be as this format: +90..');
        }

        if (empty($error)) {
            $code = rand(111111, 999999);
            $hash_code = md5($code);
            $message = "Your confirmation code is: $code";
            $phone_sent = false;
            $email_sent = false;
            if (!empty($_POST['phone_number']) && ($ask->config->two_factor_type == 'both' || $ask->config->two_factor_type == 'phone')) {
                $send = SendSMSMessage($_POST['phone_number'], $message);
                if ($send) {
                    $phone_sent = true;
                    $Update_data = array(
                        'phone_number' => secure($_POST['phone_number'])
                    );
                    $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
                }
            }
            if ($ask->config->two_factor_type == 'both' || $ask->config->two_factor_type == 'email') {
                $send_message_data       = array(
                    'from_email' => $ask->config->email,
                    'from_name' => $ask->config->name,
                    'to_email' => $ask->user->email,
                    'to_name' => $ask->user->name,
                    'subject' => 'Please verify that it’s you',
                    'charSet' => 'utf-8',
                    'message_body' => $message,
                    'is_html' => true
                );
                $send = SendMessage($send_message_data);
                if ($send) {
                    $email_sent = true;
                }
            }
            if ($email_sent == true || $phone_sent == true) {
                $Update_data = array(
                    'two_factor' => 0,
                    'two_factor_verified' => 0,
                    'email_code' => $hash_code
                );
                $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
                $data = array(
                    'status' => 200,
                    'message' => lang('We have sent you an email with the confirmation code.')
                );
            }
            else{
                $data = array(
                    'status' => 400,
                    'message' => lang('Something went wrong, please try again later.'),
                );
            }
        }
    }

    if ($s == 'disable') {
        if ($_POST['two_factor'] != 'disable') {
            $error = lang('Something went wrong, please try again later.');
            $data = array(
                'status' => 400,
                'message' => $error,
            );
        } else {
            $Update_data = array(
                'two_factor' => 0,
                'two_factor_verified' => 0
            );
            $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
                        $data = array(
                'status' => 200,
                'message' => lang("Settings successfully updated!")
            );
        }
    }

    if ($s == 'verify') {
        if (empty($_POST['code'])) {
            $error = lang('Something went wrong, please try again later.');
        }
        else{
            $confirm_code = $db->where('id', $user->id)->where('email_code', md5($_POST['code']))->getValue(T_USERS, 'count(*)');
            $Update_data = array();
            if (empty($confirm_code)) {
                $error = lang('Wrong confirmation code.');
            }
            if (empty($error)) {
                $message = '';
                if ($ask->config->two_factor_type == 'phone') {
                    $message = lang('Your phone number has been successfully verified.');
                    if (!empty($_GET['setting'])) {
                        $Update_data['phone_number'] = $userData->new_phone;
                        $Update_data['new_phone'] = '';
                    }
                }
                if ($ask->config->two_factor_type == 'email') {
                    $message = lang('Your E-mail has been successfully verified.');
                    if (!empty($_GET['setting'])) {
                        $Update_data['email'] = $userData->new_email;
                        $Update_data['new_email'] = '';
                    }
                }
                if ($ask->config->two_factor_type == 'both') {
                    $message = lang('Your phone number and E-mail have been successfully verified.');
                    if (!empty($_GET['setting'])) {
                        if (!empty($userData->new_email)) {
                            $Update_data['email'] = $userData->new_email;
                            $Update_data['new_email'] = '';
                        }
                        if (!empty($userData->new_phone)) {
                            $Update_data['phone_number'] = $userData->new_phone;
                            $Update_data['new_phone'] = '';
                        }
                    }
                }
                $Update_data['two_factor_verified'] = 1;
                $Update_data['two_factor'] = 1;
                $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
                $data = array(
                    'status' => 200,
                    'message' => $message,
                );
            }
        }
        if (!empty($error)) {
            $data = array(
                'status' => 400,
                'message' => $error,
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($first == 'confirm_user_unusal_login') {
    if (!empty($_POST['confirm_code']) && !empty($_SESSION['code_id'])) {
        $confirm_code = $_POST['confirm_code'];
        $user_id = $_SESSION['code_id'];
        if (empty($_POST['confirm_code'])) {
            $errors = lang('Please check your details.');
        } else if (empty($_SESSION['code_id'])) {
            $errors = lang('Error while activating your account.');
        }
        $confirm_code = $db->where('id', $user_id)->where('email_code', md5($confirm_code))->getValue(T_USERS, 'count(*)');
        if (empty($confirm_code)) {
            $errors = lang('Wrong confirmation code.');
        }
        if (empty($errors) && $confirm_code > 0) {
            unset($_SESSION['code_id']);
            $data = array(
                'status' => 200
            );
            if (!empty($_SESSION['last_login_data'])) {
                $update_user = $db->where('id', $user_id)->update(T_USERS, array('last_login_data' => serialize($_SESSION['last_login_data'])));
            } else if (!empty(get_ip_address())) {
                $getIpInfo = fetchDataFromURL("http://ip-api.com/json/" . get_ip_address());
                $getIpInfo = json_decode($getIpInfo, true);
                if ($getIpInfo['status'] == 'success' && !empty($getIpInfo['regionName']) && !empty($getIpInfo['countryCode']) && !empty($getIpInfo['timezone']) && !empty($getIpInfo['city'])) {
                    $update_user = $db->where('id', $user_id)->update(T_USERS, array('last_login_data' => serialize($getIpInfo)));

                }
            }
            $session = createUserSession($user_id);
            $_SESSION['user_id'] = $session;
            if (isset($_SESSION['last_login_data'])) {
                unset($_SESSION['last_login_data']);
            }
            if (!empty($_POST['last_url'])) {
                $data['location'] = $_POST['last_url'];
            } else {
                $data['location'] = $ask->config->site_url;
            }
        }
        header("Content-type: application/json");
        if (!empty($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }
}
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}

header("Content-type: application/json");
if (isset($errors)) {
    echo json_encode(array(
        'errors' => $errors
    ));
    exit();
}
