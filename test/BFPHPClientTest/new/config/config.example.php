<?php
/*
 * Copy paste the contents of this `config.example.php` to your own local `config.php` in the same directory.
 * These test credentials should refer to a scratch account which you're happy to have filled with chuff created by tests.
 */

$config = array(
	'credentials' => array(
		'urlRoot' => 'https://api-sandbox.billforward.net:443/v1/',
		'access_token' => 'INSERT_ACCESS_TOKEN_HERE',
		// For use with Proxy.app: change to '127.0.0.1:4651'.
		'curlProxy' => null
		),

	// Fill these in to run situational tests
	'situational' =>  array(
		// required for Authorize.Net gateway tests:
		'AuthorizeNetLoginID' => '',
		'AuthorizeNetTransactionKey' => ''
		
		// required for Authorize.Net token tests:
		'customerProfileID' => 28476855,
		'customerPaymentProfileID' => 25879733,
		'cardLast4Digits' => 4444
		)
);