<?php
$errors   = '';
$username = '';
$phone = '';
if (!empty($_POST)) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $data = array(
            'status' => 200,
            'session_id' => $error_icon . __('please_check_details'),
        );
    }
    else {
        $username        = Secure($_POST['username']);
        $password        = Secure($_POST['password']);
        $password_hashed = sha1($password);
        $db->where("(username = ? or email = ?)", array(
            $username,
            $username
        ));

        $db->where("password", $password_hashed);
        $login = $db->getOne(T_USERS);
        if (!empty($login)) {
            if ($login->active == 0) {                
                $data = array(
                       'status' => 400,
                       'message' => $error_icon . __('account_is_not_active') . ' <a href="#" data-email-code="'.$login->email_code.'" data-username="'.$login->username.'" id="resend_confirmation_email">' . __('resend_email') . '</a>'
                );
            }
            else{           
            if (TwoFactor($login->id) === false) {
                    $_SESSION['code_id']       = $login->id;
                    $data = array(
                        'status'   => 600,
                        'location' => UrlLink('unusual-login')
                    );
                    $phone               = 1;
                }              
            if (empty($errors) && $phone == 0) {
                $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime());
                $insert_data         = array(
                    'user_id' => $login->id,
                    'session_id' => $session_id,
                    'time' => time()
                );
                $insert              = $db->insert(T_SESSIONS, $insert_data);
                $db->where('id',$login->id)->update(T_USERS,array(
                    'ip_address' => get_ip_address()
                ));
                $ask->loggedin = true;             
                $data = array(
                    'status' => 200,
                    'session_id' => $session_id,
                );
              }
            }

            }
            else {
                    $data = array(
                        'status' => 400,
                        'message' => $error_icon . __('invalid_username_or_password')
                    );
                }
    

}}