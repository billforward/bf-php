<?php
namespace BFPHPClientTest;
echo "Running Bf_Address tests for BillForward PHP Client Library.\n";

use Bf_Address;
use Bf_Profile;
Class Bf_AddressTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testUpdateAddressIndirectly() {
		$config = self::$config;

		// // No endpoint for getting address by ID
		// $addressID = $config->getUsualAddressID();
		// $address = Bf_Address::getByID($addressID);
		// TODO API: fix getByID of address
		// TODO API: fix getAll of address

		// get parent profile
		$profileID = $config->getUsualProfileID();
		$profile = Bf_Profile::getByID($profileID);
		$addresses = $profile->getAddresses();

		// use first address of profile
		$firstAddress = $addresses[0];
		$address = $firstAddress;

		// take a peek at address before update
		// var_export($address); print "\n";

		$cityBefore = $address->city;

    	$uniqueString = time();
    	$newCity = 'Neo Tokyo '.$uniqueString;
		$address->city = $newCity;

		// // Broken endpoint for saving address
		// $updatedAddress = $address->save();
		// TODO API: fix direct update of address
		$updatedProfile = $profile->save();
		$updatedAddresses = $updatedProfile->getAddresses();
		$updatedFirstAddress = $updatedAddresses[0];
		$updatedAddress = $updatedFirstAddress;

		// take a peek at address after update
		// var_export($updatedAddress);

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
