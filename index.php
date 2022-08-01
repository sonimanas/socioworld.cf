<?php
require_once('./assets/init.php');
require_once('./assets/import/social-login/autoload.php');

$type         = (!empty($_GET['type'])) ? Secure($_GET['type']) : '';
$page = 'home';
$path = '';
$user_id = '';
$username = '';

$ask->path = $path;
if (isset($_GET['link1'])) {
    $page = $_GET['link1'];
}


if ($page == 'endpoint' && !empty( $_GET['link1'])) {
        if( !isset($_REQUEST['server_key']) ){
            header('Content-Type: application/json');
            echo json_encode(['status' => 400,"error" => 'Missing server key']);
            exit();
        }else{
            if( $_REQUEST['server_key'] !== $ask->config->server_key ) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 400, "error" => 'Invalid server key']);
                exit();
            }
        }
        if (!empty($_GET['first'])) {
            $first = Secure($_GET['first'], 0);
        }
        if (!empty($_GET['second'])) {
            $second = Secure($_GET['second'], 0);
        }
        $second = Secure($_GET['type'], 0);

        require_once "./endpoint/functions.php";
        $data = [];
        $file_location = "./endpoint/v1/$second.php";
        $api = (!empty($_GET['type'])) ? Secure($_GET['type']) : '';
        $option = (!empty($_GET['first'])) ? Secure($_GET['first']) : ''; 
        $whitelist = [
            'login',
            'user',
            'forgot-password',
            'reset-password',
            'signup',
            'contact',
            'options',
            'notifications',
            'logout',
            'messages',
            'ads',
            'search',
            'top-seller',
            'wallet',
            'get-trending',
            'get-profile',
            'get-pro-user',
            'get-genres',
            'get-following',
            'get-follower',
            'session_status',
            'question',
            'social-login'
        ];

        $is_whitelist = false;
        if( in_array($api, $whitelist) ) $is_whitelist = true;
        if( in_array($option, $whitelist) ) $is_whitelist = true;
        

        if( $is_whitelist === false ) {
            if( !isset($_REQUEST['access_token']) ){
                header('Content-Type: application/json');
                echo json_encode(['status' => 400,"error" => 'Invalid access token']);
                exit();
            }
            if (empty($_REQUEST['access_token'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 400,"error" => 'Invalid access token']);
                exit();
            }
            if (IsLogged() === false) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 400,"error" => 'Invalid access token']);
                exit();
            }
        }

        if (file_exists($file_location)) {
            require_once $file_location;
            if (!empty($errors)) {
                $data = array(
                    'status' => 400,
                    'error' => end($errors)
                );
            }
        } else {
            $data = array(
                'status' => 400,
                'error' => "Endpoint not found"
            );
        }

        if(empty($data)){
            $data = array(
                'status' => 400,
                'error' => "Error while processing your request"
            );
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
if (IS_LOGGED == true) {
    if ($user->last_active < (time() - 60)) {
        $update = $db->where('id', $user->id)->update('users', array(
            'last_active' => time()
        ));
    }

    if($page !== 'logout') {

        if ($user->startup == 0) {
            $page = 'steps';
            $_SESSION['steps_type'] = 'avatar';

        }

        else if ($user->startup == 1) {
            $page = 'steps';
            $_SESSION['steps_type'] = 'info';
        }


    }
}




if (file_exists("./sources/$page/content.php")) {
    include("./sources/$page/content.php");
}

if (empty($ask->content)) {
    include("./sources/404/content.php");
}

$side_header = 'not-logged';

if (IS_LOGGED == true) {
    $side_header = 'loggedin';
}

$footerall            = '';
$footerlogin            = '';
$og_meta           = '';


if($ask->page == 'post') {
    $ask->title = '@' .$user_data->username .' '. $ask->qdata[0]->question . ' | ' . $ask->config->title;
    $ask->description = ' Question by @' . $user_data->username . ', ' . $ask->qdata[0]->question;
    $ask->keyword = $ask->keyword . ',' . $user_data->username . ',' . $ask->qdata[0]->question;

    $og_meta = LoadPage('header/og-meta', array(
        'TITLE' =>  $ask->title,
        'DESC' =>  $ask->description,
        'URL' => $ask->config->site_url.'/post/'.Secure($_GET['postid']),
        'THUMB' => '{{CONFIG theme_url}}/img/icon.png'
    ));

}

else if ( url() == ($ask->config->site_url.'/@'.$username)) {
    $user_data   = UserData($user_id, [
        'data' => true
    ]);
    if  ( !empty($user_data->about)){
        $decs = $user_data->about;
        
    } else {
        $decs = $ask->config->description;
    }
    $og_meta = LoadPage('header/og-meta', array(
        'TITLE' => $user_data->username .''. $user_data->first_name .'' . $user_data->last_name,
        'DESC'  => $decs,
        'URL'   => $ask->config->site_url.'/@'. $user_data->username,
        'THUMB' => $user_data->avatar_path
 
    ));
} else {
     $og_meta = LoadPage('header/og-meta', array(
        'TITLE' => $ask->config->title,
        'DESC' => $ask->config->description,
        'URL' => $site_url,
        'THUMB' => '{{CONFIG theme_url}}/img/icon.png',
        
    ));
}

$langs__footer = $langs;
$langs_right    = '';
$langs_left    = '';
$number = 0;
foreach ($langs__footer as $key => $language) {
    $lang_explode = explode('.', $language);
    $language     = $lang_explode[0];
    $language_    = ucfirst($language);
    if ($number % 2 == 0) {
        $langs_right .= LoadPage('footer/languages', array('LANGID' => $language, 'LANGNAME' => $language_));
    }else{
        $langs_left .= LoadPage('footer/languages', array('LANGID' => $language, 'LANGNAME' => $language_));
    }
    $number++;
}


$footerall = LoadPage('footer/content', array(
    'DATE' => date('Y'),
    'LANGS_RIGHT' => $langs_right,
    'LANGS_LEFT' => $langs_left
));

$footerlogin = LoadPage('footer/login', array(
    'DATE' => date('Y'),
    'LANGS_RIGHT' => $langs_right,
    'LANGS_LEFT' => $langs_left
));


/* Get active Announcements */
$announcement_html = '';
if (IS_LOGGED === true && $ask->page != 'timeline') {

    $announcement          = get_announcments();
    if(!empty($announcement)) {
        $announcement_html =  LoadPage("announcements/content",array(
            'ANN_ID'       => $announcement->id,
            'ANN_TEXT'     => Decode($announcement->text),
        ));
    }
}
/* Get active Announcements */

$final_content = LoadPage('container', array(
    'CONTAINER_TITLE' => $ask->title,
    'CONTAINER_DESC' => $ask->description,
    'CONTAINER_KEYWORDS' => $ask->keyword,
    'CONTAINER_CONTENT' => $ask->content,
    'IS_LOGGED' => (IS_LOGGED == true) ? 'data-logged="true"' : '',
    'MAIN_URL' => $ask->actual_link,
    'HEADER_LAYOUT' => LoadPage('header/content', array(
    'SIDE_HEADER' => LoadPage("header/$side_header"),
    'SEARCH_KEYWORD' => (!empty($_GET['keyword'])) ? Secure($_GET['keyword']) : ''
    )),
    'FOOTER_LAYOUT_ALL' => $footerall,
    'FOOTER_LAYOUT_LOGIN' => $footerlogin,
    'OG_METATAGS' => $og_meta,
    'EXTRA_JS' => LoadPage('extra-js/content'),
    'MODE' => (($ask->displaymode == 'night') ? 'checked' : ''),
    'ACTIVE_LANG' => $ask->language,
    'ACTIVE_LANGNAME' => ucfirst($ask->language)
));



echo $final_content;


$db->disconnect();
unset($ask);