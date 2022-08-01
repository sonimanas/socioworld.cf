<?php 


if (IS_LOGGED == false) {
    $data = array(
        'status' => 400,
        'error' => 'Not logged in'
    );
    echo json_encode($data);
    exit();
}


if ($first == 'bank_replenish' && $ask->config->bank_payment == 'yes') {
    if (empty($_FILES["thumbnail"]) || empty($_POST['amount']) ) {
        $error = $lang->please_check_details;
    }



     if (empty($error)) {
        $amount = Secure($_POST['amount']);
        $amountCharge = Secure($_POST['_amount']);

        $question_id = Secure($_POST['question_id']);
        $amount = $amount/100;
        $description = 'Wallet';
        
            
         $transf     =  array(
                        'promote_charge_amount' =>  $ask->config->promote_question_cost,
                        'question_id' =>  Secure($question_id),
                        'user_id' => $ask->user->id,
                        'description' =>   $description,
                        'price'       =>   Secure($amount),
                        'mode'        =>   'questionPromotion');
        
        
            
        $fileInfo      = array(
            'file' => $_FILES["thumbnail"]["tmp_name"],
            'name' => $_FILES['thumbnail']['name'],
            'size' => $_FILES["thumbnail"]["size"],
            'type' => $_FILES["thumbnail"]["type"],
            'types' => 'jpeg,jpg,png,bmp,gif'
            
            
        );
        $media                   = ShareFile($fileInfo);
        $transf['receipt_file']  = $media['filename'];
         if(!empty($transf['receipt_file'])) {

        $insert_id               = $db->insert(T_BANK_TRANSFER, $transf);       
    
         
         if(!empty($insert_id)){
            
                $data = array(
                        'message' => $success_icon . __('request_sent'),
                        'status' => 200,
                        'url'    => UrlLink('wallet'));
           } }
           else{

               
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

        }}

    


if ($first == 'get_modal') {
    $types = array('pro','wallet','pay','subscribe','rent');
    $data['status'] = 400;
    if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
        $user = $db->where('id',$ask->user->id)->getOne(T_USERS);

        $price = 0;
        $question_id = 0;
        $user_id = 0;
        if (!empty($_POST['price'])) {
            $price = Secure($_POST['price']);
        }
        if (!empty($_POST['question_id'])) {
            $question_id = Secure($_POST['question_id']);
        }
        if (!empty($_POST['user_id'])) {
            $user_id = Secure($_POST['user_id']);
        }

        $ask->show_wallet = 0;
        if (!empty($user) && $_POST['type'] == 'pro' && $user->wallet >= intval($ask->config->pro_pkg_price)) {
            $ask->show_wallet = 1;
        }
        elseif (!empty($user) && $_POST['type'] == 'pay' && !empty($question_id)) {
            $question = $db->where('id',$question_id)->getOne(T_QUESTIONS);
            if ($user->wallet >= $question->rent_price) {
                $ask->show_wallet = 1;
            }
        }
        elseif (!empty($user) && $_POST['type'] == 'rent' && !empty($question_id)) {
            $question = $db->where('id',$question_id)->getOne(T_QUESTIONS);
            if ($user->wallet >= $question->rent_price) {
                $ask->show_wallet = 1;
            }
        }

        if ($_POST['type'] == 'subscribe' && !empty($user_id)) {
            $new_user = $db->where('id',$user_id)->getOne(T_USERS);
            if (!empty($new_user) && $new_user->subscriber_price > 0 && $user->wallet >= $new_user->subscriber_price) {
                $ask->show_wallet = 1;
            }
        }

        $html = LoadPage('modals/wallet-payment-modal',array('TYPE' => Secure($_POST['type']),'PRICE' => $price,'question_id' => $question_id,'USER_ID' => $user_id));
        if (!empty($html)) {
            $data['status'] = 200;
            $data['html'] = $html;
        }
    }

}
