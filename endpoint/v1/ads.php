<?php

if (!IS_LOGGED) {

    $data    = array(
        'status'  => '400',
        'errors' => array(
            'error_id' => '1',
            'error_text' => 'Not logged in'
        )
    );
    echo json_encode($data);
    exit();
}

if ($option == 'get-paid-promote') {
    if (isset($_POST['success']) && $_POST['success'] == 1 && isset($_POST['paymentId']) && isset($_POST['PayerID']) && isset($_POST['question_id']) && isset($_POST['user_id'])) {
        if (!is_array(GetWalletReplenishingDone($_POST['paymentId'], $_POST['PayerID']))) {
            if (PromoteQuestion()) {
                header("Location: " . UrlLink('?promoted=1'));
                exit();
            }
        } else {
            header("Location: " . UrlLink(''));
            exit();
        }
    }
}
if ($option == 'get-paid') {
    if (isset($_POST['success']) && $_POST['success'] == 1 && isset($_POST['paymentId']) && isset($_POST['PayerID'])) {


        if (!is_array(GetWalletReplenishingDone($_POST['paymentId'], $_POST['PayerID']))) {
            if (ReplenishingUserBalance($_POST['amount'])) {
                $_SESSION['replenished_amount'] = $_POST['amount'];
                header("Location: " . UrlLink('wallet'));
                exit();
            }
        } else {
            header("Location: " . UrlLink('wallet'));
            exit();
        }


    } else {
        header("Location: " . UrlLink('wallet'));
        exit();
    }
    exit();
}
if ($option == 'sponsored') {
    $data    = array(
        'status'  => '200',
        'data' => GetSideBarAds()
    );
    echo json_encode($data);
    exit();
}

if (empty($_REQUEST['user_id']) || !IS_LOGGED) {
    exit("Undefined Alien ಠ益ಠ");
}
$is_owner = false;
if ($_REQUEST['user_id'] == $user->id || IsAdmin()) {
    $is_owner = true;
}
$user_id = Secure($_REQUEST['user_id']);

