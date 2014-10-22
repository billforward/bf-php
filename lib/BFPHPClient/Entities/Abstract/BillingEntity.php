<?php
abstract class Bf_BillingEntity extends \ArrayObject {
	public function getClient() {
		return $this->_client;
	}

	public function setClient(BillForwardClient &$client = NULL) {
		$this->_client = $client;
	}

	public static function getSingletonClient() {
		return BillForwardClient::getDefaultClient();
	}

	protected $_client = NULL;

	protected $_registeredEntities = array();
	protected $_registeredEntityArrays = array();

	public function __construct(array $stateParams = NULL, $client = NULL) {
		if (is_null($stateParams)) {
			$stateParams = array();
		}

		if (is_null($client)) {
			// default to singletonClient
			$client = static::getSingletonClient();
		}
		$this->setClient($client);

		$this->doUnserialize($stateParams);
	}

	public static function __set_state($stateParams)
    {
    	$entityClass = static::getClassName();
        $obj = new $entityClass($stateParams);
        return $obj;
    }

	public static function getClassName() {
		// late static bindings, PHP version 5.3+ only
		return get_called_class();
	}

	public static function getResourcePath() {
		return static::$_resourcePath;
	}

	/**
	 * Returns (if exist; otherwise OutOfBoundsException) the first entity from a collection whose properties match
	 * those provided.
	 * @param array the collection of entities to search
	 * @param array the array of properties upon which to match
	 * @return BillingEntity The matching BillingEntity
	 */
	public static function fromCollectionFindFirstWhoMatchesProperties(array $collection, array $props) {
		$matchingEntities = self::fromCollectionFindAllWhoMatchProperties($collection, $props);

		if (sizeof($matchingEntities) > 0) {
			return $matchingEntities[0];
		} else {
			throw new OutOfBoundsException('No entity matched the provided properties.');
		}	
	}

	/**
	 * Returns all entities from a collection whose properties match
	 * those provided.
	 * @param array the collection of entities to search
	 * @param array the array of properties upon which to match
	 * @return BillingEntity[] The matching BillingEntities
	 */
	public static function fromCollectionFindAllWhoMatchProperties(array $collection, array $props) {
		$matches = array();
		foreach($collection as $entity) {
			$allPropsMatched = TRUE;
			foreach($props as $key => $value) {
				if ($entity[$key] !== $value) {
					$allPropsMatched = FALSE;
				}
			}
			if ($allPropsMatched) {
				array_push($matches, $entity);
			}
		}
		return $matches;
	}

	/**
	 * Adds the '@type' key to entity, in an order-sensitive
	 * fashion (so unserialization works as expected on the API end)
	 * @param string the value to assing to @type
	 * @param array the existing params used to serialize entity
	 * @return array the new state params to use for initializing the entity
	 */
	protected function addTypeParam($type, $stateParams) {
		if (is_null($stateParams)) {
			$stateParams = array();
		}
		$newStateParams = array(
			'@type' => $type
			);
		foreach($stateParams as $key => $value) {
			if ($key !== '@type') {
				$newStateParams[$key] = $value;	
			}
		}
		return $newStateParams;
	}

	protected function doUnserialize(array $json) {
		foreach($json as $key => $value) {
			$this->offsetSet($key, $value);
		}
	}

	protected function unserializeEntity($key, $class, array $json) {
		$this->_registeredEntities[$key] = $class;
		if (array_key_exists($key, $json)) {
			$newEntity = $this->buildEntity($class, $json[$key]);
			$this->offsetSet($key, $newEntity);
		}
	}

	protected function unserializeArrayEntities($key, $class, array $json) {
		$this->_registeredEntityArrays[$key] = $class;
		if (array_key_exists($key, $json)) {
			// var_export($key);
			// var_export($json);
			// var_export($json[$key]);
			$entities = $this->buildEntityArray($class, $json[$key]);
			$this->offsetSet($key, $entities);
		}
	}

	protected function buildEntityArray($class, array $input) {
		$entityArray = array();
		foreach ($input as $index => $value) {
			$newEntity = $this->buildEntity($class, $value);
			$entityArray[$index] = $newEntity;
		}
		return $entityArray;
	}

