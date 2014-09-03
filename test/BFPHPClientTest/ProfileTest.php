<?php
namespace BFPHPClientTest;
echo "Running Bf_Profile tests for BillForward PHP Client Library.\n";

use BfClient;
use Bf_Account;
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
}