if ($option == 'create') {
    if( $is_owner === false ){
        $error = 'please check details';
    }

    $request   = array();
    $request[] = (empty($_POST['name']) || empty($_POST['website']));
    $request[] = (empty($_POST['headline']) || empty($_POST['description']));
    $request[] = (empty($_POST['audience-list']) || empty($_POST['gender']));
    $request[] = (empty($_POST['bidding']) || empty($_FILES['media']));
    $request[] = (empty($_POST['location']) || empty($_POST['appears']));
    $request[] = ($ask->user->wallet == 0 || $ask->user->wallet == '0.00');
    if (in_array(true, $request)) {
             $error = 'please check details';
                 } else {
        if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 100) {
            $error = 'invalid company name';
        } else if (!filter_var($_POST['website'], FILTER_VALIDATE_URL) || $_POST['website'] > 3000) {
            $error = 'enter valid url';
        } else if (strlen($_POST['headline']) < 5 || strlen($_POST['headline']) > 200) {
            $error = 'enter valid title';
        }
        if (!in_array($_FILES["media"]["type"], $ad_media_types)) {
            $error = 'select valid img vid';
        } else if (gettype($_POST['audience-list']) != 'array' || count($_POST['audience-list']) < 1) {
            $error = 'please check details';
        } else if ($_POST['bidding'] != 'clicks' && $_POST['bidding'] != 'views') {
            $error = 'please check details';
        } else if (!in_array($_POST['appears'], array(
            'post',
            'sidebar',
            'video'
        ))) {
            $error = 'please check details';
        } else if (in_array($_POST['appears'], array(
            'post',
            'sidebar'
        ))) {
            $img_types = array(
                'image/png',
                'image/jpeg',
                'image/gif'
            );
            if (!in_array($_FILES["media"]["type"], $img_types)) {
                $error = 'select valid img';
            }
        } else if (in_array($_POST['appears'], array(
            'video'
        ))) {
            $img_types = array(
                'video/mp4',
                'video/mov',
                'video/avi'
            );
            if (!in_array($_FILES["media"]["type"], $img_types)) {
                $error = 'select valid vid';
            }
        } else if ($_FILES["media"]["size"] > $ask->config->max_image_upload_size || true) {
            $maxUpload = SizeUnits($ask->config->maxUpload);
            $error     = str_replace('{file_size}', $maxUpload, 'file too big');
        }
    }
    if (empty($error)) {
        $registration_data             = array(
            'name' => Secure($_POST['name']),
            'url' => Secure($_POST['website']),
            'headline' => Secure($_POST['headline']),
            'description' => Secure($_POST['description']),
            'location' => Secure($_POST['location']),
            'audience' => Secure(implode(',', $_POST['audience-list'])),
            'gender' => Secure($_POST['gender']),
            'bidding' => Secure($_POST['bidding']),
            'posted' => time(),
            'appears' => Secure($_POST['appears']),
            'user_id' => Secure($ask->user->id)
        );
        $fileInfo                      = array(
            'file' => $_FILES["media"]["tmp_name"],
            'name' => $_FILES['media']['name'],
            'size' => $_FILES["media"]["size"],
            'type' => $_FILES["media"]["type"],
            'types' => 'jpg,png,bmp,gif,mp4,avi,mov',
            'compress' => false
        );
        $media                         = ShareFile($fileInfo);
        $registration_data['ad_media'] = $media['filename'];
        $last_id                       = $db->insert(T_USER_ADS, $registration_data);
        if($last_id){
            $registration_data['id'] = $last_id;
            $registration_data['ad_media'] = getmedia($media['filename']);
            $data                          = array(
                'message' => 'Ad successifully added',
                'status' => 200,
                'adData' => $registration_data,
                'url' => UrlLink('ads')
            );
        }else{
            $data = array(
                'error' => 'Can not add this Ad to database: ' . $db->getLastError(),
                'status' => 400
            );
        }

    } else {
        $data = array(
            'error' => $error,
            'status' => 400
        );
    }
}
if ($option == 'update') {
    if( $is_owner === false ){
        $error = 'please check details';
    }
    $request   = array();
    $request[] = (empty($_POST['ad-id']) || !is_numeric($_POST['ad-id']));
    $request[] = (empty($_POST['name']) || empty($_POST['website']));
    $request[] = (empty($_POST['headline']) || empty($_POST['description']));
    $request[] = ($_POST['ad-id'] < 1 || empty($_POST['gender']));
    $request[] = (empty($_POST['bidding']) || empty($_POST['location']));
    $request[] = (empty($_POST['audience-list']) || !is_array($_POST['audience-list']));
    $request[] = (empty($_POST['appears']) || empty($_FILES['media']));
    if (in_array(true, $request)) {
        $error = 'please check details';
    } else {
        if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 100) {
            $error = 'invalid company name';
        } else if (!filter_var($_POST['website'], FILTER_VALIDATE_URL) || $_POST['website'] > 3000) {
            $error = 'enter valid url';
        } else if (strlen($_POST['headline']) < 5 || strlen($_POST['headline']) > 200) {
            $error = 'enter valid title';
        }        if (!in_array($_FILES["media"]["type"], $ad_media_types)) {
            $error = 'select valid img vid';
        } else if (gettype($_POST['audience-list']) != 'array' || count($_POST['audience-list']) < 1) {
            $error = 'please check details';
        } else if ($_POST['bidding'] != 'clicks' && $_POST['bidding'] != 'views') {
            $error = 'please check details';
        } else if (!in_array($_POST['appears'], array(
            'post',
            'sidebar',
            'video'
        ))) {
            $error = 'please check details';
        } else if (in_array($_POST['appears'], array(
            'post',
            'sidebar'
        ))) {
            $img_types = array(
                'image/png',
                'image/jpeg',
                'image/gif'
            );
            if (!in_array($_FILES["media"]["type"], $img_types)) {
                $error = 'select valid img';
            }
        } else if (in_array($_POST['appears'], array(
            'video'
        ))) {
            $img_types = array(
                'video/mp4',
                'video/mov',
                'video/avi'
            );
            if (!in_array($_FILES["media"]["type"], $img_types)) {
                $error = 'select valid vid';
            }
        } else if ($_FILES["media"]["size"] > $ask->config->max_image_upload_size || true) {
            $maxUpload = SizeUnits($ask->config->maxUpload);
            $error     = str_replace('{file_size}', $maxUpload, 'file too big');
        }
    }
    if (empty($error)) {
        $update_data = array(
            'name' => Secure($_POST['name']),
            'url' => Secure($_POST['website']),
            'headline' => Secure($_POST['headline']),
            'description' => Secure($_POST['description']),
            'location' => Secure($_POST['location']),
            'audience' => Secure(implode(',', $_POST['audience-list'])),
            'gender' => Secure($_POST['gender']),
            'bidding' => Secure($_POST['bidding']),
            'appears' => Secure($_POST['appears']),
            'posted' => time()
        );

        $fileInfo                      = array(
            'file' => $_FILES["media"]["tmp_name"],
            'name' => $_FILES['media']['name'],
            'size' => $_FILES["media"]["size"],
            'type' => $_FILES["media"]["type"],
            'types' => 'jpg,png,bmp,gif,mp4,avi,mov',
            'compress' => false
        );
        $media                         = ShareFile($fileInfo);
        $update_data['ad_media'] = $media['filename'];

        $table       = T_USER_ADS;
        $adid        = Secure($_POST['ad-id']);
        $user_id     = $ask->user->id;
        $db->where("id", $adid)->where("user_id", $user_id)->update($table, $update_data);

        $update_data['id'] = $adid;
        $data = array(
            'message' => 'Your ad was successifully edited',
            'status' => 200,
            'adData' => $update_data,
            'url' => UrlLink('ads')
        );
        if (isset($_POST['a']) && $_POST['a'] == 1) {
            //TODO:update when make admin
            $data['url'] = UrlLink('index.php?link1=admincp&page=user_ads');
        }
    } else {
        $data = array(
            'error' => $error,
            'status' => 400
        );
    }
}
if ($option == 'get_estimated_users') {

    $data = array(
        'status' => 304
    );

    if (isset($_POST['estimated_audience']) && isset($_POST['estimated_gender'])) {
        if ($_POST['estimated_gender'] == "All") {
        } else if ($_POST['estimated_gender'] == "male") {
            $db->where('gender', "male");
        } else if ($_POST['estimated_gender'] == "female") {
            $db->where('gender', "female");
        }
        if (!empty($_POST['estimated_audience'])) {
            $db->where('country_id', explode(",", $_POST['estimated_audience']), 'IN');
        }
        $count          = $db->getValue(T_USERS, "count(*)");
        $data['status'] = 200;
        $data['count']  = $count;
    }

}
if ($option == 'remove') {
    if( $is_owner === false ){
        $error = 'please check details';
        $data = array(
            'error' => $error,
            'status' => 400
        );
    }else {
        if (DeleteUserAd(Secure($_POST['id']))) {
            $data['status'] = 200;
            $data['message'] = 'ad removed';
        }else{
            $error = 'Can not delete ad from database';
            $data = array(
                'error' => $error,
                'status' => 400
            );
        }
    }
}
if ($option == 'ts') {
    $data    = array(
        'status' => 400
    );
    $request = (!empty($_POST['ad_id']) && is_numeric($_POST['ad_id']));
    $user_id = $ask->user->id;
    if ($request === true) {
        $ad_id   = Secure($_POST['ad_id']);
        $ad_data = $db->where('id', $ad_id)->where('user_id', $user_id)->getOne(T_USER_ADS);
        if (!empty($ad_data)) {
            $up_data = array(
                'status' => (($ad_data->status == 1) ? 0 : 1)
            );
            $db->where('id', $ad_id)->where('user_id', $user_id)->update(T_USER_ADS, $up_data);
            $data['status'] = 200;
            $data['ad']     = ($ad_data->status == 1) ? 'not active' : $data['ad'] = 'active';
        }
    }
}
if ($option == 'rads-c' && !empty($_POST['ad_id']) && is_numeric($_POST['ad_id'])) {
    $data = array(
        "status" => 400
    );
    $ad   = Secure($_POST['ad_id']);
    IsConversionExists($ad);
    if (RegisterAdConversionClick($ad)) {
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'rads-v' && !empty($_POST['ad_id']) && is_numeric($_POST['ad_id'])) {
    $data        = array(
        "status" => 400
    );
    $ad          = Secure($_POST['ad_id']);
    $get_ad_data = GetUserAdData($ad);
    if ($get_ad_data['bidding'] == 'clicks') {
        RegisterAdConversionClick($ad);
    } else {
        RegisterAdClick($ad);
    }
    $data['status'] = 200;
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
?>