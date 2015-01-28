<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

class Bf_AccountTest extends PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		TestBase::initialize();
	}

	public function testGetAll()
    {	    	
		$accounts = Bf_Account::getAll();

		$firstAccount = $accounts[0];

		$expected = Bf_Account::getResourcePath()->getEntityName();
		$actual = $firstAccount['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Type of any returned entity matches known value."
			);
    }

    public function testCreate() {
		$account = new Bf_Account();

		$createdAccount = Bf_Account::create($account);

		$actual = $createdAccount;

		$this->assertNotNull(
			$actual,
			"Entity created correctly."
			);
	}

    public function testCreateWithProfileAndAddress() {
    	$account = Models::Account();

		$createdAccount = Bf_Account::create($account);

		$actual = $createdAccount;

		$this->assertNotNull(
			$actual,
			"Nested entity created correctly."
			);
    }

	public function testCreateWithProfile()
    {	
    	// creates a new default account
    	$profile = new Bf_Profile(array(
    		));
		$account = new Bf_Account(array(
			'profile' => $profile
			));

    	$createdAccount = Bf_Account::create($account);

    	$actual = $createdAccount->profile;

    	$this->assertNotNull(
			$actual,
			"Nested entity introduced correctly."
			);
    }

	public function testUpdateWithProfile()
    {	
    	//--Add a Profile to an existing Account
		// construct default model of new account
		$account = new Bf_Account();
		// create modeled account via API
		$createdAccount = Bf_Account::create($account);

		$newEmail = 'always@testing.is.moe';

		// construct model of profile
		$profile = new Bf_Profile(array(
			'email' => $newEmail,
			'firstName' => 'Test',
			));

		// associate profile with account
		$createdAccount->profile = $profile;
		// save changes to account
		$createdAccount->save();

    	$expected = $newEmail;
		$actual = $createdAccount->profile->email;

		$this->assertEquals(
			$expected,
			$actual,
			"Nested entity introduced correctly."
			);
    }
}
