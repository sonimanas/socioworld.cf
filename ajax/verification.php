<?php 
if (IS_LOGGED == false) {
    $data = array('status' => 400, 'error' => 'Not logged in');
    echo json_encode($data);
    exit();
}


if ($_GET['first'] == 'request') {
	$error          = false;
	$user_id        = $ask->user->id;
	$request_exists = ($db->where('user_id',$user_id)->getValue(T_VERIF_REQUESTS,'count(*)'));
	$post           = (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['message']) || empty($_FILES['identity']));

	if (!empty($request_exists)) {
		$error = $lang->submit_verif_request_error;
	}

	elseif ($post == true) {
		$error = $lang->please_check_details;
	}

	else{
        if (strlen($_POST['first_name']) < 4 || strlen($_POST['first_name']) > 32) {
	        $error = $lang->username_characters_length;
	    } 

	    else if (strlen($_POST['last_name']) > 32) {
	        $error = $lang->ivalid_last_name;
	    } 

	    else if (!file_exists($_FILES['identity']['tmp_name'])) {
	        $error = $lang->ivalid_image_file;
	    } 

	    else if (file_exists($_FILES["identity"]["tmp_name"])) {
	        $image = getimagesize($_FILES["identity"]["tmp_name"]);
	        if (!in_array($image[2], array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_BMP))) {
	            $error = $lang->ivalid_id_file;
	        }
	    }  
	}

	if (empty($error)) {

        $file_info = array(
            'file' => $_FILES['identity']['tmp_name'],
            'size' => $_FILES['identity']['size'],
            'name' => $_FILES['identity']['name'],
            'type' => $_FILES['identity']['type']
        );

        $upload          = ShareFile($file_info);
    	$re_data         = array(
            'user_id'    => $user_id,
            'name'       => Secure($_POST['first_name']) . ' ' . Secure($_POST['last_name']),
            'message'    => Secure($_POST['message']),
            'time'       => time(),
            'media_file' => $upload['filename']
        );

    	$insert = $db->insert(T_VERIF_REQUESTS,$re_data);

    	if ($insert) {
    		$data['status']  = 200;
        	$data['message'] = $success_icon . __('verif_sent');
    	}

    	else{
        	$data['status']  = 500;
        	$data['message'] = $lang->unknown_error;
        }
	}
	else{
		$data['status']  = 400;
		$data['message'] = $error;
	}
}