<?php
namespace BFPHPClientTest;
echo "Running Bf_Product tests for BillForward PHP Client Library.\n";

use Bf_Product;
Class Bf_ProductTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testGetAll()
    {	
    	// short alias
    	$client = self::$client;
    	
		$products = Bf_Product::getAll();

		$firstProduct = $products[0];

		$expected = Bf_Product::getResourcePathStatic()->getEntityName();
		$actual = $firstProduct['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }

	public function testGetByID()
    {	
    	// short alias
    	$client = self::$client;
    	$config = self::$config;

    	$productId = $config
    	->getUsualProductID();
    	
		$product = Bf_Product::getById($productId);

		$description = $product
		->description;

		$expected = $config
    	->getUsualProductDescription();
		$actual = $description;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that product's description matches known value."
			);
    }
}