	protected function buildEntity($class, $constructArgs) {
		$client = $this->getClient();
		if (!is_array($constructArgs)) {
			// maybe we have been given an entity already?
			if (is_object($constructArgs) && method_exists($constructArgs, 'getClassName')) {
				$itsClass = $constructArgs->getClassName();
				if ($itsClass == $class) {
					// use the provided entity. Hm, should we set its client to match our own? Let's not for now.
					return $constructArgs;
				} else {
					$thisClass = $this->getClassName();
					$errorString = "Construction of entity <$class> inside entity <$thisClass> failed; expected array or <$class> entity. Instead received object (with class <$itsClass>).";
					throw new \Exception($errorString);
				}
			} else {
				$thisClass = $this->getClassName();
				$errorString = "Construction of entity <$class> inside entity <$thisClass> failed; expected array or <$class> entity. Instead received: <$constructArgs>";
				throw new \Exception($errorString);
			}
		} else {
			$newEntity = new $class($constructArgs, $client);
		}
		return $newEntity;
	}

	public function getSerialized() {
		$outputArray = array();
		foreach ($this as $key => $value) {
			$outputArray[$key] = static::serializeField($value);
		}
		return $outputArray;
	}

	public static function serializeField($value) {
		if (is_object($value) && method_exists($value, 'getSerialized')) {
			// if it's an entity
			return $value->getSerialized();
		} else if (is_array($value)) {
			// if it's an array of entities (or worse)?
			$tempArray = array();
			// recursively serialize its contents, and return them
			foreach ($value as $key2 => $value2) {
				$tempArray[$key2] = static::serializeField($value2);
			}
			return $tempArray;
		} else {
			// just a normal kvp
			return $value;
		}
	}

	public static function getByID($id, $options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		// empty IDs are no good!
		if (!$id) {
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
		}

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/$id";
		$fullRoute = $apiRoute.$endpoint;

		$response = $client->doGet($fullRoute, $options);
		$json = $response->json();

		$results = $json['results'];

		$firstMatch = $results[0];

		return new $entityClass($firstMatch, $client);
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$fullRoute = $apiRoute;

		$response = $client->doGet($fullRoute, $options);

		$json = $response->json();
		$results = $json['results'];

		$entities = array();

		foreach($results as $value) {
			$constructedEntity = new $entityClass($value, $client);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}

	public static function getAllThenGrabAllWithProperties(array $properties, $options = NULL, $customClient = NULL) {
		$entities = self::getAll($options, $customClient);
		return self::fromCollectionFindAllWhoMatchProperties($entities, $properties);
	}

	public static function getAllThenGrabFirstWithProperties(array $properties, $options = NULL, $customClient = NULL) {
		$entities = self::getAll($options, $customClient);
		return self::fromCollectionFindFirstWhoMatchesProperties($entities, $properties);
	}

    public function &__get($name) {
    	$ref =& $this[$name];
    	return $ref;
    }

    public function &__set($name, $value) {
    	$this->offsetSet($name, $value);
    	$ref =& $this->$name;
    	return $ref;	
    }

    public function offsetSet($name, $value) {
    	if (array_key_exists($name, $this->_registeredEntities)) {
			// if we expect an Entity in this field, parse it now
    		$expectedClass = $this->_registeredEntities[$name];
    		$entity = $this->buildEntity($expectedClass, $value);
    		$parsedValue = $entity;
    	} else if (array_key_exists($name, $this->_registeredEntityArrays)) {
    		// if we expect an Entity[] in this field, parse it now
    		$expectedClass = $this->_registeredEntityArrays[$name];
    		$entities = $this->buildEntityArray($expectedClass, $value);
    		$parsedValue = $entities;
    	} else {
    		// otherwise this is just a normal field; parse as such
			$parsedValue = $value;
    	}
    	return parent::offsetSet($name, $parsedValue);
    }

    public function getJson() {
    	return json_encode($this, JSON_PRETTY_PRINT);
    }

    public function printJson() {
    	echo "\n";
    	print_r($this->getJson());
    	echo "\n";
    }
}
