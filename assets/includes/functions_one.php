<?php
require_once('app_start.php');
use Aws\S3\S3Client;
use Twilio\Rest\Client;
// Paypal methods
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

function __($key){
    global $ask;
    if(property_exists($ask->lang,$key)){
        return $ask->lang->$key;
    }else{
        return $ask->default_lang->$key;
    }
}
function GetConfig() {
    global $db;
    $data  = array();
    $configs = $db->get(T_CONFIG);
    foreach ($configs as $key => $config) {
        $data[$config->name] = $config->value;
    }
    return $data;
}
function UserData($user_id = 0, $options = array()) {
    global $db, $ask, $lang, $countries_name , $user;
    if (!empty($options['data'])) {
        $fetched_data   = $user_id;
    }

    else {
        $fetched_data   = $db->where('id', $user_id)->getOne(T_USERS);
    }

    if (empty($fetched_data)) {
        return false;
    }
    $fetched_data->name   = $fetched_data->username;
    $fetched_data->avatar_path = $fetched_data->avatar;
    $fetched_data->cover_path  = $fetched_data->cover;
    $fetched_data->avatar = GetMedia($fetched_data->avatar);
    $fetched_data->cover  = GetMedia($fetched_data->cover);
    $fetched_data->url    = UrlLink('@' . $fetched_data->username);
    $fetched_data->about_decoded = br2nl($fetched_data->about);

    if (!empty($fetched_data->first_name)) {
        $fetched_data->name = $fetched_data->first_name . ' ' . $fetched_data->last_name;
    }

    if (empty($fetched_data->about)) {
        $fetched_data->about = '';
    }
    //$fetched_data->balance  = number_format($fetched_data->balance, 2);
    $fetched_data->name_v   = $fetched_data->name;
    if ($fetched_data->verified == 1 ) {
        $fetched_data->name_v = $fetched_data->name . ' <i class="fa fa-check-circle fa-fw verified"></i>';
    }
    if (!empty($countries_name[$fetched_data->country_id])) {
    $fetched_data->country_name  = $countries_name[$fetched_data->country_id];
   }
    @$fetched_data->gender_text  = ($fetched_data->gender == 'male') ? __('male') : __('female');

    @$fetched_data->followers = $db->where('user_id', $fetched_data->id)->getValue(T_FOLLOWERS, "count(*)");
    @$fetched_data->following = $db->where('follower_id', $fetched_data->id)->getValue(T_FOLLOWERS, "count(*)");
    @$fetched_data->questions = $db->where('user_id', $fetched_data->id)->getValue(T_QUESTIONS, "count(*)");
    @$fetched_data->is_following = false;

    $is_followed = $db->where('user_id', $fetched_data->id)->where('follower_id', $user->id)->getValue(T_FOLLOWERS, "count(*)");

    if ($is_followed > 0) {
        @$fetched_data->is_following = true;
    }

    return $fetched_data;
}
function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}
function GetMedia($media = '', $is_upload = false){
    global $ask;
    if (empty($media)) {
        return '';
    }

    $media_url     = $ask->config->site_url . '/' . $media;
    if ($ask->config->s3_upload == 'on' && $is_upload == false) {
        $media_url = "https://" . $ask->config->s3_bucket_name . ".s3.amazonaws.com/" . $media;
    } else if ($ask->config->ftp_upload == "on") {
        return addhttp($ask->config->ftp_endpoint) . '/' . $media;
    }
    else if ($ask->config->spaces == 'on') {

        if (empty($ask->config->spaces_key) || empty($ask->config->spaces_secret) || empty($ask->config->space_region) || empty($ask->config->space_name)) {
            return $media_url;
        }
        return  'https://' . $ask->config->space_name . '.' . $ask->config->space_region . '.digitaloceanspaces.com/' . $media;
    }
    else if ($ask->config->cloud_upload == 'on') {
        return 'https://storage.cloud.google.com/'. $ask->config->cloud_bucket_name . '/' . $media;
    } else {
       return $media_url; 
    }

}

