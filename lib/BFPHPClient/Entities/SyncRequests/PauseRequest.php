<?php

class Bf_PauseRequest extends Bf_BillingEntity {

	public static function getByID($id, $options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get by ID support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get All support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}

	/**
	 * Constructs a Bf_PauseRequest model designed for freezing subscriptions.
	 * @param array $stateParams (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $stateParams['dryRun'] Whether to forego persisting the effected changes.
	 * @return Bf_PauseRequest The created pause request model.
	 */
	public static function constructForPausing(
		array $stateParams = array(
			'dryRun' => false
			)
		) {
		$model = new self(array_merge(
			static::getFinalArgDefault(__METHOD__),
			$stateParams));

		return $model;
	}

	/**
	 * Constructs a Bf_PauseRequest model designed for resuming subscriptions.
	 * @param array $stateParams (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $stateParams['dryRun'] Whether to forego persisting the effected changes.
	 * @return Bf_PauseRequest The created resumption request model.
	 */
	public static function constructForResumption(
		array $stateParams = array(
			'dryRun' => false
			)
		) {
		$model = new self(array_merge(
			static::getFinalArgDefault(__METHOD__),
			$stateParams));

		return $model;
	}
}