<?php  
     require_once('assets/import/stripe-php-3.20.0/vendor/autoload.php');
		global $ask;

		$stripe = array(
		    'secret_key' => $ask->config->stripe_secret,
		    'publishable_key' => $ask->config->stripe_id
		);
		\Stripe\Stripe::setApiKey($stripe[ 'secret_key' ]);
	if (!empty($_POST[ 'description' ]) && !empty($_POST[ 'price' ]) && !empty($_POST[ 'payType' ]) && !empty($_POST[ 'stripeToken' ])){

		$product        = Secure($_POST[ 'description' ]);
		$realprice      = Secure($_POST[ 'price' ]);
		$price          = Secure($_POST[ 'price' ]);
		$amount         = 0;
		$currency       = strtolower($ask->config->stripe_currency);
		$payType        = Secure($_POST[ 'payType' ]);
		$membershipType = 0;
		$token          = $_POST[ 'stripeToken' ];

		if (empty($token)) {
		    $data = array(
		        'status' => 400,
		        'error' => 'invalid token'
		    );
		}


		try {
		    $customer = \Stripe\Customer::create(array(
		        'source' => $token
		    ));
		    $charge   = \Stripe\Charge::create(array(
		        'customer' => $customer->id,
		        'amount' => $price,
		        'currency' => $currency
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
		                
		                'status' => 200,
		                'message' => 'card successfully chaged'
		                
		            );
		        } else {
		            $data = array(
		                'status' => 400,
		                'error' => 'can not create payment'
		            );
		        }

		    }
		} catch (Exception $e) {
		    $data = array(
		        'status' => 400,
		        'error' => $e->getMessage()
		    );
		}
	} else {

		     $data       = array(
	            'status'     => '400',
	            'error'         => 'Bad Request, Invalid or missing parameter'
	        );
	       
            

	}


		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);
		exit();
?>