<?php

require 'assets/import/PayPal/vendor/autoload.php';
$paypal = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        $ask->config->paypal_id,
        $ask->config->paypal_secret
    )
);
$paypal->setConfig(
    array(
        'mode' => $ask->config->paypal_mode
    )
);