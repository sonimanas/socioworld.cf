<?php
if ($first == 'get-paid-promote') {
    if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['paymentId']) && isset($_GET['PayerID']) && isset($_GET['question_id']) && isset($_GET['user_id'])) {
        if (!is_array(GetWalletReplenishingDone($_GET['paymentId'], $_GET['PayerID']))) {
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

if ($first == 'get-paid') {
    if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['paymentId']) && isset($_GET['PayerID'])) {


        if (!is_array(GetWalletReplenishingDone($_GET['paymentId'], $_GET['PayerID']))) {
            if (ReplenishingUserBalance($_GET['amount'])) {
                $_SESSION['replenished_amount'] = $_GET['amount'];
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

if (empty($_REQUEST['user_id']) || !IS_LOGGED) {
    exit("Undefined Alien ಠ益ಠ");
}
$is_owner = false;
if ($_REQUEST['user_id'] == $user->id || IsAdmin()) {
    $is_owner = true;
}
$user_id = Secure($_REQUEST['user_id']);

if ($first == 'create') {
    if( $is_owner === false ){
        $error = $error_icon . __('please_check_details');
    }

    $request   = array();
    $request[] = (empty($_POST['name']) || empty($_POST['website']));
    $request[] = (empty($_POST['headline']) || empty($_POST['description']));
    $request[] = (empty($_POST['audience-list']) || empty($_POST['gender']));
    $request[] = (empty($_POST['bidding']) || empty($_FILES['media']));
    $request[] = (empty($_POST['location']) || empty($_POST['appears']));
    $request[] = ($ask->user->wallet == 0 || $ask->user->wallet == '0.00');

    if (in_array(true, $request)) {
        $error = $error_icon . __('please_check_details');
    } else {
        if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 100) {
            $error = $error_icon . __('invalid_company_name');
        } else if (!filter_var($_POST['website'], FILTER_VALIDATE_URL) || $_POST['website'] > 3000) {
            $error = $error_icon . __('enter_valid_url');
        } else if (strlen($_POST['headline']) < 5 || strlen($_POST['headline']) > 200) {
            $error = $error_icon . __('enter_valid_title');
        }
        if (!in_array($_FILES["media"]["type"], $ad_media_types)) {
            $error = $error_icon . __('select_valid_img_vid');
        } else if (gettype($_POST['audience-list']) != 'array' || count($_POST['audience-list']) < 1) {
            $error = $error_icon . __('please_check_details');
        } else if ($_POST['bidding'] != 'clicks' && $_POST['bidding'] != 'views') {
            $error = $error_icon . __('please_check_details');
        } else if (!in_array($_POST['appears'], array(
            'post',
            'sidebar',
            'video'
        ))) {
            $error = $error_icon . __('please_check_details');
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
                $error = $error_icon . __('select_valid_img');
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
                $error = $error_icon . __('select_valid_vid');
            }
        } else if ($_FILES["media"]["size"] > $ask->config->max_image_upload_size || true) {
            $maxUpload = SizeUnits($ask->config->maxUpload);
            $error     = $error_icon . str_replace('{file_size}', $maxUpload, __('file_too_big'));
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
        $data                          = array(
            'message' => $success_icon . __('ad_added'),
            'status' => 200,
            'url' => UrlLink('ads')
        );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    } else {
        $data = array(
            'message' => $error,
            'status' => 500
        );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}

if ($first == 'update') {
    if( $is_owner === false ){
        $error = $error_icon . __('please_check_details');
    }
    $request   = array();
    $request[] = (empty($_GET['ad-id']) || !is_numeric($_GET['ad-id']));
    $request[] = (empty($_POST['name']) || empty($_POST['website']));
    $request[] = (empty($_POST['headline']) || empty($_POST['description']));
    $request[] = ($_GET['ad-id'] < 1 || empty($_POST['gender']));
    $request[] = (empty($_POST['bidding']) || empty($_POST['location']));
    $request[] = (empty($_POST['audience-list']) || !is_array($_POST['audience-list']));
    if (in_array(true, $request)) {
        $error = $error_icon . __('please_check_details');
    } else {
        if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 100) {
            $error = $error_icon . __('invalid_company_name');
        } else if (!filter_var($_POST['website'], FILTER_VALIDATE_URL) || $_POST['website'] > 3000) {
            $error = $error_icon . __('enter_valid_url');
        } else if (strlen($_POST['headline']) < 5 || strlen($_POST['headline']) > 200) {
            $error = $error_icon . __('enter_valid_title');
        }
        if (!in_array($_POST['bidding'], array(
            'clicks',
            'views'
        ))) {
            $error = $error_icon . __('please_check_details');
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
            'posted' => time()
        );
        $table       = T_USER_ADS;
        $adid        = Secure($_GET['ad-id']);
        $user_id     = $ask->user->id;
        $db->where("id", $adid)->where("user_id", $user_id)->update($table, $update_data);
        $data = array(
            'message' => $success_icon . __('ad_saved'),
            'status' => 200,
            'url' => UrlLink('ads')
        );
        if (isset($_GET['a']) && $_GET['a'] == 1) {
            //TODO:update when make admin
            $data['url'] = UrlLink('index.php?link1=admincp&page=user_ads');
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    } else {
        $data = array(
            'message' => $error,
            'status' => 500
        );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}

if ($first == 'get_estimated_users') {

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
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }

}

if ($first == 'remove') {
    if( $is_owner === false ){
        $error = $error_icon . __('please_check_details');
        $data = array(
            'message' => $error,
            'status' => 500
        );
    }else {
        $data['status'] = 304;
        if (DeleteUserAd(Secure($_POST['id']))) {
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($first == 'ts') {
    $data    = array(
        'status' => 304
    );
    $request = (!empty($_GET['ad_id']) && is_numeric($_GET['ad_id']));
    $user_id = $ask->user->id;
    if ($request === true) {
        $ad_id   = Secure($_GET['ad_id']);
        $ad_data = $db->where('id', $ad_id)->where('user_id', $user_id)->getOne(T_USER_ADS);
        if (!empty($ad_data)) {
            $up_data = array(
                'status' => (($ad_data->status == 1) ? 0 : 1)
            );
            $db->where('id', $ad_id)->where('user_id', $user_id)->update(T_USER_ADS, $up_data);
            $data['status'] = 200;
            $data['ad']     = ($ad_data->status == 1) ? __('not_active') : $data['ad'] = __('active');
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}



if ($first == 'rads-c' && !empty($_GET['ad_id']) && is_numeric($_GET['ad_id'])) {
    $data = array(
        "status" => 304
    );
    $ad   = Secure($_GET['ad_id']);
    IsConversionExists($ad);
    if (RegisterAdConversionClick($ad)) {
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($first == 'rads-v' && !empty($_GET['ad_id']) && is_numeric($_GET['ad_id'])) {
    $data        = array(
        "status" => 304
    );
    $ad          = Secure($_GET['ad_id']);
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


if ($first == 'bank_replenish' && $ask->config->bank_payment == 'yes') {
	if (empty($_FILES["thumbnail"]) || empty($_POST['amount'])) {
        $error = $lang->please_check_details;
    }
    if (empty($error)) {
    	$amount = Secure($_POST['amount']);
    	$amount = $amount/100;
        $description = 'Wallet';
        $fileInfo      = array(
            'file' => $_FILES["thumbnail"]["tmp_name"],
            'name' => $_FILES['thumbnail']['name'],
            'size' => $_FILES["thumbnail"]["size"],
            'type' => $_FILES["thumbnail"]["type"],
            'types' => 'jpeg,jpg,png,bmp,gif',
			'compress' => false
        );
        $media         = ShareFile($fileInfo);
    
       
		$bankTransferData =  array( 'user_id' => Secure($ask->user->id),
                                    'description' => Secure($description),
                                    'price'       => Secure($amount),
                                     'mode'         => Secure('wallet'));
		
		$bankTransferData['receipt_file']   =  $media['filename'];     
		
    

        	$insert_id = $db->insert(T_BANK_TRANSFER,$bankTransferData);
            if (!empty($insert_id)) {
                $data = array(
                    'message' => $lang->bank_transfer_request,
                    'status' => 200
                );
            }
        }
        else{
            $error = $lang->please_check_details;
            $data = array(
                'status' => 500,
                'message' => $error
            );
        }
    } else {
        $data = array(
            'status' => 500,
            'message' => $error
        );
    }
header("Content-type: application/json");
echo json_encode($data);
exit();

?>