<?php
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', true);
error_reporting(0);
@ini_set('max_execution_time', 0);
require 'config.php';
require 'phpMailer_config.php';
require 'assets/import/DB/vendor/autoload.php';
require 'assets/import/getID3-1.9.14/getid3/getid3.php';
require 'assets/import/s3/aws-autoloader.php';
require 'assets/import/ftp/vendor/autoload.php';
require 'assets/import/imagethumbnail.php';

$ask     = ToObject(array());

$ask->directory_separator = DIRECTORY_SEPARATOR;
$ask->base_path = realpath(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR;

// Connect to MySQL Server
$mysqli     = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
$sqlConnect = $mysqli;

// Handling Server Errors
$ServerErrors = array();
if (mysqli_connect_errno()) {
    $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists('curl_init')) {
    $ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
    $ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!extension_loaded('zip')) {
    $ServerErrors[] = "ZipArchive extension is NOT installed on your web server !";
}

if (isset($ServerErrors) && !empty($ServerErrors)) {
    foreach ($ServerErrors as $Error) {
        echo "<h3>" . $Error . "</h3>";
    }
    die();
}
$query = $mysqli->query("SET NAMES utf8mb4");
// Connecting to DB after verfication

$db = new MysqliDb($mysqli);

$http_header = 'http://';
if (!empty($_SERVER['HTTPS'])) {
    $http_header = 'https://';
}

$ask->site_pages          = array('home');
$ask->actual_link         = $http_header . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']);
$config                   = GetConfig();
$ask->loggedin            = false;
$config['user_statics']   = stripslashes(htmlspecialchars_decode($config['user_statics']));
$config['questions_statics'] = stripslashes(htmlspecialchars_decode($config['questions_statics']));
$config['theme_url']      = $site_url . '/themes/' . $config['theme'];
$config['site_url']       = $site_url;
$config['script_version'] = $config['version'];
$ask->extra_config = array();
$config['hostname'] = '';
$config['server_port'] = '';
$site = parse_url($site_url);
if (empty($site['host'])) {
    $config['hostname'] = $site['scheme'] . '://' .  $site['host'];
}
$ask->config              = ToObject($config);
$langs                    = db_langs();
$ask->langs               = $langs;

if (IsLogged() == true) {
    $session_id        = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
  if (isset($_POST['access_token']) && !empty($_POST['access_token'])) {
    $ask->user_session  = GetUserFromSessionID($_POST['access_token'], 'mobile');
    $ask->user_session_id = secure($_POST['access_token']);
} else{
        $session_id        = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
        $ask->user_session  = GetUserFromSessionID($session_id);
    }
    $user = $ask->user  = UserData($ask->user_session);
    if (!empty($user->language) && in_array($user->language, $langs)) {
        $_SESSION['lang'] = $user->language;
    }

    if ($user->id < 0 || empty($user->id) || !is_numeric($user->id) || UserActive($user->id) === false) {
        header("Location: " . UrlLink('logout'));
    }
    $ask->loggedin   = true;
}
else if (!empty($_POST['user_id']) && !empty($_POST['s'])) {
    $platform       = ((!empty($_POST['platform'])) ? $_POST['platform'] : 'phone');
    $s              = Secure($_POST['s']);
    $user_id        = Secure($_POST['user_id']);
    $verify_session = verify_api_auth($user_id, $s, $platform);
    if ($verify_session === true) {
        $user = $ask->user  = UserData($user_id);
        if (empty($user) || UserActive($user->id) === false) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'authentication_failed',
                'errors' => array(
                    'error_id' => '1',
                    'error_text' => 'Error 400 - The user does not exist'
                )
            );
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }
        $ask->loggedin = true;
    }
    else {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'authentication_failed',
            'errors' => array(
                'error_id' => '1',
                'error_text' => 'Error 400 - Session does not exist'
            )
        );
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
else if (!empty($_GET['user_id']) && !empty($_GET['s'])) {
    $platform       = ((!empty($_GET['platform'])) ? $_GET['platform'] : 'phone');
    $s              = Secure($_GET['s']);
    $user_id        = Secure($_GET['user_id']);
    $verify_session = verify_api_auth($user_id, $s, $platform);
    if ($verify_session === true) {
        $user = $ask->user  = UserData($user_id);
        if (empty($user) || UserActive($user->id) === false) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'authentication_failed',
                'errors' => array(
                    'error_id' => '1',
                    'error_text' => 'Error 400 - The user does not exist'
                )
            );

            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }

        $ask->loggedin = true;
    }
    else {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'authentication_failed',
            'errors' => array(
                'error_id' => '1',
                'error_text' => 'Error 400 - Session does not exist'
            )
        );
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
elseif (!empty($_GET['cookie']) && $ask->loggedin != true) {
    $session_id            = $_GET['cookie'];
    $ask->user_session     = GetUserFromSessionID($session_id);
    if (!empty($ask->user_session) && is_numeric($ask->user_session)) {
        $user = $ask->user = UserData($ask->user_session);
        $ask->loggedin     = true;

        if (!empty($user->language)) {
            if (file_exists(__DIR__ . '/../langs/' . $user->language . '.php')) {
                $_SESSION['lang'] = $user->language;
            }
        }
        setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
    }
}

if (isset($_GET['lang']) AND !empty($_GET['lang'])) {
    $lang_name = Secure(strtolower($_GET['lang']));
    if (in_array($lang_name, $langs)) {
        $_SESSION['lang'] = $lang_name;
        if ($ask->loggedin == true) {
            $db->where('id', $user->id)->update(T_USERS, array('language' => $lang_name));
        }
    }
}
if (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = $ask->config->language;
}

