<?php
if (IS_LOGGED == true) {
    header("Location: " . UrlLink(''));
    exit();
}

if (empty($_GET['code'])) {
    header("Location: " . UrlLink(''));
    exit();
}
$errors_final = '';
$code = Secure($_GET['code']);
$db->where('email_code', $code);
$user_id = $db->getValue(T_USERS, 'id');
$error_code = false;
if (empty($user_id)) {
    $error_code = true;
}
if ($error_code == true) {
    $errors_final .= $error_icon . __('email_code_not_found') . "<br>";
}
$ask->page        = 'login';
$ask->title       = __('change_password') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword     = $ask->config->keyword;
$page = 'content';
if ($error_code == true) {
    $page = 'invalid';
}
$ask->content     = LoadPage('auth/reset-password/' . $page, array(
    'ERRORS' => $errors_final,
    'CODE' => $code
));