<?php

if ($option == 'mention' || $option == 'general' || $option == 'profile' || $option == 'password' || $option == 'delete' || $option == 'mention' || $option == 'save_user_location' || $option == 'update-notifications' || $option == 'update_two_factor') {
  
    if (empty($_POST['user_id']) || !is_numeric($_POST['user_id']) || $_POST['user_id'] == 0) {
        $errors[] = "Invalid user ID";
    } else {
        $userData = UserData($_POST['user_id']);
    }
}


if ($option == 'get-following-byid') {
    if (empty($_POST['user_id'])){
        $errors[] = "Please check your details";
    } else {
        $userData = UserData($_POST['user_id']);
        if (!empty ($userData->id) && empty($errors)) {
            $offset            = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
            $limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;

            if ($offset > 0) {
                $db->where('id' , $offset , '>');
            }
            $following = $db
                            ->where('follower_id', $userData->id)
                            ->get(T_FOLLOWERS, $limit, array('*'));
            $following_data = [];
            foreach($following as $key => $val){
                $following_data[] = [
                    'userData' => userData($val->user_id),
                    'time' => $val->time
                ];
            }
            $data = [
                'status' => 200,
                'data' => $following_data
            ];

        }else{
            $data = [
                'status'  => '400',
                'error' => 'User not found'
            ];
        }
    }
}

if ($option == 'get-followers-byid') {
    if (empty($_POST['user_id'])){
        $errors[] = "Please check your details";
    } else {
        $userData = UserData($_POST['user_id']);
        if (!empty ($userData->id) && empty($errors)) {
            $offset            = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
            $limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;

            if ($offset > 0) {
                $db->where('id' , $offset , '>');
            }
            $follower = $db
                            ->where('user_id', $userData->id)
                            ->get(T_FOLLOWERS, $limit, array('*'));
            $follower_data = [];
            foreach($follower as $key => $val){
                $follower_data[] = [
                    'userData' => userData($val->follower_id),
                    'time' => $val->time
                ];
            }
            $data = [
                'status' => 200,
                'data' => $follower_data
            ];

        }else{
            $data = [
                'status'  => '400',
                'error' => 'User not found'
            ];
        }
    }
}

if ($option == 'get_user_profile_by_id') {
    if (empty($_POST['user_id'])){
        $errors[] = "Please check your details";
    } else {
        $userData = UserData($_POST['user_id']);
    }
    if (!empty ($userData->id) && empty($errors)) {
        $data = [
            'status' => 200,
            'data' => $userData
        ];
    }else{
        $data = [
            'status'  => '400',
            'error' => 'User not found'
        ];
    }
}
if ($option == 'avatar') {
       if (empty($_FILES)) {
            $errors[] = "Please check your details";
        }


        $userData = UserData($_POST['user_id']);
        $update_data = array();
      
        if (!empty($userData->id)) {
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
        if (IsAdmin() || $userData->id == $user->id) {
            if(isset($_POST['mode']) && $_POST['mode'] == 'step' ){
                $update_data['startup'] = 1;
            }

            $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
            if ($update) {
                $data = array(
                    'status' => 200,
                    'message' => "avatar uploaded successfully"
                );
                if(isset($update_data['avatar'])){
                    $data['avatar_url'] = GetMedia($update_data['avatar']);
                }
                if(isset($update_data['cover'])){
                    $data['cover_url'] = GetMedia($update_data['cover']);
                }
            }
        } 
        else {
            $data = array(
                'status' => 400,
                'error' => "is_owner not true"
            );
     }
}
if ($option == 'profile') {
        if (empty($_POST['user_id'])){
                $errors[] = "Please check your details";
        } else {

               $userData = UserData($_POST['user_id']);
        }
      
            if (!empty ($userData->id)){

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

                if (IsAdmin() || $userData->id == $user->id) {
                    $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                    if ($update) {
                        $data = [
                            'status' => 200,
                            'message' => "Profile successfully updated!"
                        ];
                    }
                }
            }
          }
}
if ($option == 'password') {
        if (!empty($userData->id)){
         if ( empty($_POST['current_password']) ) {
            $errors[] = "Enter Current Password";
        }
        else if ( empty($_POST['new_password']) || empty($_POST['confirm_new_password'])) {
            $errors[] = "please check details";
        } else {
            if ( !IsAdmin() ) {
                if ($userData->password != sha1($_POST['current_password'])) {
                    $errors[] = "current password dont match";
                }
            }
            if (strlen($_POST['new_password']) < 4) {
                $errors[] = "password is short";
            }
            if ($_POST['new_password'] != $_POST['confirm_new_password']) {
                $errors[] = "new password dont match";
            }
            if (empty($errors)) {
                $update_data = array(
                    'password' => sha1($_POST['new_password'])
                );
                if (isAdmin() || $userData->id == $user->id) {
                    $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                    if ($update) {
                        $data = array(
                            'status' => 200,
                            'message' => 'Your password was succesifully updated'
                        );
                    }
                }
            }
         }
      }
    } 
