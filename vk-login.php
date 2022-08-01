<?php
require_once('assets/init.php');
if (isset($_GET['code']) && !empty($_GET['code'])) {
	try {
        $app_id        = $ask->config->vkontakte_app_ID;
        $app_secret    = $ask->config->vkontakte_app_key;
        $vk_url  = $ask->config->vkontakte_domain_uri;
        $siteRedirectUri = $ask->config->site_url . '/vk-login.php';
        $code          = $_GET['code'];
        $url           = $vk_url . "/access_token?client_id={$app_id}&display=page&redirect_uri={$siteRedirectUri}&client_secret={$app_secret}&code={$_GET['code']}";
        $get           = file_get_contents($url);
        $vk_json_reply = json_decode($get, true);
        $access_token  = '';
        if (is_array($vk_json_reply) && isset($vk_json_reply['access_token'])) {
            $access_token    = $vk_json_reply['access_token'];
            $user_idvk         = $vk_json_reply['user_id'];
            $type            = "get_user_data";
            $url             = "https://api.vk.com/method/users.get?user_id={$user_idvk}&access_token={$access_token}&fields=uid,first_name,last_name,photo_big";
            $user_data_json  = file_get_contents($url);
            $user_data_array = json_decode($user_data_json, true);
            if (is_array($user_data_array) && !empty($user_data_array) && isset($user_data_array['user_data'])) {
                $user_data  = $user_data_array['user_data'];
                $user_email = $user_data['email'];
                if (UserEmailExists($user_email) === true) {
                    $db->where('email', $user_email);
                    $login               = $db->getOne(T_USERS);
                    $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime());
                    $insert_data         = array(
                        'user_id' => $login->id,
                        'session_id' => $session_id,
                        'time' => time()
                    );
                    $insert              = $db->insert(T_SESSIONS, $insert_data);
                    $_SESSION['user_id'] = $session_id;
                    setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
                    header("Location: " . UrlLink(''));
                    exit();
                }
            } else {
                $str          = md5(microtime());
                $id           = substr($str, 0, 9);
                $user_uniq_id = (empty($db->where('username', $id)->getValue(T_USERS, 'id'))) ? $id : 'u_' . $id;
                $first_name   = (isset($user_data['first_name'])) ? Secure($user_data['first_name'], 0) : '';
                $last_name    = (isset($user_data['last_name'])) ? Secure($user_data['last_name'], 0) : '';
                $gender       = (isset($user_data['gender'])) ? Secure($user_data['gender'], 0) : 'male';
                $username     = (Secure($user_data['username']));
                $provider     = ($vk_url . "/{$username}");
                $re_data      = array(
                    'username' => Secure($user_uniq_id, 0),
                    'email' => Secure($user_email, 0),
                    'password' => Secure(sha1($user_email), 0),
                    'email_code' => Secure(md5($user_uniq_id), 0),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'gender' => Secure($gender),
                    'last_active' => time(),
                    'registered' => date('Y') . '/' . intval(date('m')),
                    'user_upload_limit' => 0,
                    'active' => 1,
                    'ip_address' => get_ip_address()
                );
                
                $insert_id = $db->insert(T_USERS, $re_data);
                if ($insert_id) {
                    $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime());
                    $insert_data         = array(
                        'user_id' => $insert_id,
                        'session_id' => $session_id,
                        'time' => time()
                    );
                    $insert              = $db->insert(T_SESSIONS, $insert_data);
                    $_SESSION['user_id'] = $session_id;
                    setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
                    header("Location: " . UrlLink(''));
                    exit();
                }
            }
        }
    } catch (Exception $e) {
        exit($e->getMessage());
        switch ($e->getCode()) {
            case 0:
                echo "Unspecified error.";
                break;
            case 1:
                echo "Hybridauth configuration error.";
                break;
            case 2:
                echo "Provider not properly configured.";
                break;
            case 3:
                echo "Unknown or disabled provider.";
                break;
            case 4:
                echo "Missing provider application credentials.";
                break;
            case 5:
                echo "Authentication failed The user has canceled the authentication or the provider refused the connection.";
                break;
            case 6:
                echo "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
                break;
            case 7:
                echo "User not connected to the provider.";
                break;
            case 8:
                echo "Provider does not support this feature.";
                break;
        }
        echo " an error found while processing your request!";
        echo " <b><a href='" . UrlLink('') . "'>Try again<a></b>";
    }
}else{
    header("Location: " . UrlLink(''));
    exit();
}