function UserActive($user_id = 0) {
    global $db;
    $db->where('active', '1');
    $db->where('id', Secure($user_id));
    return ($db->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function CreateMainSession() {
    $hash = substr(sha1(rand(1111, 9999)), 0, 70);
    if (!empty($_SESSION['main_hash_id'])) {
        $_SESSION['main_hash_id'] = $_SESSION['main_hash_id'];
        return $_SESSION['main_hash_id'];
    }
    $_SESSION['main_hash_id'] = $hash;
    return $hash;
}
function CheckMainSession($hash = '') {
    if (!isset($_SESSION['main_hash_id']) || empty($_SESSION['main_hash_id'])) {
        return false;
    }
    if (empty($hash)) {
        return false;
    }
    if ($hash == $_SESSION['main_hash_id']) {
        return true;
    }
    return false;
}

function UsernameExists($username = '') {
    global $db;
    return ($db->where('username', Secure($username))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function UserEmailExists($email = '') {
    global $db;
    return ($db->where('email', Secure($email))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function SendMessage($data = array()) {
    global $ask, $mail;
    $email_from      = $data['from_email'] = Secure($data['from_email']);
    $to_email        = $data['to_email'] = Secure($data['to_email']);
    $subject         = $data['subject'];
    $data['charSet'] = Secure($data['charSet']);

    if ($ask->config->smtp_or_mail == 'mail') {
        $mail->IsMail();
    }

    else if ($ask->config->smtp_or_mail == 'smtp') {
        $mail->isSMTP();
        $mail->Host        = $ask->config->smtp_host;
        $mail->SMTPAuth    = true;
        $mail->Username    = $ask->config->smtp_username;
        $mail->Password    = $ask->config->smtp_password;
        $mail->SMTPSecure  = $ask->config->smtp_encryption;
        $mail->Port        = $ask->config->smtp_port;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }

    else {
        return false;
    }

    $mail->IsHTML($data['is_html']);
    $mail->setFrom($data['from_email'], $data['from_name']);
    $mail->addAddress($data['to_email'], $data['to_name']);
    $mail->Subject = $data['subject'];
    $mail->CharSet = $data['charSet'];
    $mail->MsgHTML($data['message_body']);
    if ($mail->send()) {
        $mail->ClearAddresses();
        return true;
    }
}

function SendSMSMessage($to, $message) {
    global $ask;
    if (empty($to)) {
        return false;
    }
    if (!empty($ask->config->sms_twilio_username) && !empty($ask->config->sms_twilio_password) && !empty($ask->config->sms_t_phone_number)) {
        include_once('assets/import/twilio/vendor/autoload.php');
        $account_sid = $ask->config->sms_twilio_username;
        $auth_token  = $ask->config->sms_twilio_password;
        $to          = secure($to);
        $client      = new Client($account_sid, $auth_token);
        try {
            $send = $client->account->messages->create($to, array(
                'from' => $ask->config->sms_t_phone_number,
                'body' => $message
            ));
            if ($send) {
                return true;
            }
        }
        catch (Exception $e) {
            print_r($e->getMessage());
        }
        return false;
    }
    return false;
}
function VerifyIP($username = '') {
    global $ask, $db;
    if (empty($username)) {
        return false;
    }
    if ($ask->config->login_auth == 0) {
        return true;
    }
    $getuser = UserData($username);
    $get_ip = '95.12.11.21';//get_ip_address();
    $getIpInfo = fetchDataFromURL("http://ip-api.com/json/$get_ip");
    $getIpInfo = json_decode($getIpInfo, true);
    if ($getIpInfo['status'] == 'success' && !empty($getIpInfo['regionName']) && !empty($getIpInfo['countryCode']) && !empty($getIpInfo['timezone']) && !empty($getIpInfo['city'])) {
        $create_new = false;
        $_SESSION['last_login_data'] = $getIpInfo;
        if (empty($getuser->last_login_data)) {
            $create_new = true;
        } else {
            $lastLoginData = unserialize($getuser->last_login_data);
            if (($getIpInfo['regionName'] != $lastLoginData['regionName']) || ($getIpInfo['countryCode'] != $lastLoginData['countryCode']) || ($getIpInfo['timezone'] != $lastLoginData['timezone']) || ($getIpInfo['city'] != $lastLoginData['city'])) {
                // send email
                $code = rand(111111, 999999);
                $hash_code = md5($code);
                $email['username'] = $getuser->name;
                $email['countryCode'] = $getIpInfo['countryCode'];
                $email['timezone'] = $getIpInfo['timezone'];
                $email['email'] = $getuser->email;
                $email['ip_address'] = $get_ip;
                $email['code'] = $code;
                $email['city'] = $getIpInfo['city'];
                $email['date'] = date("Y-m-d h:i:sa");
                $update_code =  $db->where('id', $username)->update(T_USERS, array('email_code' => $hash_code));
                $email_body = LoadPage("emails/unusual-login", $email);
                $send_message_data       = array(
                    'from_email' => $ask->config->email,
                    'from_name' => $ask->config->name,
                    'to_email' => $getuser->email,
                    'to_name' => $getuser->name,
                    'subject' => 'Please verify that it\'s you',
                    'charSet' => 'utf-8',
                    'message_body' => $email_body,
                    'is_html' => true
                );
                $send = SendMessage($send_message_data);
                if ($send && !empty($_SESSION['last_login_data'])) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
        if ($create_new == true) {
            $lastLoginData = serialize($getIpInfo);
            $update_user = $db->where('id', $username)->update(T_USERS, array('last_login_data' => $lastLoginData));
            return true;
        }
        return false;
    } else {
        return true;
    }
}
function TwoFactor($username = '') {
    global $ask, $db;
    if (empty($username)) {
        return true;
    }
    if ($ask->config->two_factor == 0) {
        return true;
    }
    $getuser = UserData($username);
    if ($getuser->two_factor == 0 || $getuser->two_factor_verified == 0) {
        return true;
    }
    $code = rand(111111, 999999);
    $hash_code = md5($code);
    $update_code =  $db->where('id', $username)->update(T_USERS, array('email_code' => $hash_code));
    $message = "Your confirmation code is: $code";
    if (!empty($getuser->phone_number) && ($ask->config->two_factor_type == 'both' || $ask->config->two_factor_type == 'phone')) {
        $send_message = SendSMSMessage($getuser->phone_number, $message);
    }
    if ($ask->config->two_factor_type == 'both' || $ask->config->two_factor_type == 'email') {
        $send_message_data       = array(
            'from_email' => $ask->config->email,
            'from_name' => $ask->config->name,
            'to_email' => $getuser->email,
            'to_name' => $getuser->name,
            'subject' => 'Please verify that it\'s you',
            'charSet' => 'utf-8',
            'message_body' => $message,
            'is_html' => true
        );
        $send = SendMessage($send_message_data);
    }
    return false;
}
function GetTerms() {
    global $db;
    $data  = array();
    $terms = $db->get(T_TERMS);
    foreach ($terms as $key => $term) {
        $data[$term->type] = $term->text;
    }
    return $data;
}
function IsAdmin() {
    global $ask;
    if (IS_LOGGED == false) {
        return false;
    }
    if ($ask->user->admin == 1) {
        return true;
    }
    return false;
}
use Google\Cloud\Storage\StorageClient;
function UploadToS3($filename, $config = array()) {
    global $ask;

    if ($ask->config->s3_upload != 'on' && $ask->config->ftp_upload != 'on' && $ask->config->spaces != 'on' && $ask->config->cloud_upload != 'on') {
        return false;
    }
    if ($ask->config->ftp_upload == "on") {
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($ask->config->ftp_host, false, $ask->config->ftp_port);
        $login = $ftp->login($ask->config->ftp_username, $ask->config->ftp_password);
        if ($login) {
            if (!empty($ask->config->ftp_path)) {
                if ($ask->config->ftp_path != "./") {
                    $ftp->chdir($ask->config->ftp_path);
                }
            }
            $file_path = substr($filename, 0, strrpos( $filename, '/'));
            $file_path_info = explode('/', $file_path);
            $path = '';
            if (!$ftp->isDir($file_path)) {
                foreach ($file_path_info as $key => $value) {
                    if (!empty($path)) {
                        $path .= '/' . $value . '/' ;
                    } else {
                        $path .= $value . '/' ;
                    }
                    if (!$ftp->isDir($path)) {
                        $mkdir = $ftp->mkdir($path);
                    }
                }
            }
            $ftp->chdir($file_path);
            $ftp->pasv(true);
            if ($ftp->putFromPath($filename)) {
                if (empty($config['delete'])) {
                    if (empty($config['amazon'])) {
                        @unlink($filename);
                    }
                }
                $ftp->close();
                return true;
            }
            $ftp->close();
        }
    } 
    else if ($ask->config->spaces == 'on' && !empty($ask->config->spaces_key) && !empty($ask->config->spaces_secret) && !empty($ask->config->space_name) && !empty($ask->config->space_region)) {
        include_once("assets/import/spaces/spaces.php");
        $key = $ask->config->spaces_key;
        $secret = $ask->config->spaces_secret;
        $space_name = $ask->config->space_name;
        $region = $ask->config->space_region;
        $space = new SpacesConnect($key, $secret, $space_name, $region);
        $upload = $space->UploadFile($filename, "public");
        if ($upload) {
            if (empty($config['delete'])) {
                if ($space->DoesObjectExist($filename)) {
                    if (empty($config['amazon'])) {
                        @unlink($filename);
                    }
                    return true;
                }
            } else {
                return true;
            }
            return true;
        }
    }
      else if ($ask->config->cloud_upload == 'on') {
        require_once 'assets/libraries/cloud/vendor/autoload.php';

        try {
            $storage = new StorageClient([
               'keyFilePath' =>$ask->config->cloud_file_path 
            ]);
            // set which bucket to work in
            $bucket = $storage->bucket($ask->config->cloud_bucket_name);
            $fileContent = file_get_contents($filename);

            // upload/replace file 
            $storageObject = $bucket->upload(
                                    $fileContent,
                                    ['name' => $filename]
                            );
            if (!empty($storageObject)) {
                if (empty($config['delete'])) {
                    if (empty($config['amazon'])) {
                        @unlink($filename);
                    }
                }
                return true;
            }
        } catch (Exception $e) {
            // maybe invalid private key ?
            // print $e;
            // exit();
            return false;
        }
    }

    else {
        $s3Config = (
            empty($ask->config->amazone_s3_key) ||
            empty($ask->config->amazone_s3_s_key) ||
            empty($ask->config->region) ||
            empty($ask->config->s3_bucket_name)
        );

        if ($s3Config){
            return false;
        }

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $ask->config->region,
            'credentials' => [
                'key'    => $ask->config->amazone_s3_key,
                'secret' => $ask->config->amazone_s3_s_key,
            ]
        ]);

        $s3->putObject([
            'Bucket' => $ask->config->s3_bucket_name,
            'Key'    => $filename,
            'Body'   => fopen($filename, 'r+'),
            'ACL'    => 'public-read',
        ]);

        if (empty($config['delete'])) {
            if ($s3->doesObjectExist($ask->config->s3_bucket_name, $filename)) {
                if (empty($config['amazon'])) {
                    @unlink($filename);
                }
                return true;
            }
        }

        else {
            return true;
        }
    }
}
function ShareFile($data = array(), $type = 0) {
    global $ask, $mysqli;
    $allowed = '';
    if (!file_exists('upload/files/' . date('Y'))) {
        @mkdir('upload/files/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/files/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/files/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y'))) {
        @mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/videos/' . date('Y'))) {
        @mkdir('upload/videos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/videos/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/videos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/sounds/' . date('Y'))) {
        @mkdir('upload/sounds/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/sounds/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/sounds/' . date('Y') . '/' . date('m'), 0777, true);
    }
     
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = $data['file'];
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
     if ($ask->config->fileSharing == 'on') {
        if (isset($data['types'])) {
            $allowed = $data['types'];
        } else {
            $allowed = $ask->config->allowedExtenstion;
        }
    } else {
        $allowed = 'jpg,png,jpeg,gif,mp4,m4v,webm,flv,mov,mpeg,mp3,wav';
    }
    // $allowed           = 'jpg,png,jpeg,gif,mp4,mp3,wav,text/plain';
    // if (!empty($data['allowed'])) {
    //     $allowed  = $data['allowed'];
    // }
    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);

    if (!in_array($file_extension, $extension_allowed)) {
        return array(
            'error' => 'File format not supported'
        );
    }
    if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
        $folder   = 'photos';
        $fileType = 'image';
    } 
    else if ($file_extension == 'mp3' || $file_extension == 'wav') {
            $folder   = 'sounds';
            $fileType = 'soundFile';
        } 
    else if($file_extension == 'mp4' || $file_extension == 'mov' || $file_extension == 'webm' || $file_extension == 'flv'){
        $folder   = 'videos';
        $fileType = 'video';
    } else {
        $folder   = 'files';
        $fileType = 'file';
    }

    if (empty($folder) || empty($fileType)) {
        return false;
    }
    $mime_types = explode(',', str_replace(' ', '', $ask->config->mime_types . ',application/json,application/octet-stream'));


    if (!in_array($data['type'], $mime_types)) {
        return array(
            'error' => 'File format not supported'
        );
    }
    $dir         = "upload/{$folder}/" . date('Y') . '/' . date('m');
    $filename    = $dir . '/' . GenerateKey() . '_' . date('d') . '_' . md5(time()) . "_{$fileType}.{$file_extension}";
    $second_file = pathinfo($filename, PATHINFO_EXTENSION);
    if (move_uploaded_file($data['file'], $filename)) {

        if(isset($data['mode'])) {
            if ($data['mode'] == 'avatar') {

                $thumbfile = str_replace('_image', '_avatar', $filename);
                $thumbnail = new ImageThumbnail($filename);
                $thumbnail->setSize(200, 200);
                $thumbnail->save($thumbfile);
                //@unlink($filename);
                if (is_file($thumbfile)) {
                    UploadToS3($thumbfile, array(
                        'amazon' => 0
                    ));
                }

                $filename = $thumbfile;
            }
        }else{

            if ($second_file == 'jpg' || $second_file == 'jpeg' || $second_file == 'png' || $second_file == 'gif') {
                if ($type == 1) {
                    @CompressImage($filename, $filename, 50);
                    $explode2 = @end(explode('.', $filename));
                    $explode3 = @explode('.', $filename);
                    $last_file = $explode3[0] . '_small.' . $explode2;
                    @Resize_Crop_Image(400, 400, $filename, $last_file, 60);

                    if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on'  || $ask->config->spaces == 'on' || $ask->config->cloud_upload == 'on') && !empty($last_file)) {
                        $upload_s3 = UploadToS3($last_file);
                    }
                } else {
                    @CompressImage($filename, $filename, 50);

                    if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on' || $ask->config->spaces == 'on'|| $ask->config->cloud_upload == 'on') && !empty($filename)) {
                        $upload_s3 = UploadToS3($filename);
                    }
                }
            } else {
                if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on' || $ask->config->spaces == 'on' || $ask->config->cloud_upload == 'on') && !empty($filename)) {
                    $upload_s3 = UploadToS3($filename);
                }
            }

        }

        $last_data             = array();
        $last_data['filename'] = $filename;
        $last_data['name']     = $data['name'];
        return $last_data;
    }
}
function IsQuestionReported($user_id, $question_id){
    global $db;
    if (empty($user_id)||empty($question_id)) {
        return false;
    }
    $question = $db->where('question_id',$question_id)->where('user_id',$user_id)->getOne(T_REPORTS);
    if($question){
        return true;
    }else{
        return false;
    }
}
function IsQuestionPromoted($question_id){
    global $db;
    if (empty($question_id)) {
        return false;
    }
    $start = strtotime(date('M')." ".date('d').", ".date('Y')." 12:00am");
    $end = strtotime(date('M')." ".date('d').", ".date('Y')." 11:59pm");
    $question = $db->where('promoted', $start,'>=')->where('promoted', $end,'<=')->where('id',$question_id)->getOne(T_QUESTIONS,array('promoted'));
    if($question){
        return true;
    }else{
        return false;
    }
}
function IsQuestionLiked($user_id, $question_id){
    global $db;
    if (empty($user_id)||empty($question_id)) {
        return false;
    }
    $question = $db->where('question_id',$question_id)->where('user_id',$user_id)->getOne(T_LIKES);
    if($question){
        return true;
    }else{
        return false;
    }
}
function IsUserFollow($user_id, $follow_user_id){
    global $db;
    if (empty($user_id)||empty($follow_user_id)) {
        return false;
    }

    $follow_user = $db->where('follower_id',$follow_user_id)->where('user_id',$user_id)->getOne(T_FOLLOWERS);
    if($follow_user){
        return true;
    }else{
        return false;
    }
}
function DeleteUser($id = 0) {
    global $ask, $db;

    if (empty($id)) {
        return false;
    }
    if ($ask->user->id != $id) {
        if (IsAdmin() == false) {
            return false;
        }
    }
    $db->where('follower_id', $id)->delete(T_FOLLOWERS);
    $db->where('notifier_id', $id)->delete(T_NOTIFICATIONS);
    $db->where('recipient_id', $id)->delete(T_NOTIFICATIONS);
     // $db->where('user_id', $id)->delete(T_COMMENTS);
    $db->where('user_id', $id)->delete(T_FOLLOWERS);
    $db->where('ask_user_id', $id)->delete(T_QUESTIONS, null, 'id');

   $get_questions = $db->where('user_id', $id)->get(T_QUESTIONS, null, 'id');
   foreach ($get_questions as $key => $question) {
       $delete_question = DeleteQuestion($question->id);
   }
    $get_cover_and_avatar = UserData($id);
    if ($get_cover_and_avatar->avatar_path != 'upload/photos/d-avatar.jpg') {
        @unlink($ask->base_path . str_replace('/' , $ask->directory_separator ,  $get_cover_and_avatar->avatar_path ) );
        if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on' || $ask->config->spaces == 'on' || $ask->config->cloud_upload == 'on')) {
            DeleteFromToS3($get_cover_and_avatar->avatar_path);
        }
    }
    if ($get_cover_and_avatar->cover_path != 'upload/photos/d-cover.jpg') {
        @unlink($ask->base_path . str_replace('/' , $ask->directory_separator ,  $get_cover_and_avatar->cover_path ));
        if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on' || $ask->config->spaces == 'on' || $ask->config->cloud_upload == 'on')) {
            DeleteFromToS3($get_cover_and_avatar->cover_path);
        }
    }
    $delete_user = $db->where('id', $id)->delete(T_USERS);
    if ($delete_user) {
        return true;
    }
}
function DeleteFromToS3($filename, $config = array()) {
    global $ask;

    if ($ask->config->s3_upload != 'on' && $ask->config->ftp_upload != 'on' && $ask->config->spaces != 'on' && $ask->config->cloud_upload != 'on') {
        return false;
    }

    if ($ask->config->ftp_upload == "on") {
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($ask->config->ftp_host, false, $ask->config->ftp_port);
        $login = $ftp->login($ask->config->ftp_username, $ask->config->ftp_password);

        if ($login) {
            if (!empty($ask->config->ftp_path)) {
                if ($ask->config->ftp_path != "./") {
                    $ftp->chdir($ask->config->ftp_path);
                }
            }
            $file_path = substr($filename, 0, strrpos( $filename, '/'));
            $file_name = substr($filename, strrpos( $filename, '/') + 1);
            $file_path_info = explode('/', $file_path);
            $path = '';
            if (!$ftp->isDir($file_path)) {
                return false;
            }
            $ftp->chdir($file_path);
            $ftp->pasv(true);
            if ($ftp->remove($file_name)) {
                return true;
            }
        }
    }
    else if ($ask->config->spaces == 'on' && !empty($ask->config->spaces_key) && !empty($ask->config->spaces_secret) && !empty($ask->config->space_name) && !empty($ask->config->space_region)) {
        include_once("assets/import/spaces/spaces.php");
        $key = $ask->config->spaces_key;
        $secret = $ask->config->spaces_secret;
        $space_name = $ask->config->space_name;
        $region = $ask->config->space_region;
        $space = new SpacesConnect($key, $secret, $space_name, $region);
        $delete = $space->DeleteObject($filename);
        if (!$space->DoesObjectExist($filename)) {
            return true;
        }
    }
    else if ($ask->config->cloud_upload == 'on') {
       require_once 'assets/libraries/cloud/vendor/autoload.php';

        try {
            $storage = new StorageClient([
               'keyFilePath' =>$ask->config->cloud_file_path 
            ]);
            // set which bucket to work in
            $bucket = $storage->bucket($ask->config->cloud_bucket_name);
            // set which bucket to work in
            $object = $bucket->object($filename);
            $delete = $object->delete();
            if ($delete) {
                return true;
            }
        } catch (Exception $e) {
            // maybe invalid private key ?
            // print $e;
            // exit();
            return false;
        }
    } 

    else {
        $s3Config = (
            empty($ask->config->amazone_s3_key) ||
            empty($ask->config->amazone_s3_s_key) ||
            empty($ask->config->region) ||
            empty($ask->config->s3_bucket_name)
        );

        if ($s3Config){
            return false;
        }
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $ask->config->region,
            'credentials' => [
                'key'    => $ask->config->amazone_s3_key,
                'secret' => $ask->config->amazone_s3_s_key,
            ]
        ]);

        $s3->deleteObject([
            'Bucket' => $ask->config->s3_bucket_name,
            'Key'    => $filename,
        ]);

        if (!$s3->doesObjectExist($ask->config->s3_bucket_name, $filename)) {
            return true;
        }
    }
}
function GetFollowersHtml($followers_get){
    $count = 0 ;
    $html_followers = '';
    foreach ($followers_get as $key => $follower) {
        $follower_data = UserData($follower->follower_id);
        if($follower_data) {
            $html_followers .= LoadPage('timeline/partials/follower_list', array(
                'ID' => $follower_data->id,
                'USERNAME' => $follower_data->username,
                'NAME' => $follower_data->name,
                'AVATAR' => $follower_data->avatar,
                'TIME' => Time_Elapsed_String($follower->time),
                'FOLLOWER_BUTTON' => GetFollowButton($follower_data->id)
            ));
            $count++;
        }
    }
    return array('html_followers' => $html_followers, 'count' => $count);
}
function GetFollowingsHtml($followers_get){
    $count = 0 ;
    $html_followers = '';
    //asort($followers_get);
    foreach ($followers_get as $key => $follower) {
        //echo '<h1>' . $follower->user_id . "</h1>";
        $follower_data = UserData($follower->user_id);
        if($follower_data) {
            $html_followers .= LoadPage('timeline/partials/following_list', array(
                'ID' => $follower_data->id,
                'USERNAME' => $follower_data->username,
                'NAME' => $follower_data->name,
                'AVATAR' => $follower_data->avatar,
                'TIME' => Time_Elapsed_String($follower->time),
                'FOLLOWER_BUTTON' => GetFollowButton($follower_data->id)
            ));
            $count++;
        }
    }
    return array('html_followers' => $html_followers, 'count' => $count);
}
function GetFollowButton($user_id = 0) {
    global $ask, $db, $lang;
    if (empty($user_id)) {
        return false;
    }

    $button_text  = __('follow');
    $button_icon  = '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>';
    $button_class = 'default';
    if (IS_LOGGED == true) {
        $check_if_follower = $db->where('user_id', $user_id)->where('follower_id', $ask->user->id)->getValue(T_FOLLOWERS, 'count(*)');
        if ($check_if_follower == 1) {
            $button_text  = __('following');
            $button_icon  = '<polyline points="20 6 9 17 4 12"></polyline>';
            $button_class = 'primary';
        }
        if( $user_id <> $ask->user->id ) {
            return LoadPage('buttons/follow', array(
                'IS_FOLLOWED_BUTTON' => $button_class,
                'IS_FOLLOWED_ICON' => $button_icon,
                'IS_FOLLOWED_TEXT' => $button_text,
                'USER_ID' => $user_id,
                'FOLLOWERS' => number_format($db->where('user_id', $user_id)->getValue(T_FOLLOWERS, "count(*)"))
            ));
        }else{
            return '';
        }
    }else{
        return '';
    }
}
function UserSuggestions($limit = 20) {
    global $ask, $sqlConnect;
    if (!is_numeric($limit)) {
        return false;
    }
    $data      = array();
    $user_id   = Secure($ask->user->id);
    $query_one = " SELECT `id` FROM " . T_USERS . " WHERE `active` = '1' AND `id` NOT IN (SELECT `user_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id}) AND `id` <> {$user_id}";
    if (isset($limit)) {
        $query_one .= " ORDER BY RAND() LIMIT {$limit}";
    }
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $data[] = UserData($fetched_data['id']);
    }
    return $data;
}
function UpdateSeenReports() {
    global $ask, $sqlConnect;
    $query_one = " UPDATE " . T_REPORTS . " SET `seen` = 1 WHERE `seen` = 0";
    $sql       = mysqli_query($sqlConnect, $query_one);
    if ($sql) {
        return true;
    }
}
function UserQuestionLikes($question_id) {
    global $sqlConnect;
    if (!is_numeric($question_id)) {
        return false;
    }
    $data      = array();
    $query_one = " SELECT `user_id` FROM " . T_LIKES . " WHERE `question_id` = '".$question_id."'  ORDER BY `id`";
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $data[] = UserData($fetched_data['user_id']);
    }
    return $data;
}
function GetHashtagSug($limit, $query) {
    global $sqlConnect;
    $data      = array();
    $html_fi   = array();
    $query_one = "SELECT * FROM " . T_HASHTAGS . " WHERE `tag` LIKE '%{$query}%' ORDER BY `trend_use_num` DESC";
    $query_one .= " LIMIT {$limit}";
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $html_fi['username'] = $fetched_data['tag'];
        $html_fi['label']    = $fetched_data['tag'];
        $data[]              = $html_fi;
    }
    return $data;
}
function GetHashtag($tag = '', $type = true) {
    global $sqlConnect;
    $create = false;
    if (empty($tag)) {
        return false;
    }
    $tag     = Secure($tag);
    $md5_tag = md5($tag);
    if (is_numeric($tag)) {
        $query = " SELECT * FROM " . T_HASHTAGS . " WHERE `id` = {$tag}";
    } else {
        $query  = " SELECT * FROM " . T_HASHTAGS . " WHERE `hash` = '{$md5_tag}' ";
        $create = true;
    }
    $sql_query   = mysqli_query($sqlConnect, $query);
    $sql_numrows = mysqli_num_rows($sql_query);
    $week        = date('Y-m-d', strtotime(date('Y-m-d') . " +2day"));
    if ($sql_numrows == 1) {
        $sql_fetch = mysqli_fetch_assoc($sql_query);
        return $sql_fetch;
    } elseif ($sql_numrows == 0 && $type == true) {
        if ($create == true) {
            $hash          = md5($tag);
            $query_two     = " INSERT INTO " . T_HASHTAGS . " (`hash`, `tag`, `last_trend_time`,`expire`) VALUES ('{$hash}', '{$tag}', " . time() . ", '$week')";
            $sql_query_two = mysqli_query($sqlConnect, $query_two);
            if ($sql_query_two) {
                $sql_id = mysqli_insert_id($sqlConnect);
                $data   = array(
                    'id' => $sql_id,
                    'hash' => $hash,
                    'tag' => $tag,
                    'last_trend_time' => time(),
                    'trend_use_num' => 0
                );
                return $data;
            }
        }
    }
}
function GetTrendingHashs($type = 'latest', $limit = 5) {
    global $sqlConnect;
    $data = array();
    if (empty($type)) {
        return false;
    }
    if (empty($limit) or !is_numeric($limit) or $limit < 1) {
        $limit = 5;
    }
    if ($type == "latest") {
        $query = "SELECT * FROM " . T_HASHTAGS . " WHERE `expire` >= CURRENT_DATE()  ORDER BY `last_trend_time` DESC LIMIT {$limit}";
    } elseif ($type == "popular") {
        $query = "SELECT * FROM " . T_HASHTAGS . " WHERE `expire` >= CURRENT_DATE()  ORDER BY `trend_use_num` DESC LIMIT {$limit}";
    }
    $sql_query   = mysqli_query($sqlConnect, $query);
    $sql_numrows = mysqli_num_rows($sql_query);
    if ($sql_numrows > 0) {
        while ($sql_fetch = mysqli_fetch_assoc($sql_query)) {
            $sql_fetch['url'] = UrlLink('hashtag/' . $sql_fetch['tag']);
            $data[]           = $sql_fetch;
        }
    }
    return $data;
}
function GetHashtagPostCount($tag_id){
    global $sqlConnect;
    if (empty($tag_id) or !is_numeric($tag_id) or $tag_id < 1) {
        return false;
    }
    $tag      = Secure($tag_id);
    $search_string = "#[" . $tag . "]";
    $query_one     = "SELECT COUNT(`id`) AS `tags` FROM " . T_QUESTIONS . " WHERE `question` LIKE '%{$search_string}%'";
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    if (mysqli_num_rows($sql_query_one) == 1) {
        $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
        return $sql_fetch_one['tags'];
    }
}
function GetSearchHash($s_query) {
    global $sqlConnect;
    $search_query = str_replace('#', '', Secure($s_query));
    $data         = array();
    $query        = mysqli_query($sqlConnect, "SELECT * FROM " . T_HASHTAGS . " WHERE `tag` LIKE '%{$search_query}%' ORDER BY `trend_use_num` DESC LIMIT 10");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $fetched_data['url'] = UrlLink('hashtag/' . $fetched_data['tag']);
        $data[]              = $fetched_data;
    }
    return $data;
}
function PostMarkup($text, $link = true, $hashtag = true, $mention = true) {
    if ($link == true) {
        $link_search = '/\[a\](.*?)\[\/a\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode     = urldecode($match);
                $match_decode_url = $match_decode;
                $count_url        = mb_strlen($match_decode);
                if ($count_url > 50) {
                    $match_decode_url = mb_substr($match_decode_url, 0, 30) . '....' . mb_substr($match_decode_url, 30, 20);
                }
                $match_url = $match_decode;
                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                    $match_url = 'http://' . $match_url;
                }
                $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode_url . '</a>', $text);
            }
        }
    }
  
    if ($hashtag == true) {
        $hashtag_regex = '/(#\[([0-9]+)\])/i';
        preg_match_all($hashtag_regex, $text, $matches);
        $match_i = 0;
        foreach ($matches[1] as $match) {
            $hashtag  = $matches[1][$match_i];
            $hashkey  = $matches[2][$match_i];
            $hashdata = GetHashtag($hashkey);
            if (is_array($hashdata)) {
                $hashlink = '<a href="' . UrlLink('hashtag/' . $hashdata['tag']) . '" data-load="?link1=hashtag&hashtag='.$hashdata['tag'].'" class="hash">#' . $hashdata['tag'] . '</a>';
                $text     = str_replace($hashtag, $hashlink, $text);
            }
            $match_i++;
        }
    }
    if ($mention == true) {
        $mention_regex = '/@\[([0-9]+)\]/i';
        if (preg_match_all($mention_regex, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match         = Secure($match);
                $match_user    = UserData($match);
                $match_search  = '@[' . $match . ']';
                $match_replace = '<span style="display: none;">@' . $match_user->username . '</span><span class="user-popover" data-id="' . $match_user->id . '"><a href="' . UrlLink('@' . $match_user->username) . '" class="hash" data-load="?link1=timeline&id=' . $match_user->username . '">' . $match_user->name . '</a></span>';
                if (isset($match_user->id)) {
                    $text = str_replace($match_search, $match_replace, $text);
                }
            }
        }
    }
    return $text;
}
function IsModerator($user_id = '') {
    global $ask, $sqlConnect;
    if (IS_LOGGED == false) {
        return false;
    }
    if ($ask->user->admin == 2) {
        return true;
    }
    return false;
}
function IsAdsOwner($ads_id = 0, $user_id = 0) {
    global $sqlConnect, $ask;
    if (empty($user_id)) {
        $user_id = $ask->user->id;
    }
    $user_id = Secure($user_id);
    $ads_id  = Secure($ads_id);
    $result  = false;
    $query   = mysqli_query($sqlConnect, "SELECT * FROM
            " . T_USER_ADS . " WHERE `id` = '$ads_id'");
    if (!empty($query)) {
        $fetched_data = mysqli_fetch_assoc($query);
        if ($fetched_data['user_id'] == $ask->user->id || $ask->user->admin == 1 || IsModerator() == true) {
            $result = true;
        }
    }
    return $result;
}
function GetUserAdData($id) {
    global $sqlConnect;
    if (IS_LOGGED == false || !$id || !is_numeric($id) || $id < 1) {
        return false;
    }

    $table        = T_USER_ADS;
    $query        = mysqli_query($sqlConnect, "SELECT * FROM  `$table` WHERE `id` = '$id' ");
    $fetched_data = mysqli_fetch_assoc($query);
    $data         = false;

    if (!empty($fetched_data)) {
        $fetched_data['user_data']   = UserData($fetched_data['user_id']);
        $fetched_data['is_owner']    = IsAdsOwner($fetched_data['id']);
        $fetched_data['country_ids'] = array_values(explode(',', $fetched_data['audience']));
        $fetched_data['ad_media_path']    = $fetched_data['ad_media'];
        $fetched_data['ad_media']    = GetMedia($fetched_data['ad_media']);
        $data                        = $fetched_data;
    }

    return $data;
}
function GetShortTitle($text = false, $preview = false, $len = 40) {
    if (!$text) {
        return false;
    }
    if (strlen($text) > $len && !$preview) {
        $text = mb_substr($text, 0, $len, "UTF-8") . "..";
    }
    return $text;
}
function SizeUnits($bytes = 0){
    if ($bytes >= 1073741824)
    {
        $bytes = round(($bytes / 1073741824)) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = round(($bytes / 1048576)) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = round(($bytes / 1024)) . ' KB';
    }
    return $bytes;
}
function DeleteUserAd($id = false) {
    global $sqlConnect,$ask;
    if (IS_LOGGED == false || !$id || !IsAdsOwner($id)) {
        return false;
    }
    $ad = GetUserAdData($id);

    $ad_media_path = $ask->base_path . str_replace('/' , $ask->directory_separator ,  $ad['ad_media_path'] );

    @unlink($ad_media_path);
    if (($ask->config->s3_upload == 'on' || $ask->config->ftp_upload == 'on')) {
        DeleteFromToS3($ad['ad_media']);
    }
    $query     = false;
    $query     .= mysqli_query($sqlConnect, "DELETE FROM " . T_USER_ADS . "  WHERE `id` = {$id} ");
    $query     .= mysqli_query($sqlConnect, "DELETE FROM " . T_USERADS_DATA . "  WHERE `ad_id` = {$id} ");
    return $query;
}
function fetchDataFromURL($url = '') {
    if (empty($url)) {
        return false;
    }
    $ch = curl_init($url);
    curl_setopt( $ch, CURLOPT_POST, false );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    curl_setopt( $ch, CURLOPT_HEADER, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    return curl_exec( $ch );
}
function ReplenishingUserBalance($sum) {
    global $ask, $sqlConnect;
    if (IS_LOGGED == false || !$sum) {
        return false;
    }
    $user      = $ask->user->id;
    $user_data = UserData($user);
    if (empty($user_data)) {
        return false;
    }
    $user_balance = $user_data->wallet;
    $user_balance += $sum;
    $update_data = array(
        'wallet' => $user_balance
    );

    $update      = UpdateUserData($user, $update_data);
    return $update;
}
function UpdateUserData($user_id, $update_data, $unverify = false) {
    global $ask, $sqlConnect, $cache;
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    if (empty($update_data)) {
        return false;
    }
    $user_id = Secure($user_id);
    $is_mod = IsModerator();
    $is_admin = IsAdmin();

    if ($is_admin === false && $is_mod === false) {
        if ($ask->user->id != $user_id) {
            return false;
        }
    }
    if (isset($update_data['verified'])) {
        if (empty($update_data['pro_'])) {
            if ($is_admin === false && $is_mod === false) {
                return false;
            }
        }
    }
    if ($is_mod) {
        $user_data_ = UserData($user_id);
        if ($user_data_['admin'] == 1) {
            return false;
        }
    }
    if (isset($update_data['country_id'])) {
        if (!array_key_exists($update_data['country_id'], $ask->countries_name)) {
            $update_data['country_id'] = 1;
        }
    }
    $update = array();
    foreach ($update_data as $field => $data) {
        if ($field != 'pro_') {
            $update[] = '`' . $field . '` = \'' . Secure($data, 0) . '\'';
        }
    }
    $impload   = implode(', ', $update);
    $query_one = " UPDATE " . T_USERS . " SET {$impload} WHERE `id` = {$user_id} ";

    $query_two = " UPDATE " . T_USERS . " SET `verified` = '0' WHERE `id` = {$user_id} ";
    $query1    = mysqli_query($sqlConnect, $query_one);
    if ($unverify == true) {
        @mysqli_query($sqlConnect, $query_two);
    }
    if ($query1) {
        if (!empty($update_data['username'])) {
            //UpdateUsernameInNotifications($user_id, $update_data['username']);
        }
        return true;
    } else {
        return false;
    }
}
function UpdateUsernameInNotifications($user_id = 0, $username = '') {
    global $sqlConnect;
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    if (empty($username)) {
        return false;
    }
    $query_one = "UPDATE " . T_NOTIFICATION . " SET `url` = 'index.php?link1=timeline&u={$username}' WHERE `notifier_id` = {$user_id} AND (`type` = 'following' OR `type` = 'visited_profile' OR `type` = 'accepted_request')";
    $query     = mysqli_query($sqlConnect, $query_one);
    if ($query) {
        return true;
    }
}
function UrlDomain($url)
{
    $host = @parse_url($url, PHP_URL_HOST);
    if (!$host){
        $host = $url;
    }
    if (substr($host, 0, 4) == "www."){
        $host = substr($host, 4);
    }
    if (strlen($host) > 50){
        $host = substr($host, 0, 47) . '...';
    }
    return $host;
}
function GetSideBarAds() {
    global $sqlConnect, $ask;
    if (IS_LOGGED == false) {
        return false;
    }
    $user_gender  = $ask->user->gender;
    $user_id      = $ask->user->id;
    $user_country = $ask->user->country_id;
    $query_one    = '';
    if( isset($ask->ad_con['ads']) && !empty($ask->ad_con['ads']) ) {
        $con_list = implode(',', $ask->ad_con['ads']);
        if ($con_list) {
            $query_one .= " AND `id` NOT IN ({$con_list}) ";
        }
    }
    $sql   = "SELECT * FROM  " . T_USER_ADS . " 
    WHERE `user_id` IN (SELECT `user_id` FROM " . T_USERS . " WHERE `wallet` > 0)
        AND `status` = '1' AND `appears` = 'sidebar' AND
        (`gender` = '$user_gender' OR `gender` = 'all')  AND `audience` LIKE '%$user_country%'
        {$query_one}
        ORDER BY RAND() DESC LIMIT 1";
    $query = mysqli_query($sqlConnect, $sql);
    $data  = array();
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $fetched_data['ad_media']    = GetMedia($fetched_data['ad_media']);
        $fetched_data['headline']    = GetShortTitle($fetched_data['headline'], false, 30);
        $fetched_data['description'] = GetShortTitle($fetched_data['description'], false, 60);
        if ($fetched_data['bidding'] == 'views') {
            @RegisterAdConversionView($fetched_data['id']);
        }
        $data[] = $fetched_data;
    }
    return $data;
}
function UpdateUserAds($id, $update_data = array()) {
    global $sqlConnect;
    if (IS_LOGGED == false || empty($update_data)) {
        return false;
    }
    foreach ($update_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . $data . '\'';
    }
    $impload   = implode(', ', $update);
    $query_one = "UPDATE " . T_USER_ADS . " SET {$impload} WHERE `id` = {$id} ";
    $query     = mysqli_query($sqlConnect, $query_one);
    return $query;
}
function IsReplyQuestionOwner($question){
    global $db;
    if(isset($question->replay_question_id)) {
        $question_owner_id = $db->where('id',$question->replay_question_id)->getOne(T_QUESTIONS , array('user_id'));
        if(isset($question_owner_id->user_id)) {
            if( (int)$question_owner_id->user_id !== (int)$question->user_id ){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }else{
        return false;
    }
}
function IsConversionExists($id) {
    global $ask;
    $adv_ids  = $ask->ad_con;
    $result   = false;
    $is_admin = IsAdsOwnerNotAdmin($id);

    if ($id && is_array($adv_ids) && isset($adv_ids['ads'])) {
        if ($is_admin == false) {
            if (array_key_exists($id, $adv_ids['ads'])) {
                $result = true;
            } else {
                $adv_ids['ads'][$id] = $id;
                setcookie('ad-con', htmlentities(serialize($adv_ids)), time() + (10 * 365 * 24 * 60 * 60));
            }
        }
    } else {
        setcookie('ad-con', htmlentities(serialize(array(
            'date' => date('Y-m-d'),
            'ads' => array()
        ))), time() + (10 * 365 * 24 * 60 * 60));
        $result = true;
    }
    return $result;
}
function RegisterAdConversionClick($id) {
    global $sqlConnect, $ask;
    if (IS_LOGGED == false || !$id) {
        return false;
    }
    $ad       = GetUserAdData($id);
    $user     = $ask->user->id;
    $result   = false;
    $is_admin = IsAdsOwnerNotAdmin($id);
    if ($ad && is_array($ad) && !empty($ad) && isset($ad['user_data']) && !IsConversionExists($id) && $is_admin == false) {
        $ad_user_id = $ad['user_data']->id;
        $wallet     = $ad['user_data']->wallet -= $ask->config->ad_c_price;
        $result     = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `wallet` = '$wallet' WHERE `id` = '$ad_user_id'");
        RegisterAdClick($id);
    } else if ($ad && is_array($ad) && !empty($ad) && isset($ad['user_data']) && $is_admin == false) {
        $result = RegisterAdClick($id);
    } else {
        return true;
    }
    return $result;
}
function RegisterAdConversionView($id) {
    global $sqlConnect, $ask;
    if (IS_LOGGED == false || !$id) {
        return false;
    }
    $ad       = GetUserAdData($id);
    $user     = $ask->user->id;
    $result   = false;
    $is_admin = IsAdsOwnerNotAdmin($id);
    if ($ad && is_array($ad) && !empty($ad) && isset($ad['user_data']) && !IsConversionExists($id) && $is_admin == false) {
        $ad_user_id = $ad['user_data']->id;
        $wallet     = $ad['user_data']->wallet -= $ask->config->ad_v_price;
        $result     = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `wallet` = '$wallet' WHERE `id` = '$ad_user_id'");
        RegisterAdView($id);
    } else if ($ad && is_array($ad) && !empty($ad) && isset($ad['user_data']) && $is_admin == false) {
        $result = RegisterAdView($id);
    } else {
        return true;
    }
    return $result;
}
function IsAdsOwnerNotAdmin($ads_id = 0, $user_id = 0) {
    global $sqlConnect, $ask;
    if (empty($user_id)) {
        $user_id = $ask->user->id;
    }
    $user_id = Secure($user_id);
    $ads_id  = Secure($ads_id);
    $result  = false;
    $query   = mysqli_query($sqlConnect, "SELECT * FROM
            " . T_USER_ADS . " WHERE `id` = '$ads_id'");
    if (!empty($query)) {
        $fetched_data = mysqli_fetch_assoc($query);
        if ($fetched_data['user_id'] == $ask->user->id) {
            $result = true;
        }
    }
    return $result;
}
function RegisterAdClick($id) {
    global $sqlConnect,$ask ;
    if (IS_LOGGED == false || !$id) {
        return false;
    }
    $ad     = GetUserAdData($id);
    $result = false;
    if ($ad && is_array($ad) && !empty($ad)) {
        $ad_user_id = $ad['user_data']->id;
        //record click
        $result1 = mysqli_query($sqlConnect, "INSERT INTO " . T_USERADS_DATA . " (`id`, `user_id`, `ad_id`, `clicks`, `views`, `spend`, `dt`) VALUES (NULL, '".$ad_user_id."', '".$id."', '1', '0', '".$ask->config->ad_c_price."', CURRENT_TIMESTAMP);");
        $result = UpdateUserAds($id, array(
            'clicks' => ($ad['clicks'] + 1)
        ));
    }
    return $result;
}
function RegisterAdView($id) {
    global $sqlConnect, $ask;
    if (IS_LOGGED == false || !$id) {
        return false;
    }

    $ad     = GetUserAdData($id);
    $result = false;
    if ($ad && is_array($ad) && !empty($ad)) {
        $ad_user_id = $ad['user_data']->id;
        //record view
        $sql = "INSERT INTO " . T_USERADS_DATA . " (`id`, `user_id`, `ad_id`, `clicks`, `views`, `spend`, `dt`) VALUES (NULL, '".$ad_user_id."', '".$id."', '0', '1', '".$ask->config->ad_v_price."',  CURRENT_TIMESTAMP);";
        $result1 = mysqli_query($sqlConnect, $sql);
        $result = UpdateUserAds($id, array(
            'views' => ($ad['views'] + 1)
        ));
    }
    return $result;
}
function GetWalletReplenishingDone($paymentId, $PayerID) {
    global $ask;//,$paypal;
    if (IS_LOGGED == false || !$paymentId || !$PayerID) {
        return false;
    }
    //require 'assets/includes/paypal_config.php';

    require 'assets/import/PayPal/vendor/autoload.php';
    $paypal = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            $ask->config->paypal_id,
            $ask->config->paypal_secret
        )
    );
    $paypal->setConfig(
        array(
            'mode' => $ask->config->paypal_mode
        )
    );


    $payment = Payment::get($paymentId, $paypal);
    $execute = new PaymentExecution();
    $execute->setPayerId($PayerID);
    try {
        $result = $payment->execute($execute, $paypal);
        //var_dump($result);
    } catch (PayPal\Exception\PayPalConnectionException $e) {
        $ex = json_decode($e->getData(), false);
        //var_dump($ex);
    }
//    }
//    catch (Exception $e) {
//        $ex = json_decode($e->getData(), false);
//        var_dump($ex);
////        if($ex->name=='PAYMENT_ALREADY_DONE'){
////            return true;
////        }else{
//            return json_decode($e->getData(), true);
//        //}
//    }
    return true;
}
function ReplenishWallet($sum) {
    global $ask,$paypal,$site_url;
    if (IS_LOGGED == false || !$sum) {
        return false;
    }
    require 'assets/includes/paypal_config.php';
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item = new Item();
    $item->setName('Replenishing your balance')->setQuantity(1)->setPrice($sum)->setCurrency($ask->config->ads_currency);
    $itemList = new ItemList();
    $itemList->setItems(array(
        $item
    ));
    $details = new Details();
    $details->setSubtotal($sum);
    $amount = new Amount();
    $amount->setCurrency($ask->config->ads_currency)->setTotal($sum)->setDetails($details);
    $transaction = new Transaction();
    $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Replenish my balance')->setInvoiceNumber(time());
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($site_url . "/aj/ads/get-paid?success=1&amount={$sum}")
                 ->setCancelUrl($site_url . "/aj/ads/get-paid?success=1");
    $payment = new Payment();
    $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
        $transaction
    ));
    try {
        $payment->create($paypal);
    }
    catch (Exception $e) {
        $data = array(
            'type' => 'ERROR',
            'details' => json_decode($e->getData())
        );
        if (empty($data['details'])) {
            $data['details'] = json_decode($e->getCode());
        }
        return $data;
    }
    $data = array(
        'status' => 200,
        'type' => 'SUCCESS',
        'url' => $payment->getApprovalLink()
    );
    return $data;
}
function PromoteQuestion(){
    global $ask,$db,$lang;
    $is_question_owner    = false;
    $question_id = Secure((int)$_GET['question_id']);
    $user_id = Secure((int)$_GET['user_id']);
    $question_data = $db->where('id', (int)$question_id)->getOne(T_QUESTIONS);
    if (!empty($question_data)) {
        if( (int)$question_data->user_id == (int)$user_id || IsAdmin()){
            $is_question_owner = true;
            $db->where('id', (int)$question_id)->update(T_QUESTIONS, array('promoted' => time()));
        }
    }


    return true;
}
function ReplenishPromoteQuestion($sum,$question_id,$user_id) {
    global $ask,$paypal,$site_url;
    if (IS_LOGGED == false || !$sum) {
        return false;
    }
    require 'assets/includes/paypal_config.php';
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item = new Item();
    $item->setName('Replenishing your balance')->setQuantity(1)->setPrice($sum)->setCurrency($ask->config->ads_currency);
    $itemList = new ItemList();
    $itemList->setItems(array(
        $item
    ));
    $details = new Details();
    $details->setSubtotal($sum);
    $amount = new Amount();
    $amount->setCurrency($ask->config->ads_currency)->setTotal($sum)->setDetails($details);
    $transaction = new Transaction();
    $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Replenish my balance')->setInvoiceNumber(time());
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($site_url . "/aj/ads/get-paid-promote?success=1&amount={$sum}&question_id={$question_id}&user_id={$user_id}")
        ->setCancelUrl($site_url . "/aj/ads/get-paid-promote?success=1&question_id={$question_id}&user_id={$user_id}");
    $payment = new Payment();
    $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
        $transaction
    ));
    try {
        $payment->create($paypal);
    }
    catch (Exception $e) {
        $data = array(
            'type' => 'ERROR',
            'details' => json_decode($e->getData())
        );
        if (empty($data['details'])) {
            $data['details'] = json_decode($e->getCode());
        }
        return $data;
    }
    $data = array(
        'status' => 200,
        'type' => 'SUCCESS',
        'url' => $payment->getApprovalLink()
    );
    return $data;
}
function GetCurrency($currency) {
    if (empty($currency)) {
        return false;
    }
    $currency_ = '$';
    switch ($currency) {
        case 'USD':
            $currency_ = '$';
            break;
        case 'JPY':
            $currency_ = 'Â¥';
            break;
        case 'TRY':
            $currency_ = 'â‚º';
            break;
        case 'GBP':
            $currency_ = 'Â£';
            break;
        case 'EUR':
            $currency_ = 'â‚¬';
            break;
        case 'AUD':
            $currency_ = '$';
            break;
        case 'INR':
            $currency_ = 'â‚¹';
            break;
        case 'RUB':
            $currency_ = 'Ö„';
            break;
        case 'PLN':
            $currency_ = 'zÅ‚';
            break;
        case 'ILS':
            $currency_ = 'â‚ª';
            break;
        case 'BRL':
            $currency_ = 'R$';
            break;
        case 'INR':
            $currency_ = 'â‚¹';
            break;
    }
    return $currency_;
}
function GetMyAds($args = array()) {
    global $sqlConnect, $ask;
    if (IS_LOGGED == false) {
        return false;
    }
    $options   = array(
        "id" => false,
        "offset" => 0
    );
    $args      = array_merge($options, $args);
    $offset    = Secure($args['offset']);
    $id        = Secure($args['id']);
    $user_id   = $ask->user->id;
    $query_one = '';
    $data      = array();
    if ($offset > 0) {
        $query_one .= " AND `id` < {$offset} AND `id` <> {$offset} ";
    }
    if ($id && $id > 0 && is_numeric($id)) {
        $query_one .= " AND `id` = '$id' ";
    }
    $sql   = "SELECT `id` FROM  
                " . T_USER_ADS . " 
                    WHERE `user_id` = '$user_id' 
                        {$query_one} ORDER BY `id` 
                            DESC LIMIT 30";
    $query = mysqli_query($sqlConnect, $sql);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $ad = GetUserAdData($fetched_data['id']);
        if ($ad && !empty($ad)) {
            $ad['name']     = GetShortTitle($ad['name']);
            $ad['edit-url'] = UrlLink('index.php?link1=edit-ads&id=' . $ad['id']);
            $ad['chart-url'] = UrlLink('index.php?link1=chart-ads&id=' . $ad['id']);
            $data[]         = $ad;
        }
    }
    return $data;
}
function GetSearchPosts($s_query, $after_post_id = 0, $limit = 5, $before_post_id = 0,$ids = array()) {
    global $sqlConnect,$lang;
    $data         = array();
    $search_query = str_replace('#', '', Secure($s_query));
        $query_one     = "SELECT * FROM " . T_QUESTIONS . " WHERE `question` LIKE '%{$search_query}%'";
        if (isset($after_post_id) && !empty($after_post_id) && is_numeric($after_post_id)) {
            $after_post_id = Secure($after_post_id);
            $query_one .= " AND id < {$after_post_id}";
        }
        if (isset($before_post_id) && !empty($before_post_id) && is_numeric($before_post_id)) {
            $before_post_id = Secure($before_post_id);
            $query_one .= " AND id > {$before_post_id}";
        }
        if(!empty($ids) && is_array($ids)){
            $query_one .= " AND id NOT IN (" . implode(',',$ids) .") ";
        }
        $query_one .= ' AND `public` = 1 ';
        $query_one .= " ORDER BY `id` DESC LIMIT {$limit}";
        $sql_query_one = mysqli_query($sqlConnect, $query_one);

        while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
            $p = (object)$sql_fetch_one;
            $posts = QuestionData($p);

//            if( isset($posts->answer) && ){
//                $posts->question = nl2br( __('question') . ' : ' . PostMarkup($posts->question) . "\r\n" . __('answer') . ' : ' . $posts->answer );
//            }
//            echo '<pre>';
//            print_r($posts);
//            echo "<hr>";
            //if (is_array($posts)) {
            $data[] = $posts;
            //}
        }

    return $data;
}
function GetHashtagPosts($s_query, $after_post_id = 0, $limit = 5, $before_post_id = 0,$ids = array()) {
    global $sqlConnect,$lang;
    $data         = array();
    $search_query = str_replace('#', '', Secure($s_query));
    $hashdata     = GetHashtag($search_query, false);
    if (is_array($hashdata) && count($hashdata) > 0) {
        $search_string = "#[" . $hashdata['id'] . "]";
        $query_one     = "SELECT * FROM " . T_QUESTIONS . " WHERE `question` LIKE '%{$search_string}%'";
        if (isset($after_post_id) && !empty($after_post_id) && is_numeric($after_post_id)) {
            $after_post_id = Secure($after_post_id);
            $query_one .= " AND id < {$after_post_id}";
        }
        if (isset($before_post_id) && !empty($before_post_id) && is_numeric($before_post_id)) {
            $before_post_id = Secure($before_post_id);
            $query_one .= " AND id > {$before_post_id}";
        }
        if(!empty($ids) && is_array($ids)){
            $query_one .= " AND id NOT IN (" . implode(',',$ids) .") ";
        }
        $query_one .= ' AND `public` = 1 ';
        $query_one .= " ORDER BY `id` DESC LIMIT {$limit}";
        $sql_query_one = mysqli_query($sqlConnect, $query_one);

        while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
            $p = (object)$sql_fetch_one;
            $posts = QuestionData($p);

//            if( isset($posts->answer) && ){
//                $posts->question = nl2br( __('question') . ' : ' . PostMarkup($posts->question) . "\r\n" . __('answer') . ' : ' . $posts->answer );
//            }
//            echo '<pre>';
//            print_r($posts);
//            echo "<hr>";
            //if (is_array($posts)) {
                $data[] = $posts;
            //}
        }
    }
    return $data;
}
function Sql_Result($res, $row = 0, $col = 0) {
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
        mysqli_data_seek($res, $row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])) {
            return $resrow[$col];
        }
    }
    return false;
}
function IsQuestionVoted($question_id){
    global $ask,$db;
    if(IS_LOGGED === false ){return false;}
    $can_vote = false;
    $vote_exist  = $db->where('question_id', $question_id)->where('user_id',$ask->user->id)->getOne(T_QUESTIONS_VOTES,array('count(*) as total'));
    if ($vote_exist->total > 0) {
        $can_vote = true;
    }
    return $can_vote;
}
function QuestionVotes($question_id){
    global $ask,$db;
    //if(IS_LOGGED === false ){return false;}
    $votes = 0;
    $vote_exist  = $db->where('question_id', $question_id)->getOne(T_QUESTIONS_VOTES,array('count(*) as total'));
    if ($vote_exist->total > 0) {
        $votes = $vote_exist->total;
    }
    return $votes;
}
function GetVotePercentages($choice_one, $choice_two){
    global $db;
    $Percentages = array();
    if(empty($choice_one)||empty($choice_two)) return false;
    $choice_one_count = $db->where('choice_id', $choice_one)->getOne(T_QUESTIONS_VOTES,array('count(*) as total'));
    $choice_two_count = $db->where('choice_id', $choice_two)->getOne(T_QUESTIONS_VOTES,array('count(*) as total'));

//    $choice_one_count = $db->rawQuery("SELECT count(*)/(SELECT count(*) from `".T_QUESTIONS_VOTES."` where `choice_id` = '".Secure($choice_one)."') * 100 as percentage from `".T_QUESTIONS_VOTES."` where `choice_id` = '".Secure($choice_one)."';");
//    $choice_two_count = $db->rawQuery("SELECT count(*)/(SELECT count(*) from `".T_QUESTIONS_VOTES."` where `choice_id` = '".Secure($choice_two)."') * 100 as percentage from `".T_QUESTIONS_VOTES."` where `choice_id` = '".Secure($choice_two)."';");
//

    $total = (int)$choice_one_count->total + (int)$choice_two_count->total;
    if($total == 0 ){
        $Percentages[$choice_one] = 0;
        $Percentages[$choice_two] = 0;
    }else{
        $Percentages[$choice_one] = (int)round((int)$choice_one_count->total / ((int)$total / 100),0);
        $Percentages[$choice_two] = (int)round((int)$choice_two_count->total / ((int)$total / 100),0);
    }



//    $Percentages[$choice_one] = (($choice_one_count->total + $choice_two_count->total) * $choice_one_count->total / 100) * 10000;
//    $Percentages[$choice_two] = (($choice_one_count->total + $choice_two_count->total) * $choice_two_count->total / 100) * 10000;

//    $Percentages[$choice_one] = (int)$choice_one_count[0]->percentage;
//    $Percentages[$choice_two] = (int)$choice_two_count[0]->percentage;
    return $Percentages;
}
function UserIdFromUsername($username) {
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_USERS . " WHERE `username` = '{$username}'");
    return Sql_Result($query, 0, 'id');
}
function RenderQuestion($question,$new=true){
    global $sqlConnect;
    $data = array();

     $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
        $i          = 0;
        preg_match_all($link_regex, $question, $matches);
        foreach ($matches[0] as $match) {
            $match_url    = strip_tags($match);
            $syntax       = '[a]' . urlencode($match_url) . '[/a]';
            $question = str_replace($match, $syntax, $question);
        }

    $mention_regex = '/@([A-Za-z0-9_]+)/i';
    preg_match_all($mention_regex, $question, $matches);
    foreach ($matches[1] as $match) {
        $match         = Secure($match);
        $match_user    = UserData(UserIdFromUsername($match));
        if($match_user) {
            $match_search = '@' . $match;
            $match_replace = '@[' . $match_user->id . ']';
            if (isset($match_user->id)) {
                $question = str_replace($match_search, $match_replace, $question);
                $data['mentions'][] = $match_user->id;
            }
        }
    }

    $hashtag_regex = '/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/i';
    preg_match_all($hashtag_regex, $question, $matches);
    foreach ($matches[1] as $match) {
        if (!is_numeric($match)) {
            $hashdata = GetHashtag($match);
            if (is_array($hashdata)) {
                $match_search  = '#' . $match;
                $match_replace = '#[' . $hashdata['id'] . ']';
                $data['hashtag'][] = $hashdata;
                if (mb_detect_encoding($match_search, 'ASCII', true)) {
                    $question = preg_replace("/$match_search\b/i", $match_replace, $question);
                } else {
                    $question = str_replace($match_search, $match_replace, $question);
                }
                $hashtag_query     = "UPDATE " . T_HASHTAGS . " SET 
                    `last_trend_time` = " . time() . ", 
                    `trend_use_num`   = " . ($hashdata['trend_use_num'] + 1) . ",
                    `expire`          = '" . date('Y-m-d', strtotime(date('Y-m-d') . " +2day")) . "'       
                    WHERE `id` = " . $hashdata['id'];
                mysqli_query($sqlConnect, $hashtag_query);
            }
        }
    }
    $data['question'] = $question;
    return $data;
}
function Markup($text, $link = true) {
    if ($link == true) {
        $link_search = '/\[a\](.*?)\[\/a\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode     = urldecode($match);
                $match_decode_url = $match_decode;
                $count_url        = mb_strlen($match_decode);
                if ($count_url > 50) {
                    $match_decode_url = mb_substr($match_decode_url, 0, 30) . '....' . mb_substr($match_decode_url, 30, 20);
                }
                $match_url = $match_decode;
                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                    $match_url = 'http://' . $match_url;
                }
                $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode_url . '</a>', $text);
            }
        }
    }
    $link_search = '/\[img\](.*?)\[\/img\]/i';
    if (preg_match_all($link_search, $text, $matches)) {
        foreach ($matches[1] as $match) {
            $match_decode     = urldecode($match);
            $text = str_replace('[img]' . $match . '[/img]', '<a href="' . getMedia(strip_tags($match_decode)) . '" target="_blank"><img style="width:300px;border-radius: 20px;" src="' . getMedia(strip_tags($match_decode)) . '"></a>', $text);
        }
    }

    $link_search = '/\[vd\](.*?)\[\/vd\]/i';

    if (preg_match_all($link_search, $text, $matches)){
        foreach ($matches[1] as $match) {
            $match_decode = urldecode($match);
            $text         = str_replace('[vd]'.$match. '[/vd]', '<video controls width="250"><source src="'. GetMedia(strip_tags($match_decode)) .'"type="video/mp4"/></video>', $text);
            // $text         = str_replace('[vd]'.$match. '[/vd]', '<video controls width="250"><source src="'. getMedia(strip_tags($match_decode)) .'"type="video/mov"/></video>', $text);
             
        }
    }


    return $text;
}