if (isset($_SESSION['user_id'])) {
    if (empty($_COOKIE['user_id'])) {
        setcookie("user_id", $_SESSION['user_id'], time() + (10 * 365 * 24 * 60 * 60), "/");
    }
}

$ask->language      = $_SESSION['lang'];
$ask->language_type = 'ltr';

// Add rtl languages here.
$rtl_langs           = array(
    'arabic'
);

// checking if corrent language is rtl.
foreach ($rtl_langs as $lang) {
    if ($ask->language == strtolower($lang)) {
        $ask->language_type = 'rtl';
    }
}

// Include Language File
$lang_file = 'assets/langs/' . $ask->language . '.php';
if (file_exists($lang_file)) {
    require($lang_file);
}

$lang_array = get_langs($ask->language);

if (empty($lang_array)) {
    $lang_array = get_langs();
}

$lang       = ToObject($lang_array);

$ask->lang    = $lang;

$ask->default_lang    = ToObject(get_langs());

$ask->exp_feed    = false;
$ask->userDefaultAvatar = 'upload/photos/d-avatar.jpg';
$ask->categories  = ToObject($categories);

$error_icon   = '<i class="fa fa-exclamation-circle"></i> ';
$success_icon = '<i class="fa fa-check"></i> ';
define('IS_LOGGED', $ask->loggedin);
define('none', null);

if (is_banned($_SERVER["REMOTE_ADDR"]) === true) {
    $banpage = LoadPage('terms/ban');
    exit($banpage);
}

$ask->displaymode = (!empty($_COOKIE['mode'])) ? $_COOKIE['mode'] : null;
if ($ask->config->night_mode == 'night' && empty($ask->mode)) {
    $ask->displaymode = 'night';
}
if (empty($_COOKIE['mode']) || !in_array($_COOKIE['mode'], array('night','day')) && empty($ask->mode)) {
    $ask->displaymode = ($ask->config->night_mode == 'night') ? 'night' : 'day';
    setcookie("mode", $ask->displaymode, time() + (10 * 365 * 24 * 60 * 60), "/");
}

if (!empty($_POST['mode']) && in_array($_POST['mode'], array('night','day'))) {
    setcookie("mode", $_POST['mode'], time() + (10 * 365 * 24 * 60 * 60), "/");
    $ask->displaymode = $_POST['mode'];
}

if (!empty($_GET['mode']) && in_array($_GET['mode'], array('night','day'))) {
    setcookie("mode", $_GET['mode'], time() + (10 * 365 * 24 * 60 * 60), "/");
    $ask->displaymode = $_GET['mode'];
}

if ($ask->config->night_mode == 'day') {
    $ask->displaymode = 'day';
}

$site_url    = $ask->config->site_url;
$request_url = $_SERVER['REQUEST_URI'];
$fl_currpage = "{$site_url}{$request_url}";

if (empty($_SESSION['uploads'])) {
    $_SESSION['uploads'] = array();
    if (empty($_SESSION['uploads']['videos'])) {
        $_SESSION['uploads']['videos'] = array();
    }
    if (empty($_SESSION['uploads']['images'])) {
        $_SESSION['uploads']['images'] = array();
    }
}

$ask->update_cache                  = '';
if (!empty($ask->config->last_update)) {
    $update_cache = time() - 21600;
    if ($update_cache < $ask->config->last_update) {
        $ask->update_cache = '?' . sha1(time());
    }
}

$ask->theme_using = 'default';
$path_to_details = './themes/' . $config['theme'] . '/fonts/info.json';
if (file_exists($path_to_details)) {
    $get_theme_info = file_get_contents($path_to_details);
    $decode_json = json_decode($get_theme_info, true);
    if (!empty($decode_json['name'])) {
        $ask->theme_using = $decode_json['name'];
    }
}
$ask->continents = array('Asia','Australia','Africa','Europe','America','Atlantic','Pacific','Indian');
require 'context_data.php';
try {
    $ask->custom_pages = $db->get(T_CUSTOM_PAGES);
} catch (Exception $e) {
    $ask->custom_pages = [];
}

if (!isset($_COOKIE['ad-con'])) {
    setcookie('ad-con', htmlentities(serialize(array(
        'date' => date('Y-m-d'),
        'ads' => array()
    ))), time() + (10 * 365 * 24 * 60 * 60));
}
$ask->ad_con = array();
if (!empty($_COOKIE['ad-con'])) {
    $ask->ad_con = unserialize(html_entity_decode($_COOKIE['ad-con']));
}
if (!is_array($ask->ad_con) || !isset($ask->ad_con['date']) || !isset($ask->ad_con['ads'])) {
    setcookie('ad-con', htmlentities(serialize(array(
        'date' => date('Y-m-d'),
        'ads' => array()
    ))), time() + (10 * 365 * 24 * 60 * 60));
}
if (is_array($ask->ad_con) && isset($ask->ad_con['date']) && strtotime($ask->ad_con['date']) < strtotime(date('Y-m-d'))) {
    setcookie('ad-con', htmlentities(serialize(array(
        'date' => date('Y-m-d'),
        'ads' => array()
    ))), time() + (10 * 365 * 24 * 60 * 60));
}



if ($ask->config->last_promote_question_update < (time() - 1800)) {
    UnPromoteQuestions();
}

require_once('assets/includes/paypal_config.php');
