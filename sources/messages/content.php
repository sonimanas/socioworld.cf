<?php 
if (IS_LOGGED == false) {
    var_dump(IS_LOGGED);
    header("Location: " . UrlLink('login'));
    exit();
}

$ask->page = 'messages';
$ask->title = __('messages') . ' | ' . $ask->config->title;
$ask->description = $ask->config->description;
$ask->keyword = $ask->config->keyword;

$chat_id = 0;
$chat_user = array();

if (!empty($_GET['id'])) {
    $get_user_id = $db->where('username', Secure($_GET['id']))->getValue(T_USERS, 'id');
    if (!empty($get_user_id)) {
        $chat_user = UserData($get_user_id);
        if ($chat_user->id != $ask->user->id) {
            $chat_id = $chat_user->id;
        } else {
            $chat_user = array();
        }
    } else {
        $chat_user = array();
    }
}

if (empty($chat_id)) {
    $html = LoadPage("messages/ajax/no-messages");
} else {
    $messages_html = GetMessages($chat_id, array('chat_user' => $chat_user, 'return_method' => 'html'));
    if (!empty($messages_html)) {
        $html = LoadPage("messages/ajax/messages", array('MESSAGES' => $messages_html));
    } else {
        $html = LoadPage("messages/ajax/no-messages-users");
    }
}

$users_html = GetMessagesUserList(array('return_method' => 'html'));
if (empty($users_html)) {
    $users_html = '<p class="empty_state"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>No users found</p>';
}
$ask->page_url_ = $ask->config->site_url.'/messages';
$ask->chat_id = $chat_id;
$ask->chat_user = $chat_user;

$sidebar = LoadPage('messages/sidebar', array('USERS' => $users_html));
$ask->content = LoadPage("messages/ajax/content", array(
    'SIDEBAR' => $sidebar,
    'HTML' => $html
));