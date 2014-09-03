<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Account tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Organisation;
use Bf_Account;
use BFPHPClientTest\TestConfig;
Class Bf_Account_OneOffTest extends \PHPUnit_Framework_TestCase {
	// you will need to fill these in before running this one-off test
	private $token1 = '';
	private $url1 = 'https://api-sandbox.billforward.net/2014.223.0/';

	// you will need to fill these in before running this one-off test
	private $token2 = '';
	private $url2 = 'http://localhost:8080/RestAPI/';
	public function testGetFromDifferentClientsUsingSingleton() {
		$client1 = new BillForwardClient($this->token1, $this->url1);
		BillForwardClient::setDefaultClient($client1);

		$orgs1 = Bf_Organisation::getMine();

		$firstOrg1 = $orgs1[0];
		$firstOrgID1 = $firstOrg1->id;

		$client2 = new BillForwardClient($this->token2, $this->url2);
		BillForwardClient::setDefaultClient($client2);

		$orgs2 = Bf_Organisation::getMine();

		$firstOrg2 = $orgs2[0];
		$firstOrgID2 = $firstOrg2->id;

		// you should check also whether they appear in the expected order (1 first, then 2)
		$this->assertNotEquals(
			$firstOrgID1,
			$firstOrgID2,
			"Asserting that organisationMine changes after making a client with different credentials"
			);
	}

	public function testGetFromDifferentClientsUsingReference() {
		$client2 = new BillForwardClient($this->token2, $this->url2);
		$client1 = new BillForwardClient($this->token1, $this->url1);

		$orgs1 = Bf_Organisation::getMine(null, $client1);

		$firstOrg1 = $orgs1[0];
		$firstOrgID1 = $firstOrg1->id;


		$orgs2 = Bf_Organisation::getMine(null, $client2);

		$firstOrg2 = $orgs2[0];
		$firstOrgID2 = $firstOrg2->id;

		// you should check also whether they appear in the expected order (1 first, then 2)
		$this->assertNotEquals(
			$firstOrgID1,
			$firstOrgID2,
			"Asserting that organisationMine changes after making a client with different credentials"
			);
	}

	public function testGetFromDifferentClientsUsingReference2() {
		$client2 = new BillForwardClient($this->token2, $this->url2);
		$client1 = new BillForwardClient($this->token1, $this->url1);

		$accs1 = Bf_Account::getAll(null, $client1);

		$firstAcc1 = $accs1[0];
		$firstAccID1 = $firstAcc1->id;

		$accs2 = Bf_Account::getAll(null, $client2);

		$firstAcc2 = $accs2[0];
		$firstAccID2 = $firstAcc2->id;

		// you should check also whether they appear in the expected order (1 first, then 2)
		$this->assertNotEquals(
			$firstAccID1,
			$firstAccID2,
			"Asserting that organisationMine changes after making a client with different credentials"
			);
	}
}