function GetMessagesUserList($data = array('offset' => 0 ,'limit' => 20 , 'api' => false)) {
    global $ask, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if($data['api'] === true) {
        $limit = (int)$data['limit'];
        $offset = (int)$data['offset'];
    }

    $db->where("user_one", $ask->user->id);

    if (isset($data['keyword'])) {
        $keyword = Secure($data['keyword']);
        $db->where("user_two IN (SELECT id FROM users WHERE username LIKE '%$keyword%' OR `first_name` LIKE '%$keyword%')");
    }

    if (isset($data['offset']) && $data['api'] === true) {
        $db->where("id > " . $offset);
    }

    $users = $db->orderBy('time', 'DESC')->get(T_CHATS, $limit);

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }

    $users_html = '';
    $data_array = array();
    foreach ($users as $key => $user) {
        $user = UserData($user->user_two);
        if (!empty($user)) {
            $get_last_message = $db->where("((from_id = {$ask->user->id} AND to_id = $user->id AND `from_deleted` = '0') OR (from_id = $user->id AND to_id = {$ask->user->id} AND `to_deleted` = '0'))")->orderBy('id', 'DESC')->getOne(T_MESSAGES);
            $get_count_seen = $db->where("to_id = {$ask->user->id} AND from_id = $user->id AND `from_deleted` = '0' AND seen = 0")->orderBy('id', 'DESC')->getValue(T_MESSAGES, 'COUNT(*)');
            if ($return_method == 'html') {
                $users_html .= LoadPage("messages/ajax/user-list", array(
                    'ID' => $user->id,
                    'AVATAR' => $user->avatar,
                    'NAME' => $user->name,
                    'LAST_MESSAGE' => (!empty($get_last_message->text)) ? markUp( strip_tags($get_last_message->text) ) : '',
                    'COUNT' => (!empty($get_count_seen)) ? $get_count_seen : '',
                    'USERNAME' => $user->username
                ));
            } else {
                if ($data['api'] === false) {
                    $data_array[$key]['user'] = $user;
                    $data_array[$key]['get_count_seen'] = $get_count_seen;
                    $data_array[$key]['get_last_message'] = $get_last_message;
                }else{
                    $data_array[] = array(
                        'user' => $user,
                        'get_count_seen' => $get_count_seen,
                        'get_last_message' => $get_last_message
                    );
                }
            }
        }
    }
    $users_obj = (!empty($data_array)) ? ToObject($data_array) : array();
    return (!empty($users_html)) ? $users_html : $users_obj;
}

