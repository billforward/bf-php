<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Address tests for BillForward PHP Client Library.\n";

use Bf_Account;
use Bf_Profile;
use Bf_Address;
use BFPHPClientTest\TestConfig;
Class Bf_Address_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	// commented out because I don't want to create more addresses
	public function testCreateAddressDirectly() {
		$config = self::$config;

		$profileID = $config->getUsualProfileID();
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

		var_export($createdAddress);
	}
}