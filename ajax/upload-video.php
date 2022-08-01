<?php 
if (IS_LOGGED == false || $ask->config->upload_system != 'on') {
    $data = array('status' => 400, 'error' => 'Not logged in');
    echo json_encode($data);
    exit();
}



// $max_user_upload = $ask->config->user_max_upload;
// if ($ask->user->is_pro != 1 && $ask->user->uploads >= $max_user_upload && $ask->config->go_pro == 1){
//     $data = array('status' => 401);
//     echo json_encode($data);
//     exit();
// }

if (!empty($_FILES['video']['tmp_name'])) {
    if (!IsAdmin()) {
        if ($ask->user->user_upload_limit != '0') {
            if ($ask->user->user_upload_limit != 'unlimited') {
                if (($ask->user->uploads + $_FILES['video']['size']) >= $ask->user->user_upload_limit) {
                    $max  = size_format($ask->user->user_upload_limit);
                    $data = array('status' => 402,'message' => ($lang->file_is_too_big .": $max"));
                    echo json_encode($data);
                    exit();
                }
            }
        }
        else{
            if ($ask->config->upload_system_type == '0') {
                if ($ask->config->max_upload_all_users != '0' && ($ask->user->uploads + $_FILES['video']['size']) >= $ask->config->max_upload_all_users) {
                    $max  = size_format($ask->config->max_upload_all_users);
                    $data = array('status' => 402,'message' => ($lang->file_is_too_big .": $max"));
                    echo json_encode($data);
                    exit();
                }
            }
            elseif ($ask->config->upload_system_type == '1') {
                if ($ask->user->is_pro == '0' && ($ask->user->uploads + $_FILES['video']['size']) >= $ask->config->max_upload_free_users && $ask->config->max_upload_free_users != 0) {
                    $max  = size_format($ask->config->max_upload_free_users);
                    $data = array('status' => 402,'message' => ($lang->file_is_too_big .": $max"));
                    echo json_encode($data);
                    exit();
                }
                elseif ($ask->user->is_pro > '0' && ($ask->user->uploads + $_FILES['video']['size']) >= $ask->config->max_upload_pro_users && $ask->config->max_upload_pro_users != 0) {
                    $max  = size_format($ask->config->max_upload_pro_users);
                    $data = array('status' => 402,'message' => ($lang->file_is_too_big .": $max"));
                    echo json_encode($data);
                    exit();
                }
            }
        }
    }

    // if ($_FILES['video']['size'] > $ask->config->max_upload) {
    //     $max  = size_format($ask->config->max_upload);
    //     $data = array('status' => 402,'message' => ($lang->file_is_too_big .": $max"));
    //     echo json_encode($data);
    //     exit();
    // }

    $allowed           = 'mp4,mov,webm,mpeg';

    $new_string        = pathinfo($_FILES['video']['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        $data = array('status' => 400, 'error' => $lang->file_not_supported);
        echo json_encode($data);
        exit();
    }

	$file_info = array(
        'file' => $_FILES['video']['tmp_name'],
        'size' => $_FILES['video']['size'],
        'name' => $_FILES['video']['name'],
        'type' => $_FILES['video']['type'],
        'allowed' => 'mp4,mov,webm,mpeg'
    );
    $file_upload = ShareFile($file_info);
    if (!empty($file_upload['filename'])) {
        $explode3  = @explode('.', $file_upload['name']);
        $file_upload['name'] = $explode3[0];
    	$data   = array('status' => 200, 'file_path' => $file_upload['filename'], 'file_name' => $file_upload['name']);
        $update = array('uploads' => ($ask->user->uploads += $file_info['size']));
        $db->where('id',$ask->user->id)->update(T_USERS,$update);
        $data['uploaded_id'] = $db->insert(T_UPLOADED,array('user_id' => $ask->user->id,
                                                            'path' => $file_upload['filename'],
                                                            'time' => time()));

    } 

    else if (!empty($file_upload['error'])) {
        $data = array('status' => 400, 'error' => $file_upload['error']);
    }
}
?>