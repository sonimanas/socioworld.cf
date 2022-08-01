<?php
if ($first == 'change_display_mode') {
    if (!empty($_GET['mode']) && in_array($_GET['mode'], array('night','day'))) {
        setcookie("mode", $_GET['mode'], time() + (10 * 365 * 24 * 60 * 60), "/");
        $ask->displaymode = $_GET['mode'];
        header("Content-type: application/json");
        echo json_encode(array(
            'status' => 200,
            'message' => 'done'
        ));
        exit();
    }
}
if ($first == 'hide-announcement' && IS_LOGGED === true) {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $announcement_id = Secure($_POST['id']);
        $user_id         = $ask->user->id;
        $insert_data     = array(
            'announcement_id' => $announcement_id,
            'user_id'         => $user_id
        );

        $db->insert(T_ANNOUNCEMENT_VIEWS,$insert_data);
        $data['status'] = 200;
    }
}