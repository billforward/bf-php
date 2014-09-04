<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Account tests for BillForward PHP Client Library.\n";

use Bf_Account;
use Bf_Profile;
use Bf_Address;
use BFPHPClientTest\TestConfig;
Class Bf_Account_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
    	
    	// creates a new default account
		$account = new Bf_Account();

		$response = Bf_Account::create($account);
	}

    public function testCreateWithProfileAndAddress() {
    	//-- Make account with profile, profile with addresses..
		$address = new Bf_Address(array(
			'addressLine1' => 'address line 1',
		    'addressLine2' => 'address line 2',
		    'addressLine3' => 'address line 3',
		    'city' => 'London',
		    'province' => 'London',
		    'country' => 'United Kingdom',
		    'postcode' => 'SW1 1AS',
		    'landline' => '02000000000',
		    'primaryAddress' => true
			));
		// make one-item list of addresses
		$addresses = array($address);

		// construct model of profile, associating addresses to it
		$profile = new Bf_Profile(array(
			'email' => 'always@testing.is.moe',
			'firstName' => 'Test',
			'addresses' => $addresses
			));

		// construct model of account, associating profile to it
		$account = new Bf_Account(array(
			'profile' => $profile,
			));

		// create real account from model, using API
		$createdAcc = Bf_Account::create($account);

		var_export($createdAcc);
    }

	public function testCreateWithProfile()
    {	
    	// creates a new default account
    	$profile = new Bf_Profile(array(
    		));
		$account = new Bf_Account(array(
			'profile' => $profile
			));

    	var_export($account); print "\n";

    	$createdAccount = Bf_Account::create($account);

    	var_export($createdAccount); print "\n";

    	// TODO API: API should not ignore our profile when it has no params!
    }

	public function testUpdateWithProfile()
    {	
    	//--Add a Profile to an existing Account
		// construct default model of new account
		$account = new Bf_Account();
		// create modeled account via API
		$createdAccount = Bf_Account::create($account);

		// construct model of profile
		$profile = new Bf_Profile(array(
			'email' => 'always@testing.is.moe',
			'firstName' => 'Test',
			));

		// associate profile with account
		$createdAccount->profile = $profile;
		// save changes to account
		$createdAccount->save();

    	var_export($createdAccount); print "\n";
    }
}