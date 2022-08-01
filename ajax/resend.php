<?php
$data['status'] = 400;
if (IS_LOGGED == true && empty($_GET['id'])) {
    $data['message'] = $error_icon . __('you_already_loggedin');
}

if (empty($_GET['id']) || empty($_GET['u_id'])) {
    $data['message'] = $error_icon . __('please_check_details');
}

$email_code = Secure($_GET['id']);
$username = Secure($_GET['u_id']);

$check_for_code = $db->where('username', $username)->where('email_code', $email_code)->getOne(T_USERS);

if (empty($check_for_code)) {
    $data['message'] = $error_icon . __('please_check_details');
}
$email_code = sha1(time() + rand(111,999));
$db->where('username', $username)->update(T_USERS, array('email_code' => $email_code));
$link = $email_code . '/' . $check_for_code->email;
$data['EMAIL_CODE'] = $link;
$data['USERNAME'] = $username;
$send_email_data = array(
    'from_email' => $ask->config->email,
    'from_name' => $ask->config->name,
    'to_email' => $check_for_code->email,
    'to_name' => $username,
    'subject' => 'Confirm your account',
    'charSet' => 'UTF-8',
    'message_body' => LoadPage('emails/confirm-account', $data),
    'is_html' => true
);

$send_message = SendMessage($send_email_data);
if($send_message) {
    $data = array(
        'status' => 200,
        'message' => $success_icon . __('successfully_resend_desc')
    );
}else{
    $data['message'] = $error_icon . __('error_while_send_confirmation_email');
}