function GetMessageData($id = 0) {
    global $ask, $db;
    if (empty($id) || !IS_LOGGED) {
        return false;
    }
    $fetched_data = $db->where('id', Secure($id))->getOne(T_MESSAGES);
    if (!empty($fetched_data)) {
        $fetched_data->text = Markup($fetched_data->text);
        return $fetched_data;
    }
    return false;
}
function DeleteMessage($message_id, $media = '', $deleter_id = 0) {
    global $ask, $sqlConnect,$db;
    if (empty($deleter_id)) {
        if (IS_LOGGED == false) {
            return false;
        }
    }
    if (empty($message_id) || !is_numeric($message_id) || $message_id < 0) {
        return false;
    }
    $user_id = $deleter_id;
    if (empty($user_id) && IS_LOGGED == true) {
        $user_id = $ask->user->id;
    }
    $message_id    = Secure($message_id);
    $query_one     = "SELECT * FROM " . T_MESSAGES . " WHERE `id` = {$message_id}";

    $sql_query_one = mysqli_query($sqlConnect, $query_one);

    if (mysqli_num_rows($sql_query_one) == 1) {
        $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
           if ($sql_fetch_one['to_id'] != $user_id && $sql_fetch_one['from_id'] != $user_id) {
            return false;
        }
            $query = mysqli_query($sqlConnect, "DELETE FROM " . T_MESSAGES . " WHERE `id` = {$message_id}");
          
            if ($query) {
                if (isset($sql_fetch_one['media']) AND !empty($sql_fetch_one['media'])) {
                    @unlink($sql_fetch_one['media']);
                    $delete_from_s3 = DeleteFromToS3($sql_fetch_one['media']);
                }
                return true;
            } else {
                return false;
            }
       
        return false;
    }
}
function GetMessages($id, $data = array(),$limit = 50) {
    global $ask, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $chat_id = Secure($id);

    if (!empty($data['chat_user'])) {
        $chat_user = $data['chat_user'];
    } else {
        $chat_user = UserData($chat_id);
    }


    $where = "((`from_id` = {$chat_id} AND `to_id` = {$ask->user->id} AND `to_deleted` = '0') OR (`from_id` = {$ask->user->id} AND `to_id` = {$chat_id} AND `from_deleted` = '0'))";

    // count messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $data['last_id'] = Secure($data['last_id']);
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $data['first_id'] = Secure($data['first_id']);
        $db->where('id', $data['first_id'], '<');
    }

    $count_user_messages = $db->getValue(T_MESSAGES, "count(*)");
    $count_user_messages = $count_user_messages - $limit;
    if ($count_user_messages < 1) {
        $count_user_messages = 0;
    }

    // get messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $db->where('id', $data['first_id'], '<');
    }

    $get_user_messages = $db->orderBy('id', 'ASC')->get(T_MESSAGES, array($count_user_messages, $limit));

    $messages_html = '';

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }
 
    $update_seen = array();
   
      
    $last_file_view = '';
          
    foreach ($get_user_messages as $key => $message) {
         

    

           $filename    = (!empty( $message->media)) ? GetMedia( $message->media) : '';
           $fname       = Secure($message->mediaFileName);
     
         if (isset($filename) && !empty($filename)) {
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
            $file           = '';
            $media_file     = '';
            $icon_size                  = 'fa-2x';
            $start_link     = "<a href=" . $filename . ">";
            $end_link       = '</a>';
            $file_extension = strtolower($file_extension);
          

            if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
                $media_file .= "<a href='" . $filename  . "' target='_blank'><img src='" .$filename . "' alt='image' class='image-file pointer'></a>";
                
            }
            if ($file_extension == 'pdf') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-pdf-o"></i> ' . $fname; 
            }
            if ($file_extension == 'txt') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-text-o"></i> ' . $fname; 
            }
            if ($file_extension == 'zip' || $file_extension == 'rar' || $file_extension == 'tar') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-archive-o"></i> ' . $fname; 
            }
            if ($file_extension == 'doc' || $file_extension == 'docx') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-word-o"></i> ' . $fname;
            }
            if ($file_extension == 'mp3' || $file_extension == 'wav') {
               
                    $media_file .= LoadPage('messages/ajax/audio',array('MEDIASOUND' =>$filename));
                
            }
            if (empty($file)) {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-o"></i> ' . $fname;
            } 
            if ($file_extension == 'mp4' || $file_extension == 'mkv' || $file_extension == 'avi' || $file_extension == 'webm' || $file_extension == 'mov') {
                $media_file .= LoadPage('messages/ajax/video',array('MEDIAVIDEO' =>$filename));;
            }  
  
                     if (isset($media_file) && !empty($media_file)) {
                $last_file_view = $media_file;
            } else {
                $last_file_view = $start_link . $file . $end_link;
            }
           //return (!empty($last_file_view)) ? $last_file_view ;
        }else{
             $last_file_view = '';
        }
       
        

                  
     if ($return_method == 'html') {
                $message_type = 'incoming';
                $messgefromsender = 'notAplicable';
                if ($message->from_id == $ask->user->id) {
                    $message_type = 'outgoing';
                   

                }
               
                $messages_html .= LoadPage("messages/ajax/$message_type", array(
                'ID' => $message->id,
                'AVATAR' => $chat_user->avatar,
                'NAME' => $chat_user->name,
                'TIME' =>  Time_Elapsed_String($message->time),
                'TEXT' => Markup($message->text),
                'MEDIA'=> (!empty($last_file_view)) ? $last_file_view : ''
                
              
                
                

            ));

        }
        if ($message->seen == 0 && $message->to_id == $ask->user->id) {
            $update_seen[] = $message->id;
        }
    }

    if (!empty($update_seen)) {
        $update_seen = implode(',', $update_seen);
        $update_seen = $db->where("id IN ($update_seen)")->update(T_MESSAGES, array('seen' => time()));
    }

    return (!empty($messages_html)) ? $messages_html : $get_user_messages;
}
function SeenMessage($message_id) {
    global $sqlConnect,$db,$ask;
    $message_id   = Secure($message_id);
    $query        = mysqli_query($sqlConnect, " SELECT `seen` FROM " . T_MESSAGES . " WHERE `id` = {$message_id}");
    $fetched_data = mysqli_fetch_assoc($query);
    if ($fetched_data['seen'] > 0) {
        $data         = array();
        $data['time'] = date('c', $fetched_data['seen']);
        $data['seen'] = Time_Elapsed_String($fetched_data['seen']);
        return $data;
    } else {
        return false;
    }
}
function GetMessageButton($username = '') {
    global $ask, $db, $lang;
    if (empty($username)) {
        return false;
    }
    if (IS_LOGGED == false) {
        return false;
    }

    $button_text  = $lang->message;

    return LoadPage('buttons/message', array(
        'TEXT' => $button_text,
        'USERNAME' => $username,
    ));
}


