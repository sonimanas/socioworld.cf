<?php

$vl1 = (empty($_POST['first_name']) || empty($_POST['last_name']));
$vl2 = (empty($_POST['email']) || empty($_POST['message']));
$vl3 = ($vl1 || $vl2);

$data['status'] = 400;

if ($vl3 === true) {
    $data['message'] = $error_icon . __('please_check_details');
}

else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $data['message'] = $error_icon . __('email_invalid_characters');
}

else{

    $first_name        = Secure($_POST['first_name']);
    $last_name         = Secure($_POST['last_name']);
    $email             = Secure($_POST['email']);
    $message           = Secure($_POST['message']);
    $name              = $first_name . ' ' . $last_name;

    $send_message_data = array(
        'from_email' => $ask->config->email,
        'from_name' => $name,
        'reply-to' => $email,
        'to_email' => $ask->config->email,
        'to_name' => $ask->config->name,
        'subject' => 'Contact us new message',
        'charSet' => 'utf-8',
        'message_body' => $message,
        'is_html' => false
    );

    $send = SendMessage($send_message_data);
    if ($send) {
        $data = array(
            'status' => 200,
            'message' => $success_icon . __('email_sent')
        );
    }

    else {
        $data['message'] = $error_icon . __('error_msg');
    }
}
