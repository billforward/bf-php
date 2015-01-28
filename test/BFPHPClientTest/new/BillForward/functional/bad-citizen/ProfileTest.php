<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

class Bf_ProfileTest extends \PHPUnit_Framework_TestCase {
	protected static $models = NULL;
	protected static $created = NULL;

	public static function setUpBeforeClass() {
		TestBase::initialize();
		self::makeRequiredEntities();
	}

	public static function makeRequiredEntities() {
		$models = array(
			'account' => Models::AccountWithJustProfile(),
			);
		$created = array(
			'account' => Bf_Account::create($models['account'])
			);

		self::$models = $models;
		self::$created = $created;
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
			"Type of any returned entity matches known value."
			);
    }

    protected static $fetchedProfile = NULL;

    public function testGetByID() {
    	$account = self::$created['account'];
    	$accountID = $account->id;

    	$profile = $account->profile;
    	$profileID = $profile->id;

		self::$fetchedProfile = Bf_Profile::getByID($profileID);

		$expected = $accountID;
		$actual = $profile->accountID;

		$this->assertEquals(
			$expected,
			$actual,
			"Field on fetched entity matches known value."
			);
    }

    public function testGetProfileViaAccount() {    	
    	$account = self::$created['account'];
    	$accountID = $account->id;

		$fetchedAccount = Bf_Account::getByID($accountID);

		$profile = $fetchedAccount->profile;

		$expected = $accountID;
		$actual = $profile->accountID;

		$this->assertEquals(
			$expected,
			$actual,
			"Field on fetched entity matches known value."
			);
    }

    /**
     * @depends testGetByID
     */
	public function testContent()
    {
		$accountModel = self::$models['account'];
		$profileModel = $accountModel->profile;

		$expected = $profileModel->email;
		$actual = self::$fetchedProfile->email;

		$this->assertEquals(
			$expected,
			$actual,
			"Profile's email matches known value."
			);
    }

    protected static $newModelName = NULL;

    /**
     * @depends testContent
     */
    public function testUpdateModel()
    {	
    	$profile = self::$fetchedProfile;

    	$uniqueString = time();
    	$changeNameTo = "Test $uniqueString";

    	self::$newModelName = $changeNameTo;

		$firstNameBefore = $profile->firstName;

		$profile->firstName = $changeNameTo;

		$firstNameAfter = $profile->firstName;

		$expected = $changeNameTo;
		$actual = $firstNameAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Profile's email is able to be changed in model."
			);
    }

    protected static $updatedProfile = NULL;

    /**
     * @depends testUpdateModel
     */
    public function testUpdate()
    {	
    	$profile = self::$fetchedProfile;

    	// persist model changes from previous test
		self::$updatedProfile = $profile->save();

		$firstNameAfter = self::$updatedProfile->firstName;

		$expected = self::$newModelName;
		$actual = $firstNameAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Profile model change is able to be persisted to API."
			);
    }

    /**
     * @depends testUpdate
     */
    public function testSetAddressInModel()
    {	
    	$profile = self::$updatedProfile;

    	$expectedKey = 'city';
    	$expected = 'space';

    	$address = new Bf_Address(array(
    		$expectedKey => $expected
    		));
    	$profile->addresses = array($address);

    	$profileAddress = $profile->addresses[0];
    	$actual = $profileAddress->$expectedKey;

    	$this->assertEquals(
			$expected,
			$actual,
			"Profile's address is able to be changed in model through set."
			);
    }

    /**
     * @depends testSetAddressInModel
     */
    public function testSetAddressByDereference()
    {	
    	$profile = self::$updatedProfile;

    	$expectedKey = 'city';
    	$expected = 'place';

    	$address = new Bf_Address(array(
    		$expectedKey => $expected
    		));

    	$profile->addresses[0] = $address;

    	$profileAddress = $profile->addresses[0];
    	$actual = $profileAddress->$expectedKey;

    	$this->assertEquals(
			$expected,
			$actual,
			"Profile's address is able to be changed in model through array dereference."
			);
    }
}