if ($option == 'general') {
        if (empty($_POST['username']) || empty($_POST['email'])) {
            $errors[] = "Please check your details";
        } else {
            $username          = Secure($_POST['username']);
            $email             = Secure($_POST['email']);
            if (UsernameExists($_POST['username']) && $_POST['username'] != $userData->username) {
                $errors[] = "This username is already taken";
            }
            if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
                $errors[] = "Username length must be between 5 / 32";
            }
            if (!preg_match('/^[\w]+$/', $_POST['username'])) {
                $errors[] = "Invalid username characters";
            }
            if (UserEmailExists($_POST['email']) && $_POST['email'] != $userData->email) {
                $errors[] = "This e-mail is already taken";
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "This e-mail is invalid";
            }
            $country = $userData->country_id;
            if (in_array($_POST['country'], array_keys($countries_name))) {
                $country = Secure($_POST['country']);
            }

            $gender = $userData->gender;
            if (in_array($_POST['gender'], ['male', 'female'])) {
                $gender = Secure($_POST['gender']);
            }
          $active = $userData->active;
            if (!empty($_POST['activation']) && IsAdmin()) {
                if ($_POST['activation'] == '1') {
                    $active = 1;
                } else {
                    $active = 2;
                }
                if ($active == $userData->active) {
                    $active = $userData->active;
                }
            }
            $type = $userData->admin;

            if (!empty($_POST['type']) && IsAdmin()) {
                if ($_POST['type'] == '2') {
                    $type = 1;
                }

                else if ($_POST['type'] == '1') {
                    $type = 0;
                }
                if ($type == $userData->admin) {
                    $type = $userData->admin;
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
               
                if (!empty($_POST['verified'])) {
                    if ($_POST['verified'] == 'verified') {
                        $verification = 1;
                    } else {
                        $verification = 0;
                    }
                    if ($verification == $userData->verified) {
                        $verification = $userData->verified;
                    }

                    $update_data['verified'] = $verification;
                }
                if (isAdmin() || $userData->id == $user->id) {

                    $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                    if ($update) {
                        $data = [
                            'status' => 200,
                            'message' => "Settings successfully updated!"
                        ];
                    } else {
                        $errors[] = "update failed";
                    }
                }
            }
        }
    }
if ($option == 'mention') {
    $data = GetFollowingSug(5, Secure($_POST['term']));
    if(!empty($data)){
        $data = array(
            'status' => 200,
            'data' => $data
        );
    }else{
        $data = array(
            'status' => 400,
            'error'         => 'Mention not found'
        );
    }
}
if ($option == 'save_user_location' && isset($_POST['lat']) && isset($_POST['lng'])) {
        $lat          = Secure($_POST['lat']);
        $lng          = Secure($_POST['lng']);
        $update_array = array(
            'lat' => (is_numeric($lat)) ? $lat : 0,
            'lng' => (is_numeric($lng)) ? $lng : 0,
            'last_location_update' => (strtotime("+1 week"))
        );

        if (!UpdateUserData($userData->id, $update_array)) {
            $data['status'] = 200;
            $data['message'] = "your locataed was successfully updated";
        }else{
            $data       = array(
                'status'     => '400',
                'error'         => 'Error while updating user location in database, check user id and be sure you send lat and lng in float format'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
if ($option == 'update-notifications') {
        if (!empty($userData->id)) {
            $notifications_array = [
                'notification_on_answered_question',
                'notification_on_visit_profile',
                'notification_on_like_question',
                'notification_on_shared_question'
            ];
            $update_data = [];
            if(isset($_POST['notifications'])) {
                if (isset($_POST['notifications']['notification_on_answered_question'])) {
                    $update_data['notification_on_answered_question'] = (int)secure($_POST['notifications']['notification_on_answered_question']);
                }
                if (isset($_POST['notifications']['notification_on_visit_profile'])) {
                    $update_data['notification_on_visit_profile'] = (int)secure($_POST['notifications']['notification_on_visit_profile']);
                }
                if (isset($_POST['notifications']['notification_on_like_question'])) {
                    $update_data['notification_on_like_question'] = (int)secure($_POST['notifications']['notification_on_like_question']);
                }
                if (isset($_POST['notifications']['notification_on_shared_question'])) {
                    $update_data['notification_on_shared_question'] = (int)secure($_POST['notifications']['notification_on_shared_question']);
                }
                if ($userData->id == $user->id) {
                    $update = $db->where('id', Secure($_POST['user_id']))->update(T_USERS, $update_data);
                    if ($update) {
                        $data = array(
                            'status' => 200,
                            'message' => "Notification settings updated"
                        );
                    }
                }
            }else{
                $data = [
                    'status'  => '400',
                    'error' => 'Notification not set'
                ];
            }
        }
    }
if ($option == 'update_two_factor') {
        $s = '';
         if (isset($_POST['s'])) {
                $s = Secure($_POST['s'], 0);
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
                    $errors[] = "Please check your details";
                }
                elseif (!empty($_POST['phone_number']) && ($ask->config->two_factor_type == 'both' || $ask->config->two_factor_type == 'phone') && $is_phone == false) {
                    $errors[] = "Phone number should be as this format: +90..";
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
                            'message' =>'We have sent you an email with the confirmation code'
                        );
                    }
                    else{
                        $data = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again later.',
                        );
                    }
                }
            }
         if ($s == 'disable') {
                        if ($_POST['two_factor'] != 'disable') {
                            $error = "Something went wrong, please try again later";
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
                                'message' => "two factor successfully disabled"
                            );
                     }
                }
         if ($s == 'verify') {
                    if (empty($_POST['code'])) {
                        $error = "Something went wrong, please try again later.";
                    }
                    else{
                        $confirm_code = $db->where('id', $user->id)->where('email_code', md5($_POST['code']))->getValue(T_USERS, 'count(*)');
                        $Update_data = array();
                        if (empty($confirm_code)) {
                            $error = "Wrong confirmation code";
                        }
                        if (empty($error)) {
                            $message = '';
                            if ($ask->config->two_factor_type == 'phone') {
                                $message = "Your phone number has been successfully verified";
                                if (!empty($_GET['setting'])) {
                                    $Update_data['phone_number'] = $userData->new_phone;
                                    $Update_data['new_phone'] = '';
                                }
                            }
                            if ($ask->config->two_factor_type == 'email') {
                                $message = "Your E-mail has been successfully verified";
                                if (!empty($_GET['setting'])) {
                                    $Update_data['email'] = $userData->new_email;
                                    $Update_data['new_email'] = '';
                                }
                            }
                            if ($ask->config->two_factor_type == 'both') {
                                $message = "Your phone number and E-mail have been successfully verified";
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
                  }     }
            }
         if (!empty($error)) {
                $data = array(
                    'status' => 400,
                    'error' => $error,
                );
          }
     
    }
