<?php

if ($option == 'general' || $option == 'profile' || $option == 'password' || $option == 'delete' || $option == 'mention' || $option == 'save_user_location' || $option == 'update-notifications' || $option == 'update_two_factor') {
  
    if (empty($_POST['user_id']) || !is_numeric($_POST['user_id']) || $_POST['user_id'] == 0) {
        $errors[] = "Invalid user ID";
    } else {
        $userData = UserData($_POST['user_id']);
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
     $data = GetFollowingSug(5, $_POST['term']);
    }
if ($option == 'save_user_location' && isset($_POST['lat']) && isset($_POST['lng'])) {
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
        if (UpdateUserData($userData->id, $update_array)) {
            $data['status'] = 200;
            $data['message'] = "your locataed was successfully updated";
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
            if(empty($_POST['notifications'])){
                foreach ($notifications_array as $key){
                    $update_data[$key] = 0;
                }
            }else{
                foreach ($notifications_array as $key){
                    $update_data[$key] = (int)in_array($key, $_POST['notifications']);
                }
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
            $data = array(
                'status' => 200,
                'message' => "confirmation code accepted"
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
                'error' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }
}
if ($option == 'people_suggestions'){
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
    $Users_data      = array();
    $user_id   = Secure($ask->user->id);
    $query_one = " SELECT `id` FROM " . T_USERS . " WHERE `active` = '1' AND `id` NOT IN (SELECT `user_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id}) AND `id` <> {$user_id} " . $offset_text;
    $query_one .= " ORDER BY RAND() " . $limit_text;
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $Users_data[] = UserData($fetched_data['id']);
    }
    $data = array(
        'status' => 200,
        'users' => $Users_data
    );
}
if ($option == 'trending'){
    $data = array(
        'status' => 200,
        'hashtags' => GetTrendingHashs('latest'),
        'searches' => $db->orderBy('time', 'DESC')->get(T_RECENT_SEARCH, $limit, array('id','keyword'))
    );
}
?>