<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_ProductRatePlanMigrationAmendment tests for BillForward PHP Client Library.\n";

use Bf_Subscription;
use Bf_ProductRatePlanMigrationAmendment;
use BFPHPClientTest\TestConfig;
Class Bf_ProductRatePlanMigrationAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testMigrateWithMappingsPeriodEnd() {
		$client = self::$client;

		$subscriptionID = '21E2ECD6-1A20-471D-AA5A-E48B35D67077';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// 'A Sound Plan' 8AE5B829-0D0D-436D-BCDF-5612C410B139
		// 'I love it when a Plan comes together' 		EBA12D79-32B7-4178-904B-574A782B42C6
		$newProductRatePlanID = '8AE5B829-0D0D-436D-BCDF-5612C410B139';

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 10,
			'Bandwidth' => 20
			);

		$createdAmendment = $subscription->migratePlan($componentNameToValueMap, $newProductRatePlanID, 'Immediate', 'AtPeriodEnd');
		var_export($createdAmendment);
	}

	public function testMigrateWithMappingsSpecifiedTime() {
		$client = self::$client;

		$subscriptionID = '21E2ECD6-1A20-471D-AA5A-E48B35D67077';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// 'A Sound Plan' 8AE5B829-0D0D-436D-BCDF-5612C410B139
		// 'I love it when a Plan comes together' 		EBA12D79-32B7-4178-904B-574A782B42C6
		$newProductRatePlanID = '8AE5B829-0D0D-436D-BCDF-5612C410B139';

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 10,
			'Bandwidth' => 20
			);

		$time = time()+2*60; // time 2 mins from now
		$createdAmendment = $subscription->migratePlan($componentNameToValueMap, $newProductRatePlanID, 'Immediate', $time);
		var_export($createdAmendment);
	}

	public function testMigrateWithMappingsNow() {
		$client = self::$client;

		$subscriptionID = '21E2ECD6-1A20-471D-AA5A-E48B35D67077';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// 'A Sound Plan' 8AE5B829-0D0D-436D-BCDF-5612C410B139
		// 'I love it when a Plan comes together' 		EBA12D79-32B7-4178-904B-574A782B42C6
		$newProductRatePlanID = '8AE5B829-0D0D-436D-BCDF-5612C410B139';

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 10,
			'Bandwidth' => 20
			);

		$createdAmendment = $subscription->migratePlan($componentNameToValueMap, $newProductRatePlanID, 'Immediate');
		var_export($createdAmendment);
	}
}