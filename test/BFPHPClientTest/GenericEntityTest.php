<?php
namespace BFPHPClientTest;
echo "Running Bf_GenericEntity tests for BillForward PHP Client Library.\n";

use Bf_GenericEntity;
use Bf_Account;
use Bf_ResourcePath;
Class Bf_GenericEntityTest extends \PHPUnit_Framework_TestCase {
	protected static $usualAccount = NULL;
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	protected static function recycleUsualAccount() {
		if (is_null(self::$usualAccount)) {
			return self::getUsualAccount();
		} else {
			return self::$getUsualAccount;
		}
	}

	protected static function getUsualAccount() {
		$config = self::$config;

		$entityPath = new Bf_ResourcePath('accounts');
		$accountID = $config->getUsualAccountID();

		$account = Bf_GenericEntity::getByID($accountID, NULL, NULL, $entityPath);

		return $account;
	}

	public function testGetAccountUsingGeneric() {
		$account = $this->recycleUsualAccount();

		$entityPath = Bf_Account::getResourcePathStatic();

		$expected = $entityPath->getEntityName();
		$actual = $account['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of returned generic entity matches known value."
			);
	}

	public function testUpdateProfileViaParentUsingGeneric() {
		/**This test makes a generic entity of an account (which has nested entities)
		 * We show that we can update the model of nested objects within this generic entity.
		 * Caveats:
		 * - we can't use -> to indirect deep into nested entities; only direct descendants of
		 	generic entity have a magic method for -> notation.
		 * - we can't store references like $account['profile'] as a variable; this copies by value.
		 	so we would be editing a copy of the model, and our changes would not affect the
		 	generic entity.
		 * - we can't use $account['profile']->save(), because $account['profile'] is not an entity.
		 *
		 * Probably all of these could be fixed by unserializing all arrays as more generic entities. Ugh.
		 */

		$account = $this->recycleUsualAccount();

		$nameBefore = $account['profile']['firstName'];

		// take a peek at profile before update
		var_export($account['profile']);

    	$uniqueString = time();
    	$newName = 'Test2 '.$uniqueString;
		$account['profile']['firstName'] = $newName;

		$updatedAccount = $account->save();

		// take a peek at profile after update
		var_export($updatedAccount['profile']);

		$nameAfter = $updatedAccount['profile']['firstName'];

		$this->assertNotEquals(
			$nameBefore,
			$nameAfter,
			"Asserting that profile's name changes after update."
			);

		$expected = $newName;
		$actual = $nameAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that profile's name changes to expected string after update."
			);
	}
}
