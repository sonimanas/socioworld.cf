<?php
function lang($string = '') {
    global $lang_array, $db;
    $string = trim($string);
    $stringFromArray = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','_', $string));
    if (in_array($stringFromArray, array_keys($lang_array))) {
        return __($stringFromArray); //$lang_array[$stringFromArray];
    }
    $insert = ($db->where('lang_key', $stringFromArray)->getValue(T_LANGS, 'count(*)') > 0) ? true : $db->insert(T_LANGS, ['lang_key' => $stringFromArray, 'english' => secure($string)]);

    $lang_array[$stringFromArray] = $string;
    return $string;
}
function LoadPage($page_url = '', $data = array(), $set_lang = true) {
    global $ask, $lang_array, $config, $fl_currpage, $countries_name;
    $page = './themes/' . $config['theme'] . '/layout/' . $page_url . '.html';
    if (!file_exists($page)) {
        die("File not Exists : $page");
    }
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    if ($set_lang == true) {
        $page_content = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($lang_array) {
            return lang($m[1]);
        }, $page_content);
    }
    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $replace) {
            if ($key == 'USER_DATA') {
                $replace = ToArray($replace);
                $page_content = preg_replace_callback("/{{USER (.*?)}}/", function($m) use ($replace) {
                    return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                }, $page_content);
            } else {
                if( is_array($replace) || is_object($replace) ){
                    $arr = explode('_',$key);
                    $k = strtoupper($arr[0]);
                    $replace = ToArray($replace);
                    $page_content = preg_replace_callback("/{{".$k." (.*?)}}/", function($m) use ($replace) {
                        return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                    }, $page_content);
                }else{
                    $object_to_replace = "{{" . $key . "}}";
                    $page_content      = str_replace($object_to_replace, $replace, $page_content);
                }
            }
        }
    }
    if (IS_LOGGED == true) {
        $replace = ToArray($ask->user);
        $page_content = preg_replace_callback("/{{ME (.*?)}}/", function($m) use ($replace) {
            return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
        }, $page_content);
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", UrlLink("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);
    return $page_content;
}
function ToObject($array) {
    $object = new stdClass();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value = ToObject($value);
        }
        if (isset($value)) {
            $object->$key = $value;
        }
    }
    return $object;
}
function ToArray($obj) {
    if (is_object($obj))
        $obj = (array) $obj;
    if (is_array($obj)) {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = ToArray($val);
        }
    } else {
        $new = $obj;
    }
    return $new;
}
function UrlLink($string) {
    global $site_url;
    return rtrim($site_url ,'/') . str_replace('//','/','/' . $string);
}
function db_langs() {
    global $db;
    $data   = array();
    $t_lang = T_LANGS;
    try {
        $query  = $db->rawQuery("DESCRIBE `$t_lang`");
    } catch (Exception $e) {

    }
    foreach ($query as $column) {
        $data[] = $column->Field;
    }
    unset($data[0]);
    unset($data[1]);
    return $data;
}
function url(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
function Secure($string, $censored_words = 1, $br = true) {
    global $mysqli;
    if(!is_array($string)) {

        $string = trim($string);
        $string = mysqli_real_escape_string($mysqli, $string);
        $string = htmlspecialchars($string, ENT_QUOTES);

        if ($br == true) {
            $string = str_replace('\r\n', " <br>", $string);
            $string = str_replace('\n\r', " <br>", $string);
            $string = str_replace('\r', " <br>", $string);
            $string = str_replace('\n', " <br>", $string);
        } else {
            $string = str_replace('\r\n', "", $string);
            $string = str_replace('\n\r', "", $string);
            $string = str_replace('\r', "", $string);
            $string = str_replace('\n', "", $string);
        }
        $string = stripslashes($string);
        $string = str_replace('&amp;#', '&#', $string);
        $string = preg_replace("/{{(.*?)}}/", '', $string);

    }
    return $string;
}
function createUserSession($user_id = 0,$platform = 'web') {
    global $db,$sqlConnect, $ask;
    if (empty($user_id)) {
        return false;
    }
    $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime() . $user_id);
    $insert_data         = array(
        'user_id' => $user_id,
        'session_id' => $session_id,
        'platform' => $platform,
        'time' => time()
    );

    $insert              = $db->insert(T_SESSIONS, $insert_data);

    $_SESSION['user_id'] = $session_id;
    setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
    $ask->loggedin = true;

    $query_two = mysqli_query($sqlConnect, "DELETE FROM " . T_APP_SESSIONS . " WHERE `session_id` = '{$session_id}'");
    if ($query_two) {
        $ua = serialize(GetBrowser());
        $delete_same_session = $db->where('user_id', $user_id)->where('platform_details', $ua)->delete(T_APP_SESSIONS);
        $query_three = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`, `platform_details`, `time`) VALUES('{$user_id}', '{$session_id}', 'web', '$ua'," . time() . ")");
        if ($query_three) {
            return $session_id;
        }
    }
}
function GetBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
    // First get the platform?
    if (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    } elseif (preg_match('/iphone|IPhone/i', $u_agent)) {
        $platform = 'IPhone Web';
    } elseif (preg_match('/android|Android/i', $u_agent)) {
        $platform = 'Android Web';
    } else if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $u_agent)) {
        $platform = 'Mobile';
    } else if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif(preg_match('/Firefox/i',$u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif(preg_match('/Chrome/i',$u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif(preg_match('/Safari/i',$u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif(preg_match('/Opera/i',$u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif(preg_match('/Netscape/i',$u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        } else {
            $version= $matches['version'][1];
        }
    } else {
        $version= $matches['version'][0];
    }
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern,
        'ip_address' => get_ip_address()
    );
}
function getPageFromPath($path = '') {
    if (empty($path)) {
        return false;
    }
    $path = explode("/", $path);
    $data = array();
    $data['link1'] = array();
    if (!empty($path[0])) {
        $data['page'] = $path[0];
    }
    if (!empty($path[1])) {
        unset($path[0]);
        $data['link1'] = $path;
    }
    return $data;
}
function GetUserFromSessionID($session_id, $platform = 'web') {
    global $db;
    if (empty($session_id)) {
        return false;
    }
    $platform   = Secure($platform);
    $session_id = Secure($session_id);
    $return     = $db->where('session_id', $session_id);
    return $db->getValue(T_SESSIONS, 'user_id');
}
function IsLogged() {
     if (isset($_POST['access_token'])) {
        $id = GetUserFromSessionID($_POST['access_token'], 'mobile');
        if (is_numeric($id) && !empty($id)) {
            return true;
        }else{
            return false;
        }
    }

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $id = GetUserFromSessionID($_SESSION['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    }
    else if (!empty($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])) {
        $id = GetUserFromSessionID($_COOKIE['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    }
    else {
        return false;
    }
}
function UpdateAdminDetails() {
    global $ask, $db;

    $get_questions_count = $db->where('ask_user_id', '0')->where('ask_question_id', '0')->where('shared_user_id', '0')->where('shared_question_id', '0')->where('replay_user_id', '0')->where('replay_question_id', '0')->getValue(T_QUESTIONS, 'count(*)');
    $update_questions_count = $db->where('name', 'total_questions')->update(T_CONFIG, array('value' => $get_questions_count));

    $get_anon_questions_count = $db->where('is_anonymously', '1')->where('ask_user_id', '0')->where('ask_question_id', '0')->where('shared_user_id', '0')->where('shared_question_id', '0')->where('replay_user_id', '0')->where('replay_question_id', '0')->getValue(T_QUESTIONS, 'count(*)');
    $update_anon_questions_count = $db->where('name', 'total_anon_questions')->update(T_CONFIG, array('value' => $get_anon_questions_count));

    $get_non_anon_questions_count = $db->where('is_anonymously', '0')->where('ask_user_id', '0')->where('ask_question_id', '0')->where('shared_user_id', '0')->where('shared_question_id', '0')->where('replay_user_id', '0')->where('replay_question_id', '0')->getValue(T_QUESTIONS, 'count(*)');
    $update_non_anon_questions_count = $db->where('name', 'total_non_anon_questions')->update(T_CONFIG, array('value' => $get_non_anon_questions_count));

    $get_answers_count = $db->where('ask_user_id', '0' , '>')->where('ask_question_id', '0', '>')->where('shared_user_id', '0')->where('shared_question_id', '0')->where('replay_user_id', '0')->where('replay_question_id', '0')->getValue(T_QUESTIONS, 'count(*)');
    $update_answers_count = $db->where('name', 'total_answers')->update(T_CONFIG, array('value' => $get_answers_count));

    $get_shares_count = $db->where('ask_user_id', '0')->where('ask_question_id', '0')->where('shared_user_id', '0' , '>')->where('shared_question_id', '0' , '>')->where('replay_user_id', '0')->where('replay_question_id', '0')->getValue(T_QUESTIONS, 'count(*)');
    $update_shares_count = $db->where('name', 'total_shares')->update(T_CONFIG, array('value' => $get_shares_count));

    $get_replys_count = $db->where('ask_user_id', '0')->where('ask_question_id', '0')->where('shared_user_id', '0')->where('shared_question_id', '0')->where('replay_user_id', '0' , '>')->where('replay_question_id', '0' , '>')->getValue(T_QUESTIONS, 'count(*)');
    $update_replys_count = $db->where('name', 'total_replys')->update(T_CONFIG, array('value' => $get_replys_count));

    $get_subs_count = $db->where('active', '1')->getValue(T_USERS, 'count(*)');
    $update_subs_count = $db->where('name', 'total_active_users')->update(T_CONFIG, array('value' => $get_subs_count));

    $get_unsubs_count = $db->where('active', '0')->getValue(T_USERS, 'count(*)');
    $update_unsubs_count = $db->where('name', 'total_unactive_users')->update(T_CONFIG, array('value' => $get_unsubs_count));

    $user_statics = array();
    $question_statics = array();

    $months = array('1','2','3','4','5','6','7','8','9','10','11','12');
    $date = date('Y');

    foreach ($months as $value) {
        $monthNum  = $value;
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');
        $user_statics[] = array('month' => $monthName, 'new_users' => $db->where('registered', "$date/$value")->getValue(T_USERS, 'count(*)'));
        $question_statics[] = array('month' => $monthName, 'new_questions' => $db->where('YEAR(FROM_UNIXTIME(`time`))', "$date")->where('MONTH(FROM_UNIXTIME(`time`))', "$value")->getValue(T_QUESTIONS, 'count(*)'));
    }
    $update_user_statics = $db->where('name', 'user_statics')->update(T_CONFIG, array('value' => Secure(json_encode($user_statics))));
    $update_videos_statics = $db->where('name', 'questions_statics')->update(T_CONFIG, array('value' => Secure(json_encode($question_statics))));

    $update_saved_count = $db->where('name', 'last_admin_collection')->update(T_CONFIG, array('value' => time()));
}
function Decode($text = '') {
    return htmlspecialchars_decode($text);
}
function is_banned($ip_address = false){
    global $ask, $db;
    $table = T_BANNED_IPS;
    try {
        $ip    = $db->where('ip_address',$ip_address,'=')->getValue($table,"count(*)");
        return ($ip > 0);
    } catch (Exception $e) {
        return false;
    }
}
function get_announcments() {
    global $ask, $db;
    if (IS_LOGGED === false) {
        return false;
    }

    $views_table  = T_ANNOUNCEMENT_VIEWS;
    $table        = T_ANNOUNCEMENTS;
    $user         = $ask->user->id;
    $subsql       = "SELECT `announcement_id` FROM `$views_table` WHERE `user_id` = '{$user}'";
    $fetched_data = $db->where(" `active` = '1' AND `id` NOT IN ({$subsql}) ")->orderBy('RAND()')->getOne(T_ANNOUNCEMENTS);
    return $fetched_data;
}
function LoadAdminLinkSettings($link = '') {
    global $site_url;
    return $site_url . '/admin-cp/' . $link;
}
function LoadAdminLink($link = '') {
    global $site_url;
    return $site_url . '/admin-panel/' . $link;
}
function LoadAdminPage($page_url = '', $data = array(), $set_lang = true) {
    global $ask, $lang_array, $config, $db;
    $page = './admin-panel/pages/' . $page_url . '.html';
    if (!file_exists($page)) {
        return false;
    }
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    if ($set_lang == true) {
        $page_content = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($lang_array) {
            return (isset($lang_array[$m[1]])) ? $lang_array[$m[1]] : '';
        }, $page_content);
    }
    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $replace) {
            if ($key == 'USER_DATA') {
                $replace = ToArray($replace);
                $page_content = preg_replace_callback("/{{USER (.*?)}}/", function($m) use ($replace) {
                    return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                }, $page_content);
            } else {
                if( is_array($replace) || is_object($replace) ){
                    $arr = explode('_',$key);
                    $k = strtoupper($arr[0]);
                    $replace = ToArray($replace);
                    $page_content = preg_replace_callback("/{{".$k." (.*?)}}/", function($m) use ($replace) {
                        return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                    }, $page_content);
                }else{
                    $object_to_replace = "{{" . $key . "}}";
                    $page_content      = str_replace($object_to_replace, $replace, $page_content);
                }
            }
        }
    }
    if (IS_LOGGED == true) {
        $replace = ToArray($ask->user);
        $page_content = preg_replace_callback("/{{ME (.*?)}}/", function($m) use ($replace) {
            return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
        }, $page_content);
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", UrlLink("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);
    return $page_content;
}
function br2nl($st) {
    $breaks = array(
        "<br />",
        "<br>",
        "<br/>"
    );
    return str_ireplace($breaks, "\r\n", $st);
}
function verify_api_auth($user_id,$session_id, $platform = 'phone') {
    global $db;
    if (empty($session_id) || empty($user_id)) {
        return false;
    }
    $platform   = Secure($platform);
    $session_id = Secure($session_id);
    $user_id    = Secure($user_id);

    $db->where('session_id', $session_id);
    $db->where('user_id', $user_id);
    $db->where('platform', $platform);
    return ($db->getValue(T_SESSIONS, 'COUNT(*)') == 1);
}
function get_langs($lang = 'english') {
    global $db;
    $data   = array();
    $t_lang = T_LANGS;
    try {
        $query  = $db->rawQuery("SELECT `lang_key`, `$lang` FROM `$t_lang`");
        foreach ($query as $item) {
            $data[$item->lang_key] = $item->$lang;
        }
    } catch (Exception $e) {}
    return $data;
}
function Backup($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, $tables = false, $backup_name = false) {
    $mysqli = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
    $mysqli->select_db($sql_db_name);
    $mysqli->query("SET NAMES 'utf8'");
    $queryTables = $mysqli->query('SHOW TABLES');
    while ($row = $queryTables->fetch_row()) {
        $target_tables[] = $row[0];
    }
    if ($tables !== false) {
        $target_tables = array_intersect($target_tables, $tables);
    }
    $content = "-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Host Connection Info: " . $mysqli->host_info . "
-- Generation Time: " . date('F d, Y \a\t H:i A ( e )') . "
-- Server version: " . mysqli_get_server_info($mysqli) . "
-- PHP Version: " . PHP_VERSION . "
--\n
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";\n
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;\n\n";
    foreach ($target_tables as $table) {
        $result        = $mysqli->query('SELECT * FROM ' . $table);
        $fields_amount = $result->field_count;
        $rows_num      = $mysqli->affected_rows;
        $res           = $mysqli->query('SHOW CREATE TABLE ' . $table);
        $TableMLine    = $res->fetch_row();
        $content       = (!isset($content) ? '' : $content) . "
-- ---------------------------------------------------------
--
-- Table structure for table : `{$table}`
--
-- ---------------------------------------------------------
\n" . $TableMLine[1] . ";\n";
        for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
            while ($row = $result->fetch_row()) {
                if ($st_counter % 100 == 0 || $st_counter == 0) {
                    $content .= "\n--
-- Dumping data for table `{$table}`
--\n\nINSERT INTO " . $table . " VALUES";
                }
                $content .= "\n(";
                for ($j = 0; $j < $fields_amount; $j++) {
                    $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                    if (isset($row[$j])) {
                        $content .= '"' . $row[$j] . '"';
                    } else {
                        $content .= '""';
                    }
                    if ($j < ($fields_amount - 1)) {
                        $content .= ',';
                    }
                }
                $content .= ")";
                if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                    $content .= ";\n";
                } else {
                    $content .= ",";
                }
                $st_counter = $st_counter + 1;
            }
        }
        $content .= "";
    }
    $content .= "
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
    if (!file_exists('script_backups/' . date('d-m-Y'))) {
        @mkdir('script_backups/' . date('d-m-Y'), 0777, true);
    }
    if (!file_exists('script_backups/' . date('d-m-Y') . '/' . time())) {
        mkdir('script_backups/' . date('d-m-Y') . '/' . time(), 0777, true);
    }
    if (!file_exists("script_backups/" . date('d-m-Y') . '/' . time() . "/index.html")) {
        $f = @fopen("script_backups/" . date('d-m-Y') . '/' . time() . "/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    if (!file_exists('script_backups/.htaccess')) {
        $f = @fopen("script_backups/.htaccess", "a+");
        @fwrite($f, "deny from all\nOptions -Indexes");
        @fclose($f);
    }
    if (!file_exists("script_backups/" . date('d-m-Y') . "/index.html")) {
        $f = @fopen("script_backups/" . date('d-m-Y') . "/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    if (!file_exists('script_backups/index.html')) {
        $f = @fopen("script_backups/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    $folder_name = "script_backups/" . date('d-m-Y') . '/' . time();
    $put         = @file_put_contents($folder_name . '/SQL-Backup-' . time() . '-' . date('d-m-Y') . '.sql', $content);
    if ($put) {
        $rootPath = realpath('./');
        $zip      = new ZipArchive();
        $open     = $zip->open($folder_name . '/Files-Backup-' . time() . '-' . date('d-m-Y') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($open !== true) {
            return false;
        }
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
            if (!preg_match('/\bscript_backups\b/', $file)) {
                if (!$file->isDir()) {
                    $filePath     = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        $zip->close();
        $table = T_CONFIG;
        $date  = date('d-m-Y');
        $mysqli->query("UPDATE `$table` SET `value` = '$date' WHERE `name` = 'last_backup'");
        $mysqli->close();
        return true;
    } else {
        return false;
    }
}
function get_ip_address() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP))
                    return $ip;
            }
        } else {
            if (filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_FORWARDED'];
    return $_SERVER['REMOTE_ADDR'];
}
function GenerateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false) {
    $charset = '';
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if ($usenumbers) {
        $charset .= "123456789";
    }
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    return $key;
}
function Resize_Crop_Image($max_width, $max_height, $source_file, $dst_dir, $quality = 80) {
    $imgsize = @getimagesize($source_file);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $mime    = $imgsize['mime'];
    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image        = "imagegif";
            break;
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image        = "imagepng";
            break;
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image        = "imagejpeg";
            break;
        default:
            return false;
            break;
    }
    $dst_img    = @imagecreatetruecolor($max_width, $max_height);
    $src_img    = $image_create($source_file);
    $width_new  = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        $w_point = (($width - $width_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
    @$image($dst_img, $dst_dir, $quality);


    if ($dst_img)
        @imagedestroy($dst_img);
    if ($src_img)
        @imagedestroy($src_img);
}
function CompressImage($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg') {
        $image = @imagecreatefromjpeg($source_url);
        @imagejpeg($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/gif') {
        $image = @imagecreatefromgif($source_url);
        @imagegif($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = @imagecreatefrompng($source_url);
        @imagepng($image, $destination_url);
    }
}
function CreateSession() {
    $hash = sha1(rand(1111, 9999));
    if (!empty($_SESSION['hash'])) {
        $_SESSION['hash'] = $_SESSION['hash'];
        return $_SESSION['hash'];
    }
    $_SESSION['hash'] = $hash;
    return $hash;
}
function DeleteAdminInvitation($col = '', $val = false) {
    global $sqlConnect, $ask;
    if (!$val && !$col) {
        return false;
    }
    $val = Secure($val);
    $col = Secure($col);
    return mysqli_query($sqlConnect, "DELETE FROM " . T_INVITATIONS . " WHERE `$col` = '$val'");
}
function IsAdminInvitationExists($code = false) {
    global $sqlConnect, $ask;
    if (!$code) {
        return false;
    }
    $code      = Secure($code);
    $data_rows = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_INVITATIONS . " WHERE `code` = '$code'");
    return mysqli_num_rows($data_rows) > 0;
}
function GetAdminInvitation() {

    global $sqlConnect, $ask;
    if (IS_LOGGED == FALSE || IsAdmin() == FALSE) {
        return false;
    }
    $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_INVITATIONS . " ORDER BY `id` DESC ");
    $data  = array();
    $site  = $ask->config->site_url . '/register?invite=';
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $fetched_data['url'] = $site . $fetched_data['code'];
        $data[]              = $fetched_data;
    }
    return $data;
}
function InsertAdminInvitation() {
    global $sqlConnect, $ask;
    if (IS_LOGGED == FALSE || IsAdmin() == FALSE) {
        return false;
    }
    $time  = time();
    $code  = uniqid(rand(), true);
    $query   = mysqli_query($sqlConnect, "INSERT INTO " . T_INVITATIONS . " (`id`,`code`,`posted`) VALUES (NULL,'$code', '$time')");

    $site  = $ask->config->site_url . '/register?invite=';
    if ($query) {
        $last_id = mysqli_insert_id($sqlConnect);
        $data    = mysqli_query($sqlConnect, "SELECT * FROM " . T_INVITATIONS . " WHERE `id` = {$last_id}");
        if ($data && mysqli_num_rows($data) > 0) {
            $fetched_data        = mysqli_fetch_assoc($data);
            $fetched_data['url'] = $site . $fetched_data['code'];
            return $fetched_data;
        }
    }
    return false;
}
function GetLang($right = true){
    global $langs;
    $langs__footer = $langs;
    $langs_right    = '';
    $langs_left    = '';
    $number = 0;


    foreach ($langs__footer as $key => $language) {
        $lang_explode = explode('.', $language);
        $language     = $lang_explode[0];
        $language_    = ucfirst($language);
        if ($number % 2 == 0) {
            $langs_right .= LoadPage('footer/languages', ['LANGID' => $language, 'LANGNAME' => $language_]);
        }else{
            $langs_left .= LoadPage('footer/languages', ['LANGID' => $language, 'LANGNAME' => $language_]);
        }
        $number++;
    }
    if($right){
        return $langs_right;
    }

    else{
        return $langs_left;
    }
}
function Notify($data = array()){
    global $db;
    if (empty($data) || !is_array($data)) {
        return false;
    }

    if( isset($data['notifier_id']) && isset($data['recipient_id']) ){
        if( $data['notifier_id'] == $data['recipient_id'] ){
            return false;
        }
    }
    $t_notif = T_NOTIFICATIONS;
    $query   = $db->insert($t_notif,$data);
//    if ($ask->config->push_notifications == 1) {
//        NotificationWebPushNotifier();
//    }
    return $query;
}
function GetNotification($args = array()){
    global $ask, $db;
    $options  = array(
        "recipient_id" => 0,
        "type" => null,
    );

    $args         = array_merge($options, $args);
    $recipient_id = $args['recipient_id'];
    $type         = $args['type'];
    $data         = array();
    $t_notif      = T_NOTIFICATIONS;

    $limit = 20;
    if( $type == 'all' ) {
        $limit = 40;
    }

    if(isset($args['limit']) && !empty($args['limit'])){
        $limit = (int)$args['limit'];
    }

    if(isset($args['offset']) && !empty($args['offset'])){
        $db->where('id', (int)$args['offset'], '<');
    }

    if( $type == 'only_new'){
        $db->where('seen','0');
    }

    $db->where('recipient_id',$recipient_id);
    if ($type == 'new') {
        $data = $db->where('seen',0)->getValue($t_notif,'count(*)');
    }

    else{
        $query      = $db->orderBy('id','DESC')->get($t_notif,$limit);
        foreach ($query as $notif_data_row) {
            if(UserExists($notif_data_row->notifier_id) === true) {
                $data[] = ToArray($notif_data_row);
            }
        }
    }

    $db->where('recipient_id',$recipient_id);
    $db->where('time',(time() - 432000));
    $db->where('seen',0,'>');
    $db->delete($t_notif);

    return $data;
}
function GetDataFromSessionID($session_id, $platform = 'web') {
    global $sqlConnect;
    if (empty($session_id)) {
        return false;
    }
    $platform   = Secure($platform);
    $session_id = Secure($session_id);
    $data       = array();
    $query      = mysqli_query($sqlConnect, "SELECT * FROM " . T_APP_SESSIONS . " WHERE `session_id` = '{$session_id}' AND `platform` = '{$platform}' LIMIT 1");
    return mysqli_fetch_assoc($query);
}
function GetSessionDataFromUserID($user_id = 0) {
    global $sqlConnect;
    if (empty($user_id)) {
        return false;
    }
    $user_id = Secure($user_id);
    $time    = time() - 30;
    $query   = mysqli_query($sqlConnect, "SELECT * FROM " . T_APP_SESSIONS . " WHERE `user_id` = '{$user_id}' AND `platform` = 'web' AND `time` > $time LIMIT 1");
    return mysqli_fetch_assoc($query);
}
function LangsFromDB($lang = 'english') {
    global $sqlConnect;
    $data  = array();
    $query = mysqli_query($sqlConnect, "SELECT `lang_key`, `$lang` FROM " . T_LANGS);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[$fetched_data['lang_key']] = htmlspecialchars_decode($fetched_data[$lang]);
    }
    return $data;
}
function Time_Elapsed_String($ptime) {
    global $ask, $lang;
    $etime = time() - $ptime;
    if ($etime < 1) {
        return '0 seconds';
    }
    $a        = array(
        365 * 24 * 60 * 60 => __('year'),
        30 * 24 * 60 * 60 => __('month'),
        24 * 60 * 60 => __('day'),
        60 * 60 => __('hour'),
        60 => __('minute'),
        1 => __('second')
    );
    $a_plural = array(
        __('year') => __('years'),
        __('month') => __('months'),
        __('day') => __('days'),
        __('hour') => __('hours'),
        __('minute') => __('minutes'),
        __('second') => __('seconds')
    );
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            if ($ask->language_type == 'rtl') {
                $time_ago = __('time_ago') . ' ' . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            } else {
                $time_ago = $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . __('time_ago');
            }
            return $time_ago;
        }
    }
}
function QuestionData(&$question, $api = false){
    global $db,$ask,$lang;
    $user = UserData($question->user_id);

    if($user === false){
        return false;
    }
    $answer = '';
    $final_q = '';
    $question->post_type = GetQuestionType($question);

    if($api === true ){
        if($question->is_anonymously === 1){
            $question->type = 'anonymous';
        }else{
            $question->type = 'normal';
        }
        if(isset($question->photo)) {
            if ($question->photo !== NULL && !empty($question->photo)) {
                $question->type = 'photo_poll';
            }
        }

        $question->ask_user_data = [];
        if(isset($question->ask_user_id)) {
            if($question->ask_user_id > 0 && $question->ask_question_id > 0 ){
                $question->type = 'answer';
                $question->ask_user_data = UserData($question->ask_user_id);
            }
        }
        if(isset($question->ask_user_id)) {
            if($question->ask_user_id > 0 && $question->ask_question_id == 0 ){
                $question->type = 'asked';
                $question->ask_user_data = UserData($question->ask_user_id);
            }
        }
        $question->replay_user_data = [];
        if(isset($question->replay_user_id)) {
            if ($question->replay_user_id > 0 && $question->replay_question_id > 0) {
                $question->type = 'reply';
                $question->replay_user_data = UserData($question->replay_user_id);
            }
        }
        $question->shared_user_data = [];
        if(isset($question->shared_user_id)) {
            if ($question->shared_user_id > 0 && $question->shared_question_id > 0) {
                $question->type = 'share';
                $question->shared_user_data = UserData($question->shared_user_id);
            }
        }
    }

    $oreinal_question = PostMarkup($question->question);

    $question->oreinal_question = $oreinal_question;
    $question->decoded_oreinal_question = strip_tags(htmlspecialchars_decode($oreinal_question));

    $question->user_full_data = $user;
    $question->profile_user_id = $question->user_id;
    $question->user_full_name = $user->name;
    $question->user_username = $user->username;
    $question->user_avatar = $user->avatar;
    if($api === true){
        $question->user_full_data = $user;
    }
    if( $question->shared_user_id > 0 || $question->ask_user_id > 0 ){

        if( $question->shared_user_id > 0 ) {
            $is_shared_question_exists = $db->where('id',$question->shared_question_id)->getValue(T_QUESTIONS, 'count(*)');
            if($is_shared_question_exists){
                $ask_user = UserData($question->shared_user_id);
                $question->ask_user_username = $ask_user->username;
                $question->ask_user_name = $ask_user->name;
            }else{
                $question->ask_user_name = '';
            }
            $final_q = nl2br( $oreinal_question );
        }else if($question->ask_user_id > 0 &&  $question->ask_question_id == 0 ) {
            $ask_user = UserData($question->ask_user_id);
            $question->user_full_name = $ask_user->name;
            $question->user_username = $ask_user->username;
            $question->ask_user_username = $user->username;
            $question->ask_user_name = $user->name;
            $question->user_avatar = $ask_user->avatar;

            $final_q = nl2br( $oreinal_question );
        }else if($question->ask_user_id > 0 &&  $question->ask_question_id > 0 ) {
            $ask_user = UserData($question->ask_user_id);
            $question->ask_user_username = $ask_user->username;
            $question->ask_user_name = $ask_user->name;
            $q = $db->where('id',$question->ask_question_id)->getOne(T_QUESTIONS,array('question'));
            $a = $db->where('id',$question->id)->getOne(T_QUESTIONS,array('question'));
            $question->oreinal_question = nl2br(PostMarkup($q->question));
            $question->answer = nl2br(PostMarkup($a->question));
        }else{
            $question->ask_user_name = '';
        }

    }else{
        $final_q = $oreinal_question;
        $question->ask_user_name = '';
    }

    $question->question = nl2br(PostMarkup($final_q));

    $question->like_count = $db->where('question_id',$question->id)->getValue(T_LIKES, 'count(*)');
    $question->like_count = ( $question->like_count > 0 ) ? $question->like_count : '';
    $question->url  = UrlLink('post/' . $question->id);

    $question->IsReported = false;

    $question->is_liked = false;
    $question->is_voted = false;
    $question->vote_count = 0;

    $question->progress_cont1 = 0;
    $question->progress_cont2 = 0;

    if (IS_LOGGED == true) {
        if( $question->ask_user_id == 0 && $question->ask_question_id == 0 && $ask->user->id == $question->user_id){
            $question->isowner = true;
        }

        else if( $question->replay_user_id > 0 && $question->replay_question_id > 0 ){
            $question->isowner = true;
        }

        else if( $question->ask_user_id > 0 && $question->ask_question_id > 0 && $ask->user->id == $question->user_id ) {
            $question->isowner = true;
        }

        else{
            if( $question->ask_user_id > 0 && $question->ask_question_id == 0 && $ask->user->id == $question->ask_user_id) {
                $question->isowner = true;
            }else{
                $question->isowner = false;
            }
        }

        $question->is_liked = IsQuestionLiked($ask->user->id, $question->id);
        $question->is_voted = IsQuestionVoted($question->id);

        $vote_exist  = $db->where('question_id', $question->id)->where('user_id',$ask->user->id)->getOne(T_QUESTIONS_VOTES,array('count(*) as total'));
        if ($vote_exist->total > 0) {
            $question->vote_count = $vote_exist->total;
        }
        
        $question->IsReported = IsQuestionReported($ask->user->id, $question->id);
    }

    if( isset( $question->photo ) ) {
        if ($question->photo !== '') {
            $photo_poll = json_decode($question->photo);
            $question->choice1_id = (isset($photo_poll->choice1_id)) ? $photo_poll->choice1_id : '';
            $question->choice1_url = (isset($photo_poll->choice1_url)) ? $photo_poll->choice1_url : '';
            $question->choice2_id = (isset($photo_poll->choice2_id)) ? $photo_poll->choice2_id : '';
            $question->choice2_url = (isset($photo_poll->choice2_url)) ? $photo_poll->choice2_url : '';

            $Percentages = GetVotePercentages($question->choice1_id, $question->choice2_id);
            $question->progress_cont1 = $Percentages[$question->choice1_id];
            $question->progress_cont2 = $Percentages[$question->choice2_id];
        }
    }

    if( $question->type == 'photo_poll' && $question->ask_question_id > 0 ) {
        $pho = $db->where('id',$question->ask_question_id)->getOne(T_QUESTIONS,array('photo'));
        if( isset($pho->photo) && !empty($pho->photo) ){
            $photo_poll = json_decode($pho->photo);
            $question->choice1_id = (isset($photo_poll->choice1_id)) ? $photo_poll->choice1_id : '';
            $question->choice1_url = (isset($photo_poll->choice1_url)) ? $photo_poll->choice1_url : '';
            $question->choice2_id = (isset($photo_poll->choice2_id)) ? $photo_poll->choice2_id : '';
            $question->choice2_url = (isset($photo_poll->choice2_url)) ? $photo_poll->choice2_url : '';

            $Percentages = GetVotePercentages($question->choice1_id, $question->choice2_id);
            $question->progress_cont1 = $Percentages[$question->choice1_id];
            $question->progress_cont2 = $Percentages[$question->choice2_id];
            
        }
    }

    $question->is_replay = false;

    global $config;
    $censored_words = @explode(",", $config['censored_words']);
    foreach ($censored_words as $censored_word) {
        $censored_word = trim($censored_word);
        $question->question        = str_replace($censored_word, '****', $question->question);
    }


    return $question;
}
function ImportImageFromFile($media, $custom_name = '_url_image') {
    if (empty($media)) {
        return false;
    }
    if (!file_exists('upload/photos/' . date('Y'))) {
        mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    $extension = 0; //image_type_to_extension($size[2]);
    if (empty($extension)) {
        $extension = '.jpg';
    }
    $dir               = 'upload/photos/' . date('Y') . '/' . date('m');
    $file_dir          = $dir . '/' . GenerateKey() . $custom_name . $extension;
    $fileget           = file_get_contents($media);
    if (!empty($fileget)) {
        $importImage = @file_put_contents($file_dir, $fileget);
    }
    if (file_exists($file_dir)) {
        $upload_s3 = UploadToS3($file_dir);
        $check_image = getimagesize($file_dir);
        if (!$check_image) {
            unlink($file_dir);
        }
        return $file_dir;
    } else {
        return false;
    }
}
function IsFollowing($following_id, $user_id = 0) {
    global $sqlConnect, $ask;
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($following_id) || !is_numeric($following_id) || $following_id < 0) {
        return false;
    }
    if ((empty($user_id) || !is_numeric($user_id) || $user_id < 0)) {
        $user_id = $ask->user->id;
    }
    $following_id = Secure($following_id);
    $user_id      = Secure($user_id);
    $query        = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_FOLLOWERS . " WHERE `user_id` = {$following_id} AND `follower_id` = {$user_id}");
    return (Sql_Result($query, 0) == 1) ? true : false;
}
function RegisterFollow($following_id = 0, $followers_id = 0) {
    global $ask, $sqlConnect;
    if( IsAdmin() == false ){
        return false;
    }
    if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
        return false;
    }
    if (!is_array($followers_id)) {
        $followers_id = array($followers_id);
    }

    foreach ($followers_id as $follower_id) {
        if (!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1) {
            continue;
        }


//        if (IsBlocked($following_id)) {
//            continue;
//        }
        $following_id = Secure($following_id);
        $follower_id  = Secure($follower_id);
        if (IsFollowing($following_id, $follower_id) === true) {
            continue;
        }
        $follower_data  = UserData($follower_id);
        $following_data = UserData($following_id);
        if (empty($follower_data->id) || empty($following_data->id)) {
            continue;
        }

        if ($following_id == $follower_id) {
            continue;
        }

        $query = mysqli_query($sqlConnect, " INSERT INTO " . T_FOLLOWERS . " (`user_id`,`follower_id`,`time`) VALUES ({$following_id},{$follower_id},".time().")");
        if ($query) {
            $notif_data = array(
                'notifier_id' => $follower_id,
                'recipient_id' => $following_id,
                'type' => 'followed_u',
                'url' => ('@' . $following_data->username),
                'time' => time()
            );

            Notify($notif_data);
        }
    }
    return true;
}
function ShortText($text = "", $len = 100) {
    if (empty($text) || !is_string($text) || !is_numeric($len) || $len < 1) {
        return "****";
    }
    if (strlen($text) > $len) {
        $text = mb_substr($text, 0, $len, "UTF-8") . "..";
    }
    return $text;
}
function url_domain($url) {
    $host = @parse_url($url, PHP_URL_HOST);
    if (!$host) {
        $host = $url;
    }
    if (substr($host, 0, 4) == "www.") {
        $host = substr($host, 4);
    }
    if (strlen($host) > 50) {
        $host = substr($host, 0, 47) . '...';
    }
    return $host;
}
function GetAd($type, $admin = true) {
    global $db;
    $type      = Secure($type);
    $query_one = "SELECT `code` FROM " . T_ADS . " WHERE `placement` = '{$type}'";
    if ($admin === false) {
        $query_one .= " AND `active` = '1'";
    }
    $fetched_data = $db->rawQuery($query_one);
    if (!empty($fetched_data)) {
        return htmlspecialchars_decode($fetched_data[0]->code);
    }
    return '';
}
function custom_design($a = false,$code = array()){
    global $ask;
    $theme       = $ask->config->theme;
    $data        = array();
    $custom_code = array(
        "themes/$theme/js/header.js",
        "themes/$theme/js/footer.js",
        "themes/$theme/css/custom.style.css",
    );

    if ($a == 'get') {
        foreach ($custom_code as $key => $filepath) {
            if (is_readable($filepath)) {
                $data[$key] = file_get_contents($filepath);
            }
            else{
                $data[$key] = "/* \n Error found while loading: Permission denied in $filepath \n*/";
            }
        }
    }

    else if($a == 'save' && !empty($code)){
        foreach ($code as $key => $content) {
            $filepath = $custom_code[$key];

            if (is_writable($filepath)) {
                @file_put_contents($custom_code[$key],$content);
            }

            else{
                $data[$key] = "Permission denied: $filepath is not writable";
            }
        }
    }

    return $data;
}
function UploadLogo($data = array()) {
    global $ask;
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = Secure($data['file']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = Secure($data['name']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = Secure($data['name']);
    }
    if (empty($data)) {
        return false;
    }
    $allowed           = 'png';
    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return false;
    }
    $logo_name = 'logo';
    if (!empty($data['light-logo'])) {
        $logo_name = 'logo-light';
    }
    if (!empty($data['favicon'])) {
        $logo_name = 'icon';
    }
    $dir      = "themes/" . $ask->config->theme . "/img/";
    $filename = $dir . "$logo_name.png";
    if (move_uploaded_file($data['file'], $filename)) {
        return true;
    }
}
function CheckIfUserCanRegister($num = 10) {
    global $ask, $sqlConnect;
    if (IS_LOGGED == true) {
        return false;
    }
    $ip = get_ip_address();
    if (empty($ip)) {
        return true;
    }
    $time      = time() - 3200;
    $query     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_USERS . " WHERE `ip_address` = '{$ip}' AND `last_active` > {$time}");
    $sql_query = mysqli_fetch_assoc($query);
    if ($sql_query['count'] > $num) {
        return false;
    }
}
function CheckIfUserCanPost($num = 10) {
    global $ask, $sqlConnect;
    if (IS_LOGGED == false) {
        return false;
    }
    $user_id = Secure($ask->user->id);
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 1) {
        return false;
    }
    $time      = time() - 3200;
    $query     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_QUESTIONS . " WHERE `user_id` = {$user_id} AND `time` > {$time}");
    $sql_query = mysqli_fetch_assoc($query);
    if ((int)$sql_query['count'] > (int)$num) {
        return false;
    }
}
function GetThemes() {
    global $ask;
    $themes = glob('themes/*', GLOB_ONLYDIR);
    return $themes;
}
function IsFollowRequested($following_id = 0, $follower_id = 0) {
    global $sqlConnect, $ask;
    if (IS_LOGGED == false) {
        return false;
    }
    if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
        return false;
    }
    if ((!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1)) {
        $follower_id = $ask->user->id;
    }
    if (!is_numeric($follower_id) or $follower_id < 1) {
        return false;
    }
    $following_id = Secure($following_id);
    $follower_id  = Secure($follower_id);
    $query        = "SELECT `id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$follower_id} AND `user_id` = {$following_id} AND `active` = '0'";
    $sql_query    = mysqli_query($sqlConnect, $query);
    if (mysqli_num_rows($sql_query) > 0) {
        return true;
    }
}
function AutoFollow($user_id = 0) {
    global $ask, $db;
    if (empty($user_id)) {
        return false;
    }
    if (!is_numeric($user_id) || $user_id == 0) {
        return false;
    }
    $get_users = explode(',', $ask->config->auto_friend_users);
    if (!empty($get_users)) {
        foreach ($get_users as $key => $user) {
            $user = trim($user);
            $user = Secure($user);
            $getUserID = UserIdFromUsername($user);
            if (!empty($getUserID)) {
                $registerFollow = RegisterFollow($getUserID, $user_id);
                if ($registerFollow) {
                    return true;
                }
            }
        }
    } else {
        return false;
    }
}
function vrequest_exists(){
    global $db,$ask;
    if (!IS_LOGGED) {
        return false;
    }

    $user    = $ask->user->id;
    return ($db->where("user_id",$user)->getValue(VERIF_REQUESTS,"count(*)") > 0);
}
function RunInBackground($data = array()) {
    ob_end_clean();
    header("Content-Encoding: none");
    header("Connection: close");
    ignore_user_abort();
    ob_start();
    if (!empty($data)) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();
    flush();
    session_write_close();
    if (is_callable('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
}
function GetQestionByID($id){
    global $ask, $db;

    if (empty($id)) {
        return false;
    }

    $data = $db->where('id', $id)->getOne(T_QUESTIONS, array('*'));
    $data->url  = UrlLink('post/' . $data->id);
    return $data;
}
function GetQuestionDeleteIds($question_id){
    global $db;
    $ids = array();
    $ids[] = $question_id;

    $answers = $db->where('ask_question_id',$question_id)->get(T_QUESTIONS,null,array('id'));
    foreach ($answers as $a){
        $ids[] = $a->id;
        $answers_reply = $db->where('replay_question_id',$a->id)->get(T_QUESTIONS,null,array('id'));
        foreach ($answers_reply as $r){
            $ids[] = $r->id;
        }
    }

    $shares = $db->where('shared_question_id',$question_id)->get(T_QUESTIONS,null,array('id'));
    foreach ($shares as $s){
        $ids[] = $s->id;
        $shares_answers = $db->where('ask_question_id',$s->id)->get(T_QUESTIONS,null,array('id'));
        foreach ($shares_answers as $sa){
            $ids[] = $sa->id;
            $shares_answers_reply = $db->where('replay_question_id',$sa->id)->get(T_QUESTIONS,null,array('id'));
            foreach ($shares_answers_reply as $sar){
                $ids[] = $sar->id;
            }
        }
    }
    return $ids;
}
function DeleteQuestion($question_id){
    global $db, $ask;
    $ids = GetQuestionDeleteIds($question_id);
    $question_data = $db->where('id', $question_id)->getOne(T_QUESTIONS,array('*'));
    //delete question data
    $delete_question = $db->where('id', $ids, 'IN')->delete(T_QUESTIONS);
    if ($delete_question) {
        //delete question photo if any
        if( $question_data->type == 'photo_poll' ){
            if ($question_data->photo !== '') {
                $photo_poll = json_decode($question_data->photo);
                if( !empty($photo_poll) ){
                    if(isset($photo_poll->choice1_img)){
                        @unlink($ask->base_path . str_replace('/' , $ask->directory_separator ,  $photo_poll->choice1_img ));
                        if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on')) {
                            DeleteFromToS3($photo_poll->choice1_img);
                        }
                    }
                    if(isset($photo_poll->choice2_img)){
                        @unlink($ask->base_path . str_replace('/' , $ask->directory_separator ,  $photo_poll->choice2_img ));
                        if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on')) {
                            DeleteFromToS3($photo_poll->choice2_img);
                        }
                    }
                }
            }
        }

        // delete notification
        $delete_question = $db->where('question_id', $ids, 'IN')->delete(T_LIKES);
        $delete_question .= $db->where('question_id', $ids, 'IN')->delete(T_QUESTIONS_VOTES);
        $delete_question .= $db->where('question_id', $ids, 'IN')->delete(T_REPORTS);
        $delete_question .= $db->where('question_id', $ids, 'IN')->OrWhere('replay_id', $ids, 'IN')->delete(T_NOTIFICATIONS);
        if( $delete_question ){
            return true;
        }else{
            return false;
        }
        //delete answers
        //delete likes
        //delete polldata

    }else{
        return false;
    }
}
function GetQuestionType($question){
    global $ask;
    $type = array();
    if($question->is_anonymously === 1){
        $type['anonymous'] = 'anonymous';
    }else{
        $type['normal'] = 'normal';
    }
    if(isset($question->photo)) {
        if ($question->photo !== NULL && !empty($question->photo)) {
            $type['photo_poll'] = 'photo_poll';
        }
    }
    if(isset($question->ask_user_id)) {
        if($question->ask_user_id > 0 && $question->ask_question_id > 0 ){
            $type['answer'] = 'answer';
        }
    }
    if(isset($question->ask_user_id)) {
        if($question->ask_user_id > 0 && $question->ask_question_id == 0 ){
            $type['asked'] = 'asked';
        }
    }
    if(isset($question->replay_user_id)) {
        if ($question->replay_user_id > 0 && $question->replay_question_id > 0) {
            $type['reply'] = 'reply';
        }
    }
    if(isset($question->shared_user_id)) {
        if ($question->shared_user_id > 0 && $question->shared_question_id > 0) {
            $type['share'] = 'share';
        }
    }

    if (IS_LOGGED == true) {
        if(isset($question->ask_user_id)) {
            if ($question->ask_user_id == 0 && $question->ask_question_id == 0 && $ask->user->id == $question->user_id) {
                $type['owner'] = 'owner';
            }
        }
        if(isset($question->ask_user_id)) {
            if ($question->ask_user_id > 0 && $question->ask_question_id > 0 && $ask->user->id == $question->user_id) {
                $type['owner'] = 'owner';
            }
        }
        if(isset($question->shared_user_id)) {
            if ($question->shared_user_id > 0 && $question->shared_question_id > 0 && $ask->user->id == $question->user_id) {
                $type['owner'] = 'owner';
            }
        }
        if(isset($question->replay_user_id)) {
            if ($question->replay_user_id > 0 && $question->replay_question_id > 0 && $ask->user->id == $question->user_id) {
                $type['owner'] = 'owner';
            }
        }
    }

    return $type;
}
function BtnAnswerItVisible($question){
    global $ask,$db;
    $show = false;
    $haystack = $question->post_type;

    if(( in_array('anonymous',$haystack) || in_array('normal',$haystack) || in_array('photo_poll',$haystack) ) && !in_array('answer',$haystack) && !in_array('reply',$haystack) && !in_array('asked',$haystack) && !in_array('owner',$haystack)){
        $show = true;
    }
    //var_dump($question->post_type);
    return $show;
}
function BtnReplyItVisible($question){
    global $ask,$db;
    $show = false;
    $haystack = $question->post_type;

    if(( in_array('anonymous',$haystack) || in_array('normal',$haystack) || in_array('photo_poll',$haystack) ) && in_array('answer',$haystack)){
        $show = true;
    }

    return $show;


}
function OGMetaTags(){
    global $ask;
    
    if(!empty($ask->config->site_url.'/post/')) {
        echo '<meta property="og:title" content="' . $user->username . '">';
        echo '<meta property="og:image" content="' .  $ask->user_avatar .'">';
        echo '<meta property="og:image:width" content="500">';
        echo '<meta property="og:image:height" content="500">';
        echo '<meta property="og:description" content="' . $ask->users->about . '">';
    }
    else{
      
        echo '<meta property="og:title" content="' . $ask->config->title . '">';
        echo '<meta property="og:image" content="' . $ask->config->theme_url .'/img/logo.png">';
        echo '<meta property="og:description" content="' . $ask->config->description . '">';
        echo '<meta property="og:title" content=" '. $user_data->username .' " />';
        echo '<meta property="og:type" content="article" />';
        echo '<meta property="og:url" content="' . $ask->site_url.'/@'.$username . ' " />';
        echo '<meta property="og:image" content=" ' . $user_data->avatar . ' " />';
        echo '<meta property="og:image:secure_url" content=" ' . $ask->users->user_avatar . ' " />';
        echo '<meta property="og:description" content=" ' . $user_data->description . ' " />';
    }
}
function CreatePayment($data){
    global $db;
    if(empty($data)){
        return false;
    }

    return $db->insert(T_PAYMENTS, $data);
}
function connect_to_url($url = '', $config = array()) {
    if (empty($url)) {
        return false;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    if (!empty($config['POST'])) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $config['POST']);
    }
    if (!empty($config['bearer'])) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $config['bearer']
        ));
    }
    //execute the session
    $curl_response = curl_exec($curl);
    //finish off the session
    curl_close($curl);
    return $curl_response;
}
function size_format($bytes) {

    $size = array('1' => '0MB',
                  '2000000' => '2MB',
                  '6000000' => '6MB',
                  '12000000' => '12MB',
                  '24000000' => '24MB',
                  '48000000' => '48MB',
                  '96000000' => '96MB',
                  '256000000' => '256MB',
                  '512000000' => '512MB',
                  '1000000000' => '1GB',
                  '10000000000' => '10GB');
    return $size[$bytes];
}
function ChatExists($id) {
    global $db, $ask;

  if (!empty($id)){
     $chat_exits = $db->where("user_one", $ask->user->id)->where("user_two", $id)->getValue(T_CHATS, 'count(*)');
            if (!empty($chat_exits)) {
                $db->where("user_two", $ask->user->id)->where("user_one", $id)->update(T_CHATS, array('time' => time()));
                $db->where("user_one", $ask->user->id)->where("user_two", $id)->update(T_CHATS, array('time' => time()));
                if ($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)') == 0) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            } else {
                $db->insert(T_CHATS, array('user_one' => $ask->user->id, 'user_two' => $id,'time' => time()));
                if (empty($db->where("user_two", $ask->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)'))) {
                    $db->insert(T_CHATS, array('user_two' => $ask->user->id, 'user_one' => $id,'time' => time()));
                }
            }

        }

            return $chat_exits;
  
}
function UserExists($id) {
    global $db, $ask;
    if (empty($id)){
        return false;
    }
    $user_exits = $db->where("id", $id)->getValue(T_USERS, 'count(*)');
    if ($user_exits > 0) {
        return true;
    }else{
        return false;
    }
}