if ($option == 'confirm_user_unusal_login') {

    if (!empty($_POST['confirm_code']) && !empty($_SESSION['code_id'])) {

        $confirm_code = $_POST['confirm_code'];
        $user_id = $_SESSION['code_id'];

        if (empty($_POST['confirm_code'])) {
            $errors = "Please check your details";
        } else if (empty($_SESSION['code_id'])) {
            $errors = "Error while activating your account";
        }
        $confirm_code = $db->where('id', $user_id)->where('email_code', md5($confirm_code))->getValue(T_USERS, 'count(*)');
        if (empty($confirm_code)) {
            $errors = "Wrong confirmation code";
        }
        if (empty($errors) && $confirm_code > 0) {

            unset($_SESSION['code_id']);

               $data     = array(
                    'status'   => '200',
                    'success_type' => 'two factor login',
                    'message'    => 'Code correct!'
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

    } else {

        $data    = array(
                    'status'  => '400',
                    'error' => 'Email code and Session ID should be empty'
                );

    }

}
if ($option == 'verification_request'){
    $error          = false;
    $user_id        = $ask->user->id;
    $request_exists = ($db->where('user_id',$user_id)->getValue(T_VERIF_REQUESTS,'count(*)'));
    $post           = (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['message']) || empty($_FILES['identity']) || $_FILES['identity']['tmp_name'] == '');
    if (!empty($request_exists)) {
        $error = $lang->submit_verif_request_error;
    } elseif ($post == true) {
        $error = $lang->please_check_details;
    } else {
        if (strlen($_POST['first_name']) < 4 || strlen($_POST['first_name']) > 32) {
            $error = $lang->username_characters_length;
        } else if (strlen($_POST['last_name']) > 32) {
            $error = $lang->ivalid_last_name;
        } else if (!file_exists($_FILES['identity']['tmp_name'])) {
            $error = $lang->ivalid_image_file;
        } else if (file_exists($_FILES["identity"]["tmp_name"])) {
            $image = getimagesize($_FILES["identity"]["tmp_name"]);
            if (!in_array($image[2], array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_BMP))) {
                $error = $lang->ivalid_id_file;
            }
        }
    }
    if (empty($error)) {
        $file_info = array(
            'file' => $_FILES['identity']['tmp_name'],
            'size' => $_FILES['identity']['size'],
            'name' => $_FILES['identity']['name'],
            'type' => $_FILES['identity']['type']
        );
        $upload          = ShareFile($file_info);
        $re_data         = array(
            'user_id'    => $user_id,
            'name'       => Secure($_POST['first_name']) . ' ' . Secure($_POST['last_name']),
            'message'    => Secure($_POST['message']),
            'time'       => time(),
            'media_file' => $upload['filename']
        );
        $insert = $db->insert(T_VERIF_REQUESTS,$re_data);
        if ($insert) {
            $data['status']  = 200;
            $data['message'] = __('verif_sent');
        } else {
            $data['status']  = 400;
            $data['error'] = $lang->unknown_error;
        }
    }else{
        $data = [
            'status'  => '400',
            'error' => $error
        ];
    }
}
?>