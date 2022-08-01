<?php 

    require_once('assets/import/stripe-php-3.20.0/vendor/autoload.php');
    global $ask;
    $data = array();

    $stripe = array(
        'secret_key' => $ask->config->stripe_secret,
        'publishable_key' => $ask->config->stripe_id
    );
    \Stripe\Stripe::setApiKey($stripe[ 'secret_key' ]);
    $token          = $_POST[ 'stripeToken' ];

    if (empty($token)) {

        $data = array(
            'status' => 400,
            'message' => 'invalid token'
        );
    }

    try {
    $customer = \Stripe\Customer::create(array(
        'source' => $token
    ));
    $price = Secure($_POST['amount']);
    $final_amount = $price * 100;
    $charge   = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        'amount' => $final_amount,
        'currency' => $ask->config->stripe_currency
    ));

    if ($charge) {
       
         $updateUser = $db->where('id', $user->id)->update(T_USERS, ['wallet' => $db->inc($price)]);

            if ($updateUser) {
                    CreatePayment(array(
                    'user_id'   => $user->id,
                    'amount'    => $price,
                    'type'      => 'WALLET',
                    'pro_plan'  => 0,
                    'info'      => 'Replenish My Balance',
                    'via'       => 'Stripe'
                ));
                $data = array(
                    'message' => 'Payment charged',
                    'status' => 200,
                    'location'    => UrlLink('wallet')

                );
           }  else {
            $data = array(
                'status' => 400,
                'message' => 'can not create payment'
            );
        }
      } 
    }
    catch (Exception $e) {
    $data = array(
        'status' => 400,
        'error' => $e->getMessage()
    );
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
    }

