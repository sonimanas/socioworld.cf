<?php 


if (IS_LOGGED == false) {
    $data = array(
        'status' => 400,
        'error' => 'Not logged in'
    );
    echo json_encode($data);
    exit();
}


if ($first == 'upload-receipt' && $ask->config->bank_payment == 'yes') {

        if (!isset($_FILES) && empty($_FILES)) {
            $data = array('status' => 400, 'error' => 'Forbidden' );
            echo json_encode($data);
            exit();
        }


        if (!empty($_FILES['receipt_img']['tmp_name'])) {
            $file_info   = array(
                'file' => $_FILES['receipt_img']['tmp_name'],
                'size' => $_FILES['receipt_img']['size'],
                'name' => $_FILES['receipt_img']['name'],
                'type' => $_FILES['receipt_img']['type'],
                'crop' => array(
                    'width' => 600,
                    'height' => 600
                )
            );
            $file_upload = ShareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $thumbnail = secure($file_upload['filename'], 0);
                $data = array('status' => 200, 'thumbnail' => $thumbnail, 'full_thumbnail' => getMedia($thumbnail));
                $info                  = array();
                $info[ 'user_id' ]     = $user->id;
                $info[ 'receipt_file' ]= $thumbnail;
                $info[ 'created_at' ]  = date('Y-m-d H:i:s');
                $info[ 'description' ] = (isset($_POST['description'])) ? Secure($_POST['description']) : '';
                $info[ 'price' ]       = (isset($_POST['price'])) ? Secure($_POST['price']) : '0';
                $info[ 'mode' ]        = (isset($_POST['mode'])) ? Secure($_POST['mode']) : '';
                $info[ 'approved' ]    = 0;

                $info[ 'created_at' ]    = date('Y-m-d h:i:s');
                $info[ 'approved_at' ]    = 0;
                $info[ 'promote_charge_amount' ]    = 0;

                $saved                 = $db->insert(T_BANK_TRANSFER, $info);

                if(!empty($saved)){
        			
                        $data = array(
                                'message' => $success_icon . __('verif_sentB'),
                                'status' => 200,
        						'url'    => UrlLink('wallet')
        				);
        		   } else {

                         $data    = array(
                            'status'  => '400',
                            'errors' => array(
                                'error_id' => '2',
                                'error' => 'Payment not successful'
                            )
                        );
                   }


                

            } else {

                         $data    = array(
                            'status'  => '400',
                            'errors' => array(
                                'error_id' => '3',
                                'error' => 'Receipt file not attached OR file name empty'
                            )
                        );
                   }

        } else {

                         $data    = array(
                            'status'  => '400',
                            'errors' => array(
                                'error_id' => '4',
                                'error' => 'Receipt file not attached'
                            )
                        );
                   }

	}

if ($first == 'get_modal') {
    $types = array('pro','wallet','pay','subscribe');
    $data['status'] = 400;
    if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
        $user = $db->where('id',$ask->user->id)->getOne(T_USERS);

        $price = 0;
        $question_id = 0;
        $user_id = 0;
         if (!empty($_POST['question_pro'])) {
            $question_id = Secure($_POST['question_pro']);
           
        }
        if (!empty($_POST['price'])) {
            $price = Secure($_POST['price']);
        }
        if (!empty($_POST['user_id'])) {
            $user_id = Secure($_POST['user_id']);
        }

        $ask->show_wallet = 1;

        $html = LoadPage('modals/wallet-payment-modal',array('TYPE' => Secure($_POST['type']),'PRICE' => $price,'USER_ID' => $user_id, 'QUESTION_ID' => $question_id));
        if (!empty($html)) {
            $data['status'] = 200;
            $data['html'] = $html;

        }
        

    }
}
