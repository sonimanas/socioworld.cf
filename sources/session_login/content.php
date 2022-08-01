<?php
if (IS_LOGGED == true || !isset($_GET['sid']) || empty($_GET['sid'])) {
    header("Location: {$site_url}");
    exit();
}
$session_id = Secure($_GET['sid']);
$db->where('session_id',$session_id);
$__session__  = $db->getValue(T_SESSIONS,'count(`id`)');
if (empty($__session__)) {
    header("Location: " . UrlLink(''));
    exit();
}
$_SESSION['user_id'] = $session_id;
setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
$ask->loggedin = true;

$db->join(T_SESSIONS . " s", "s.user_id=u.id", "LEFT");
$db->where("s.session_id", $session_id);
$user = $db->get(T_USERS . " u", null, "*");
if(empty($user[0])){
    header("Location: {$site_url}");
    exit();
}

else{
    if($user[0]->active == 0){
        //redirect to required activate page
        header("Location: " . UrlLink(''));
    }

    else{

        if($user[0]->startup == 0){
            //redirect to step avatar
            header("Location: " . UrlLink('/steps/avatar'));
        }

        else if($user[0]->startup == 1){
            //redirect to step info
            header("Location: " . UrlLink('/steps/info'));
        }

        else{
            //redirect to profile home
            header("Location: " . UrlLink('/@' . $user[0]->username));
        }

    }
}
exit();