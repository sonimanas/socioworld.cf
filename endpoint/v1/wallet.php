<?php 


if (empty($_POST['user_id']) || !IS_LOGGED) {
        $data       = array(
            'status'     => '400',
            'error'         => 'You are not logged in'
        );
    echo json_encode($data);
    exit();
}


if ($option == 'bank_payment' && $ask->config->bank_payment == 'yes') {

        if ( empty($_FILES) && empty($_POST['description']) && empty($_POST['price']) && empty($_POST['mode'])) {
            $data       = array(
	            'status'     => '400',
	            'error'         => 'Bad Request, Invalid or missing parameter'
	        );
	       
            

        } else {


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
                $saved                 = $db->insert(T_BANK_TRANSFER, $info);

                if(!empty($saved)){
        			
                        $data = array(
                                
                                'status' => 200,
                                'message' => 'Bank receipt successfully uploaded and is awaiting approval'
        				);
        		   } else {

                         $data    = array(
                            'status'  => '400',
                            'error' => 'Payment not successful'
                        );
                   }


                

            } else {

                         $data    = array(
                            'status'  => '400',
                            'error' => 'Receipt file not attached OR file name empty'
                        );
                   }

        } else {

                         $data    = array(
                            'status'  => '400',
                            'error' => 'Receipt file not attached'
                        );
                   }

              }

	}

    if ($option == 'update_wallet'){

        $uid = (int)$_POST['user_id'];
        if($uid === (int)$user->id){

            if (empty($_POST['amount'])){
                $errors[] = "Please check your details";
            } else {
                $amount = (int)$_POST['amount'];
                $userData = UserData($uid);
                if (!empty ($userData->id) && empty($errors)) {


                    $updateUser = $db->where('id', $user->id)->update(T_USERS, ['wallet' => $db->inc($amount)]);
		        
                    if ($updateUser) {
                        CreatePayment(array(
                            'user_id'   => $user->id,
                            'amount'    => $amount,
                            'type'      => 'WALLET',
                            'pro_plan'  => 0,
                            'info'      => 'Replenish My Balance',
                            'via'       => 'ADMIN'
                        ));
                        $data = array(   
                            'status' => 200,
                            'message' => 'Wallet updated successfully.'
                        );
                    } else {
                        $data = array(
                            'status' => 400,
                            'error' => 'can not create payment'
                        );
                    }

                }
            }
        }else{
            $data = array(
                'status'  => '400',
                'error' => 'You can not update anthor user wallet'
            );
        }

    }

?>