function GetFollowingSug($limit, $query) {
    global $ask, $sqlConnect;
    $data = array();
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($query)) {
        return false;
    }
    $query_one_search = " WHERE ((`username` LIKE '%" . Secure($query) . "%') OR CONCAT( `first_name`,  ' ', `last_name` ) LIKE  '%" . Secure($query) . "%')";
    $user_id          = Secure($ask->user->id);
    $query_one        = "SELECT `id` FROM " . T_USERS;
    $query_one .= $query_one_search;
    $logged_user_id = Secure($ask->user->id);
    //$query_one .= " AND (`user_id` NOT IN (SELECT `blocked` FROM " . T_BLOCKS . " WHERE `blocker` = '{$logged_user_id}') AND `user_id` NOT IN (SELECT `blocker` FROM " . T_BLOCKS . " WHERE `blocked` = '{$logged_user_id}') ';
    $query_one .= " AND (`id` IN (SELECT `user_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` = {$user_id} AND `user_id` <> {$user_id} AND `active` = '1') OR `id` IN (SELECT `follower_id` FROM " . T_FOLLOWERS . " WHERE `follower_id` <> {$user_id} AND `user_id` = {$user_id} )) AND `active` = '1'";
    $query_one .= " LIMIT {$limit}";
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $user_data           = UserData($fetched_data['id']);
        $html_fi['username'] = $user_data->username;
        $html_fi['label']    = $user_data->name;
        $html_fi['img']      = $user_data->avatar;
        $data[]              = $html_fi;
    }
    if (empty($data)) {
        $query = "SELECT `id` FROM " . T_USERS . " {$query_one_search} AND `id` <> {$user_id}";
        //$query .= " AND `id` NOT IN (SELECT `blocked` FROM " . T_BLOCKS . " WHERE `blocker` = '{$logged_user_id}') AND `user_id` NOT IN (SELECT `blocker` FROM " . T_BLOCKS . " WHERE `blocked` = '{$logged_user_id}')";
        $query .= " LIMIT {$limit}";

        $sql = mysqli_query($sqlConnect, $query);
        while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $user_data           = UserData($fetched_data['id']);
            $html_fi['username'] = $user_data->username;
            $html_fi['label']    = $user_data->name;
            $html_fi['img']      = $user_data->avatar;
            $data[]              = $html_fi;
        }
    }
    return $data;
}
function UserCanComment($mainid,$user_id, $question_id){
    global $ask,$sqlConnect,$db;
    if( $user_id == 0 || $question_id == 0 ){
        return false;
    }
    //When a user answer to a post, the post owner and the user who answered can comment.
    $sql = 'SELECT * FROM '.T_QUESTIONS.' WHERE ( `user_id` = '.Secure($mainid).' AND `ask_user_id` = '.Secure($user_id).' AND `id` = '.Secure($question_id).' AND `ask_question_id` = 0 ) OR ( `user_id` = '.Secure($mainid).' AND `ask_user_id` = '.Secure($user_id).' AND `ask_question_id` = '.Secure($question_id).' );';

    $question = $db->rawQuery($sql);
    //var_dump(count($question));
    if(count($question) === 2){
        return true;
    }else{
        return false;
    }
}
function GetQuestionReplies($question_id){
    global $ask,$sqlConnect,$db;
    if( $question_id == 0 ){
        return false;
    }
    $data         = array();
    $query_one = 'SELECT * FROM ' . T_QUESTIONS . ' WHERE `replay_question_id` = ' . Secure($question_id) . ' ORDER BY `time` DESC LIMIT 5;';
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    while ($sql_fetch_one = mysqli_fetch_assoc($sql_query_one)) {
        $p = (object)$sql_fetch_one;
        $posts = QuestionData($p);
        $posts->is_replay = true;
        $data[] = $posts;
    }
    return $data;
}
function GetQuestionAnswers($id){
    global $ask,$db;
    $data = $db->where('ask_question_id',$id)->getOne(T_QUESTIONS,array('count(*) as total'));
    return $data->total;
}
function GetQuestionShares($id){
    global $ask,$db;
    $data = $db->where('shared_question_id',$id)->getOne(T_QUESTIONS,array('count(*) as total'));
    return $data->total;
}
function GetQuestionReply($id){
    global $ask,$db;
    $data = $db->where('replay_question_id',$id)->getOne(T_QUESTIONS,array('count(*) as total'));
    return $data->total;
}
function GetTotalAdsEarning(){
    global $db;
    $data = $db->rawQuery('SELECT SUM(`'.T_USERADS_DATA.'`.spend) AS total FROM `'.T_USERADS_DATA.'`');
    return number_format($data[0]->total,2, '.', '');
}
function GetCurrentYearAdsEarning(){
    global $db;
    $start = strtotime("1 January ".date('Y')." 12:00am");
    $end = strtotime("31 December ".date('Y')." 11:59pm");
    $data = $db->where('dt',date('Y-m-d H:i:s A', $start),'>=')->where('dt',date('Y-m-d H:i:s A', $end),'<=')->get(T_USERADS_DATA,1,array('SUM(`'.T_USERADS_DATA.'`.spend) AS total'));
    return number_format($data[0]->total,2, '.', '');
}
function GetCurrentMonthAdsEarning(){
    global $db;
    $start = strtotime("1 ".date('M')." ".date('Y')." 12:00am");
    $end = strtotime(cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'))." ".date('M')." ".date('Y')." 11:59pm");
    $data = $db->where('dt',date('Y-m-d H:i:s A', $start),'>=')->where('dt',date('Y-m-d H:i:s A', $end),'<=')->get(T_USERADS_DATA,1,array('SUM(`'.T_USERADS_DATA.'`.spend) AS total'));
    return number_format($data[0]->total,2, '.', '');
}
function GetCurrentDayAdsEarning(){
    global $db;
    $start = strtotime(date('M')." ".date('d').", ".date('Y')." 12:00am");
    $end = strtotime(date('M')." ".date('d').", ".date('Y')." 11:59pm");
    $data = $db->where('dt',date('Y-m-d H:i:s A', $start),'>=')->where('dt',date('Y-m-d H:i:s A', $end),'<=')->get(T_USERADS_DATA,1,array('SUM(`'.T_USERADS_DATA.'`.spend) AS total'));
    return number_format($data[0]->total,2, '.', '');
}
function UnPromoteQuestions(){
    global $db;
    $start = strtotime(date('M')." ".date('d').", ".date('Y')." 12:00am");
    $db->where('promoted', $start,'<=');
    $db->update(T_QUESTIONS,array('promoted'=>0));
    $db->where('name', 'last_promote_question_update')->update(T_CONFIG, array('value' => time()));
}
function GetQuestions($data = array('page' => 'home', 'filter_by' => 'all', 'after_post_id' => 0, 'before_post_id' => 0, 'ids' => array(),'limit' => 5, 'order' => 'DESC')){
    global $db,$ask,$user,$sqlConnect;
    $data['order_field'] = "`".T_QUESTIONS."`.`id`";

    $subquery_one = " `".T_QUESTIONS."`.`id` > 0 ";
    if (!empty($data['after_post_id']) && is_numeric($data['after_post_id']) && $data['after_post_id'] > 0) {
        $data['after_post_id'] = Secure($data['after_post_id']);
        $subquery_one          = " `".T_QUESTIONS."`.`id` < " . $data['after_post_id'] . " AND `".T_QUESTIONS."`.`id` <> " . $data['after_post_id'];
    } else if (!empty($data['before_post_id']) && is_numeric($data['before_post_id']) && $data['before_post_id'] > 0) {
        $data['before_post_id'] = Secure($data['before_post_id']);
        $subquery_one           = " `".T_QUESTIONS."`.`id` > " . $data['before_post_id'] . " AND `".T_QUESTIONS."`.`id` <> " . $data['before_post_id'];
    }
    $subquery_two = "";
    $orderby_text = "";
    $limit_text = "";

    //home page
    if( isset($data['page']) && trim($data['page']) == 'home' ){
        $subquery_two .= '
        AND
        promoted = 0
        AND
        (
          user_id = ' . $user->id . ' AND
          replay_question_id = 0 AND
          public = 1 AND
          promoted = 0
        )
        OR(
        (
          public = 1 AND
          promoted = 0 AND
          replay_question_id = 0 AND
          user_id IN (SELECT `user_id` FROM `'.T_FOLLOWERS.'` WHERE `follower_id` = '.$user->id.') 
        )
        OR
        (
          public = 1 AND
          promoted = 0 AND
          ask_question_id > 0 AND
          ask_user_id = '.$user->id.' 
        ))
        ';
    }

    //Near by question
    if( isset($data['page']) && trim($data['page']) == 'nearby' ){
        $subquery_two .= '
        AND
        promoted = 0 AND ask_user_id = 0 AND ask_question_id = 0 AND replay_user_id = 0 AND replay_question_id = 0 AND shared_user_id = 0  AND shared_question_id = 0 
        AND
        ROUND( ( 6371 * acos(cos(radians('. $ask->user->lat .')) * cos(radians(`lat`)) * cos(radians(`lng`) - radians('. $ask->user->lng .')) + sin(radians('. $ask->user->lat .')) * sin(radians(`lat`)))) ,1) <= ' . $ask->config->nearby_question_distance;
    }

    if( isset($data['page']) && trim($data['page']) == 'uid' ){
        $user_data = Secure($data['user_data']);
        $subquery_two .= ' 
        AND
        promoted = 0
        AND
        (
          user_id = ' . $user_data . ' AND
          is_anonymously = 0 AND
          replay_question_id = 0 AND
          public = 1
        )
        OR
        (
          public = 1 AND
          ask_user_id = ' . $user_data . '
        )
        OR
        (
          public = 0 AND
          ask_user_id = ' . $user_data . '
        )';
    }

    if( isset($data['page']) && trim($data['page']) == 'timeline' ){
        $user_data = Secure($data['user_data']);
        $loggedin_query = '';
        if (IS_LOGGED == true) {
            $loggedin_query = 'OR(
                (
                  public = 0 AND
                  user_id = ' . $ask->user->id . '
                )';
        }
        $subquery_two .= '
        AND
        promoted = 0
        AND
        (
          user_id = ' . $user_data . ' AND
          is_anonymously = 0 AND
          replay_question_id = 0 AND
          public = 1
        )
        '.$loggedin_query.'
        OR
        (
          public = 1 AND
          ask_user_id = ' . $user_data . '
        )
        OR
        (
          public = 0 AND
          ask_user_id = ' . $user_data . '
        )
        ';
        if (IS_LOGGED == true) {
            $subquery_two .= ')';
        }
    }

    if( isset($data['page']) && trim($data['page']) == 'promoted' ){
        $subquery_two .= ' AND promoted > 0 ';
        $data['limit'] = 1;
        $data['order_field'] = 'rand()';
        $data['order'] = '';
    }

    if (!empty($data['ids']) AND is_array($data['ids']) ) {
        $subquery_two .= " AND ".T_QUESTIONS.".id NOT IN (" . implode(',',$data['ids']) .") ";
    }

    if (empty($data['limit']) or !is_numeric($data['limit']) or $data['limit'] < 1) {
        $data['limit'] = 5;
    }

    $query_text = 'SELECT * FROM '. T_QUESTIONS . ' WHERE ( (' . $subquery_one . ') ' . $subquery_two;

    if( isset($data['page']) && trim($data['page']) == 'trending' ){
        $query_text = '
            SELECT 
              '.T_QUESTIONS.'.*,
              (SELECT count(*) FROM '.T_QUESTIONS.' q WHERE '.T_QUESTIONS.'.id = q.ask_question_id) + COUNT('.T_LIKES.'.id) AS score
            FROM
              '.T_QUESTIONS.'
              LEFT OUTER JOIN '.T_LIKES.' ON ('.T_QUESTIONS.'.id = '.T_LIKES.'.question_id)
            WHERE (' . $subquery_one . ') ' . $subquery_two.'  AND `public` = 1
            GROUP BY
              '.T_QUESTIONS.'.id,
              '.T_QUESTIONS.'.ask_question_id
            HAVING 
                score > 0  
        ';
        $data['order_field'] = 'score';
        $data['order'] = 'DESC';
        $data['limit'] = 5;
    }

    $offset_text = '';
    if (isset($data['after_post_id']) && is_numeric($data['after_post_id'])) {
        $offset_text = ' AND `id` > ' . (int)$data['after_post_id'];
    }

    $limit   = Secure($data['limit']);
    if (isset($data['order'])) {
        $orderby_text = " ORDER BY ".$data['order_field']." " . Secure($data['order']);
    } else {
        $orderby_text = " ORDER BY ".$data['order_field']." " . Secure($data['order']);
    }

    if($data['order'] === 'DESC'){
        if (isset($data['after_post_id']) && is_numeric($data['after_post_id'])) {
            $offset_text = ' AND '.$data['order_field'].' < ' . (int)$data['after_post_id'];
        }
    }else{
        if (isset($data['after_post_id']) && is_numeric($data['after_post_id'])) {
            $offset_text = ' AND '.$data['order_field'].' > ' . (int)$data['after_post_id'];
        }
    }

    $limit_text = " LIMIT " . $limit;

    $query_text .= ' ) '.$offset_text . $orderby_text . $limit_text;

    $data = array();
    $sql  = mysqli_query($sqlConnect, $query_text);

    while ($fetched_data = mysqli_fetch_object($sql)) {
        $post = QuestionData($fetched_data);
        if (is_object($post)) {
            $data[] = $post;
        }
    }
    return $data;
}

function GetUsersByName($name = '', $friends = false, $limit = 25) {
     global $sqlConnect, $ask;
    if (IS_LOGGED == false || !$name) {
        return false;
    }
    $user        = $ask->user->id;
    $name        = Secure($name);
    $data        = array();
    $sub_sql     = "";
    $t_users     = T_USERS;
    $t_followers = T_FOLLOWERS;
    if ($friends == true) {
        $sub_sql = "
        AND ( `id` IN (SELECT `follower_id` FROM $t_followers WHERE `follower_id` <> {$user}  AND `active` = '1')  OR
        `id` IN (SELECT `following_id` FROM $t_followers WHERE  `following_id` <> {$user} AND `active` = '1'))";
    }
    $limit_text = '';
    if (!empty($limit) && is_numeric($limit)) {
        $limit      = Secure($limit);
        $limit_text = 'LIMIT ' . $limit;
    }
   
    $query = mysqli_query($sqlConnect,  "SELECT `id` FROM " . T_USERS . " WHERE `id` <> {$user} AND `username`  LIKE '%$name%' {$sub_sql} $limit_text");
  
   while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = UserData($fetched_data['id']);
    }
   
        return $data;
}
function RegisterAdminNotification($registration_data = array()) {
    global $sqlConnect, $ask;
    if (isLogged() == false || empty($registration_data) || empty($registration_data['text'])) {
        return false;
    }
    if (empty($registration_data['full_link']) || empty($registration_data['recipients'])) {
        return false;
    }
    if (!is_array($registration_data['recipients']) || count($registration_data['recipients']) < 1) {
        return false;
    }
    $text  = $registration_data['text'];
    $link  = $registration_data['full_link'];
    $admin = $ask->user->id;
    $time  = time();
    $sql   = 'INSERT INTO `notifications` (`notifier_id`,`recipient_id`,`type`,`text`,`url`,`time`) VALUES';
    $val   = array();
    
    foreach ($registration_data['recipients'] as $user_id) {
        if ($admin != $user_id) {
            $val[] = "('$admin','$user_id','admin_notification','$text','$link','$time')";
        }
    }

    $query = mysqli_query($sqlConnect, ($sql . implode(',', $val)));
   
    return $query;
}
function GetUserIds() {
    global $sqlConnect, $ask;
    if (isLogged() == false ) {
        return false;
    }
    $data  = array();
    $admin = $ask->user->id;
    $query = mysqli_query($sqlConnect, "SELECT `id` FROM " .T_USERS. " WHERE active = '1' AND `id` <> {$admin}");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = $fetched_data['id'];
    }
    return $data;
}
function GetAllUsersByType($type = 'all') {
    global $sqlConnect;
    $data      = array();
    $query_one = " SELECT `id` FROM " . T_USERS;
    if ($type == 'active') {
        $query_one .= " WHERE `active` = '1'";
    } else if ($type == 'inactive') {
        $query_one .= " WHERE `active` = '0' OR `active` = '2'";
    } else if ($type == 'all') {
        $query_one .= "";
    }
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $data[] = UserData($fetched_data['id']);
    }
    return $data;
}
function GetUsersByTime($type = 'week') {
    global $sqlConnect;
    $types = array('week','month','3month','6month','9month','year');
    if (empty($type) || !in_array($type, $types)) {
        return array();
    }
    $data      = array();
    $end = time() - (60 * 60 * 24 * 7);
    $start = time() - (60 * 60 * 24 * 14);
    if ($type == 'month') {
        $end = time() - (60 * 60 * 24 * 30);
        $start = time() - (60 * 60 * 24 * 60);
    }
    if ($type == '3month') {
        $end = time() - (60 * 60 * 24 * 61);
        $start = time() - (60 * 60 * 24 * 150);
    }
    if ($type == '6month') {
        $end = time() - (60 * 60 * 24 * 151);
        $start = time() - (60 * 60 * 24 * 210);
    }
    if ($type == '9month') {
        $end = time() - (60 * 60 * 24 * 211);
        $start = time() - (60 * 60 * 24 * 300);
    }
    if ($type == 'year') {
        $end = time() - (60 * 60 * 24 * 365);
    }
    $sub1 = " WHERE `last_active` >= '{$start}' ";
    $sub2 = " AND `last_active` <= '{$end}' ";
    if ($type == 'year') {
        $sub2 = "";
    }
    $query_one = " SELECT `id` FROM " . T_USERS.$sub1.$sub2;
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $data[] = UserData($fetched_data['id']);
    }
    return $data;
}
