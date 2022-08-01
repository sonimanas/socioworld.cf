<?php 
if ($option == 'login') {
	if (!empty($_POST)) {
	    if (empty($_POST['username']) || empty($_POST['password'])) {
	          $data       = array(
	            'status'     => '400',
	            'error'         => 'Bad Request, Invalid or missing parameter'
	        );
	    } else {

	    $username        = Secure($_POST['username']);
        $password        = Secure($_POST['password']);
        $password_hashed = sha1($password);
        $db->where("(username = ? or email = ?)", array(
            $username,
            $username
        ));

        $db->where("password", $password_hashed);
        $login = $db->getOne(T_USERS);

           if (empty($login)) {
	        	$errors[] = "Incorrect username or password";
	        } else if ($login->active == 0) {
	        	  $data       = array(
		            'status'     => '400',
		            'error'     =>  'Your account is not activated yet, please check your inbox for the activation link'
		        );
              }

	         if (TwoFactor($login->id) === false) {
                    $_SESSION['code_id'] = $login->id;
                    $data                = array(
                        'status' => 600,
                        'location' => UrlLink('unusual-login')
                    );
                    $phone               = 1;
                }
  
	        if (empty($errors)) {
	        	 createUserSession($login->id,'mobile');


                if (!empty($_POST['android_device_id'])) {
                    $device_id  = Secure($_POST['android_device_id']);
                    $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `android_device_id` = '{$device_id}' WHERE `id` = '{$user_id}'");
                }
                if (!empty($_POST['ios_device_id'])) {
                    $device_id  = Secure($_POST['ios_device_id']);
                    $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ios_device_id` = '{$device_id}' WHERE `id` = '{$user_id}'");
                }

	            $ask->loggedin = true;
	            $ask->user = UserData($login->id);
	            unset($ask->user->password);
	           
                $data = array(
		            'status' => 200,
		            'access_token' => $_SESSION['user_id'],
		            'data' => $ask->user
		        );
	        } else {
	        	$data    = array(
			        'status'  => '400',
			        'error' => 'Not logged in'
			    );
	        }
	    }
	}
}
if ($option == 'forgot-password') {
	if (!empty($_POST)) {
	    if (empty($_POST['email'])) {
	        $data       = array(
	            'status'     => '400',
	            'error'         => 'Bad Request, Invalid or missing parameter'
	        );

	    } else {
	    $email        = Secure($_POST['email']);
        $db->where("email", $email);
        $user_id = $db->getValue(T_USERS, "id");
        if (!empty($user_id)) {
	        $rest_user = UserData($user_id);
            $email_code = sha1(time() + rand(111,999));
            $update_data = array('email_code' => $email_code);
            $db->where('id', $rest_user->id);
            $update = $db->update(T_USERS, $update_data);
            $update_data['USER_DATA'] = $rest_user;
            $send_email_data = array(
                'from_email' => $ask->config->email,
                'from_name' => $ask->config->name,
                'to_email' => $email,
                'to_name' => $rest_user->name,
                'subject' => 'Reset Password',
                'charSet' => 'UTF-8',
                'message_body' =>  $update_data,
                'is_html' => true
            );
                $send_message = SendMessage($send_email_data);
	            if ($send_message) {
	            	$data = array(
			            'status' => 200,
			            'message' => "Please check your inbox / spam folder for the reset email."
			        );
	            } else {
	            	 $data       = array(
			            'status'     => '400',
			            'error'         => 'Error found while sending the reset link, please try again later'
			        );

		        }
            }
	    }
	}
}
if ($option == 'reset-password') {
	if (!empty($_POST)) {
	    if (empty($_POST['password']) || empty($_POST['c_password']) || empty($_POST['email_code'])) {
	        $data    = array(
		        'status'  => '400',
		        'error' => 'Invalid request  or Missing arguments'
		    );
	    } else {
	        $code            = Secure($_POST['code']);
		    $password        = Secure($_POST['password']);
		    $c_password      = Secure($_POST['c_password']);
		    $password_hashed = sha1($password);
	        if ($password != $c_password) {
	            $errors[] = "Passwords don't match";
	        } else if (strlen($password) < 4 || strlen($password) > 32) {
	            $errors[] = "Password is too short";
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
	        		createUserSession($user_id);
		            $data = [
		                'status' => 200,
                        'access_token' => $_SESSION['user_id']
                    ];
	        	}
            }
	    }
	}
}
if ($option == 'signup') {
	if (!empty($_POST)) {
	    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['c_password'])|| empty($_POST['gender'])) {
	        $errors[] = "Please check your details";
	    } else {
	        $username        = Secure($_POST['username']);
		    $password        = Secure($_POST['password']);
		    $c_password      = Secure($_POST['c_password']);
		    $password_hashed = sha1($password);
	        $email           = secure($_POST['email']);
	        if (UsernameExists($_POST['username'])) {
	            $errors[] = "This username is already taken";
	        }
	        if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
	            $errors[] = "Username length must be between 5 / 32";
	        }
	        if (!preg_match('/^[\w]+$/', $_POST['username'])) {
	            $errors[] = "Invalid username characters";
	        }
	        if (UserEmailExists($_POST['email'])) {
	            $errors[] = "This e-mail is already taken";
	        }
	        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	            $errors[] = "This e-mail is invalid";
	        }
	        if ($password != $c_password) {
	            $errors[] = "Passwords don't match";
	        }
	        if (strlen($password) < 4) {
	            $errors[] = "Password is too short";
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
	            'registered' => date('Y') . '/' . intval(date('m'))
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
	                        'subject' => "Confirm your account",
	                        'charSet' => 'UTF-8',
	                        'message_body' => LoadPage('emails/confirm-account', $data),
	                        'is_html' => true
	                    );
	                    $send_message = SendMessage($send_email_data);
	                    $data = array(
				            'status' => 200,
                            'wait_validation' => 1,
                            'access_token' => $_SESSION['user_id'],
		                    'data' => $ask->user
				        );
	                } else {
	                	createUserSession($user_id,'mobile');
	                	$ask->loggedin = true;
	                    $ask->user = UserData($user_id);
                        unset($ask->user->password);
	                    $data = array(
				            'status' => 200,
                            'wait_validation' => 0,
                            'access_token' => $_SESSION['user_id'],
                            'data' => $ask->user
				        );
	                }
	            }
	        }
	    }
	
}
?>