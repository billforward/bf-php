<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

Class Bf_AddressTest extends \PHPUnit_Framework_TestCase {
	protected static $entities = NULL;

	public static function setUpBeforeClass() {
		TestBase::initialize();
		self::$entities = self::makeRequiredEntities();
	}

	public static function makeRequiredEntities() {
		$models = array(
			'account' => Models::AccountWithJustProfile(),
			);
		$created = array(
			'account' => Bf_Account::create($models['account'])
			);

		return $created;
	}

	public function testCreateAddressDirectly() {
		$account = self::$entities['account'];
		$profile = $account->profile;
		$profileID = $profile->id;

		$address = new Bf_Address(array(
			'profileID' => $profileID,
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
		$createdAddress = Bf_Address::create($address);

		$expected = Bf_Address::getResourcePath()->getEntityName();
		$actual = $createdAddress['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Type of any returned entity matches known value."
			);
	}

	/**
     * @depends testCreateAddressDirectly
     */
	public function testUpdateAddressIndirectly() {
		$account = self::$entities['account'];
		$updatedAccount = Bf_Account::getByID($account->id);

		$profile = $updatedAccount->profile;

		$addresses = $profile->addresses;
		$firstAddress = $addresses[0];
		$address = $firstAddress;

		$cityBefore = $address->city;

    	$uniqueString = time();
    	$newCity = 'Neo Tokyo '.$uniqueString;
		$address->city = $newCity;

		$updatedProfile = $profile->save();
		$updatedAddresses = $updatedProfile->getAddresses();
		$updatedFirstAddress = $updatedAddresses[0];
		$updatedAddress = $updatedFirstAddress;

		$cityAfter = $updatedAddress->city;

		$this->assertNotEquals(
			$cityBefore,
			$cityAfter,
			"Asserting that address's city name changes after update."
			);

		$expected = $newCity;
		$actual = $cityAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that address's city name changes to expected string after update."
			);
	}
}