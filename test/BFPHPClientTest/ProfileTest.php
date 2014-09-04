<?php
namespace BFPHPClientTest;
echo "Running Bf_Profile tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Account;
use Bf_Profile;
use Bf_Address;
Class Bf_ProfileTest extends \PHPUnit_Framework_TestCase {

	protected static $usualProfile = NULL;
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	protected function recycleUsualProfile() {
		if (is_null(self::$usualProfile)) {
			return $this->getUsualProfile();
		} else {
			return self::$usualProfile;
		}
    }

	public function testGetAll()
    {	    	
		$profiles = Bf_Profile::getAll();

		$firstProfile = $profiles[0];

		$expected = Bf_Profile::getResourcePath()->getEntityName();
		$actual = $firstProfile['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }

    protected function getUsualProfile() {
    	// short alias
    	$client = self::$client;
    	$config = self::$config;
    	
    	$testAccountId = $config
    	->getUsualAccountID();

		$account = Bf_Account::getById($testAccountId);

		$profile = $account
		->getProfile();

		self::$usualProfile = $profile;

		return $profile;
    }

	public function testContent()
    {	
    	$config = self::$config;

    	$profile = $this->recycleUsualProfile();
		$email = $profile->email;

		$expected = $config->getUsualAccountsProfileEmail();
		$actual = $email;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that profile's email matches known value."
			);
    }

    public function testUpdateModel()
    {	
    	$profile = $this->recycleUsualProfile();
    	$changeNameTo = 'Best';

		$firstNameBefore = $profile
		->firstName;

		$profile
		->firstName = $changeNameTo;

		$firstNameAfter = $profile
		->firstName;

		$expected = $changeNameTo;
		$actual = $firstNameAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that profile's email is able to be changed in model."
			);
    }

    public function testUpdate()
    {	
    	$profile = $this->recycleUsualProfile();

    	$uniqueString = time();

    	$changeNameTo = 'Test '.$uniqueString;

    	// note: affected by side-effect of previous test; has changes to model
		$firstNameBefore = $profile
		->firstName;

		$profile
		->firstName = $changeNameTo;

		$profile->save();

		$updated = $this->getUsualProfile();

		$firstNameAfter = $updated
		->firstName;

		$expected = $changeNameTo;
		$actual = $firstNameAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that profile's firstName is able to be changed in model."
			);
    }

    /*public function testSetAddress()
    {	
    	$profile = $this->recycleUsualProfile();

    	var_export($profile); print "\n";

    	$address1 = new Bf_Address(array(
    		'city' => 'space'
    		));

    	$profile->addresses = array($address1);

    	var_export($profile); print "\n";

    	// $updatedProfile = $profile->save();

    	// var_export($updatedProfile); print "\n";
    }*/

    public function testSetAddressByDereference()
    {	
    	$profile = $this->recycleUsualProfile();

    	// var_export($profile); print "\n";

    	$expectedKey = 'city';
    	$expected = 'space';

    	$address = new Bf_Address(array(
    		$expectedKey => $expected
    		));

    	$profile->addresses[0] = $address;

    	// var_export($profile); print "\n";

    	$profileAddress = $profile->getAddresses()[0];
    	$actual = $profileAddress->$expectedKey;

    	$this->assertEquals(
			$expected,
			$actual,
			"Asserting that profile's address is able to be changed in model through array dereference."
			);

    	// $updatedProfile = $profile->save();

    	// var_export($updatedProfile); print "\n";
    }
}