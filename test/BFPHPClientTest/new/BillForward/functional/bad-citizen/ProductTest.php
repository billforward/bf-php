<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;
Class Bf_Product_OneOffTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		TestBase::initialize();
	}

	protected static $createdProduct = NULL;
	protected static $productDescription = NULL;

	public function testCreate() {    	
		$product = Models::MonthlyNonRecurringProduct();
		self::$productDescription = $product->description;

		$uniqueString = time();
    	$name = "TEST_$uniqueString";

		$product->name = $name;

		self::$createdProduct = Bf_Product::create($product);

		$expected = self::$productDescription;
		$actual = self::$createdProduct->description;

		$this->assertEquals(
			$expected,
			$actual,
			"Entity has expected field with known value."
			);

		$expected = Bf_Product::getResourcePath()->getEntityName();
		$actual = self::$createdProduct['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Type of any returned entity matches known value."
			);
	}

	/**
     * @depends testCreate
     */
	public function testGetAll()
    {
		$products = Bf_Product::getAll();

		$firstProduct = $products[0];

		$expected = Bf_Product::getResourcePath()->getEntityName();
		$actual = $firstProduct['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Type of any returned entity matches known value."
			);
    }

    /**
     * @depends testCreate
     */
	public function testGetByID()
    {
    	$product = self::$createdProduct;
    	$productId = $product->id;
    	
		$fetchedProduct = Bf_Product::getById($productId);

		$description = $fetchedProduct->description;

		$expected = self::$productDescription;
		$actual = $description;

		$this->assertEquals(
			$expected,
			$actual,
			"Product's description matches known value."
			);
    }
}