<?php
class Models {
	public static function Account() {
		//-- Make account with expected profile, profile with expected address
		$address = new Bf_Address(array(
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
		// make one-item list of addresses
		$addresses = array($address);

		$profile = new Bf_Profile(array(
			'email' => 'chill.guy@sharklasers.com',
			'firstName' => 'Ruby',
			'lastName' => 'Red',
			'addresses' => $addresses,
			));
		
		$account = new Bf_Account(array(
			'profile' => $profile,
			));

		return $account;
	}

	public static function UnitOfMeasure() {
		$uom = new Bf_UnitOfMeasure(array(
			'name' => 'CPU',
			'displayedAs' => 'Cycles',
			'roundingScheme' => 'UP',
			));
		return $uom;
	}

	public static function UnitOfMeasure2() {
		$uom = new Bf_UnitOfMeasure(array(
			'name' => 'Bandwidth',
			'displayedAs' => 'Mbps',
			'roundingScheme' => 'UP',
			));
		return $uom;
	}

	public static function MonthlyProduct() {
		$product = new Bf_Product(array(
			'productType' => 'non-recurring',
			'state' => 'prod',
			'name' => 'Monthly non-recurring',
			'description' => 'Purchaseables to which customer has a non-renewing, monthly entitlement',
			'durationPeriod' => 'months',
			'duration' => 1,
			));
		return $product;
	}

	public static function FastProduct() {
		$product = new Bf_Product(array(
			'productType' => 'recurring',
			'state' => 'prod',
			'name' => 'Quickly recurring',
			'description' => 'Purchaseables to which customer has an automatically-renewing, 3-minutely entitlement',
			'durationPeriod' => 'minutes',
			'duration' => 3,
			));
		return $product;
	}

	public static function PricingComponentTiers() {
		$tiers = array(
			new Bf_PricingComponentTier(array(
			'lowerThreshold' => 0,
			'upperThreshold' => 0,
			'pricingType' => 'unit',
			'price' => 0,
			)),
			new Bf_PricingComponentTier(array(
			'lowerThreshold' => 1,
			'upperThreshold' => 10,
			'pricingType' => 'unit',
			'price' => 1,
			)),
			new Bf_PricingComponentTier(array(
			'lowerThreshold' => 11,
			'upperThreshold' => 1000,
			'pricingType' => 'unit',
			'price' => 0.5,
			))
			);
		return $tiers;
	}

	public static function PricingComponentTiers2() {
		$tiers = array(
			new Bf_PricingComponentTier(array(
			'lowerThreshold' => 0,
			'upperThreshold' => 0,
			'pricingType' => 'unit',
			'price' => 0,
			)),
			new Bf_PricingComponentTier(array(
			'lowerThreshold' => 1,
			'upperThreshold' => 10,
			'pricingType' => 'unit',
			'price' => 0.10,
			)),
			new Bf_PricingComponentTier(array(
			'lowerThreshold' => 11,
			'upperThreshold' => 1000,
			'pricingType' => 'unit',
			'price' => 0.05,
			))
			);
		return $tiers;
	}
}