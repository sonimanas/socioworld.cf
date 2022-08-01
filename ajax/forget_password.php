<?php

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$data['status'] = 400;
if (IS_LOGGED == true) {
    $data['message'] = $error_icon . __('you_already_loggedin');
}

if (!empty($_POST)) {
    if(empty($_POST['email']) || empty($_POST['g-recaptcha-response'])){
        if(empty($_POST['email'])){
            $data['message'] = $error_icon . __('please_check_details');
        }

        else if (empty($_POST['g-recaptcha-response'])) {
            $data['message'] = $error_icon . __('reCaptcha_error');
        }

        else{
            $recaptcha_data = array(
                'secret' => $ask->config->recaptcha_key,
                'response' => $_POST['g-recaptcha-response']
            );
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($recaptcha_data));
            curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);
            $response = json_decode($response);
            if (!$response->success) {
                $data['message'] = $error_icon . __('reCaptcha_error');
            }
        }

    }

    else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $data['message'] = $error_icon . __('email_invalid_characters');
    }

    else {
        $email        = Secure($_POST['email']);
        $db->where("email", $email);
        $user_id = $db->getValue(T_USERS, "id");
        if (!empty($user_id)) {
            $rest_user = UserData($user_id);
            $email_code = generateRandomString(40);
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
                'message_body' => LoadPage('emails/reset-password', $update_data),
                'is_html' => true
            );
            $send_message = SendMessage($send_email_data);
            if ($send_message) {
                $data['status'] = 200;
                $data['message'] = $success_icon . __('email_sent');
            }else{
                $data['message'] = $error_icon . __('error_while_send_confirmation_email');
            }
        } else {
            $data['message'] = $error_icon . __('email_not_exist');
        }
    }

}

else{
    $data['message'] = $error_icon . __('please_check_details');
}


