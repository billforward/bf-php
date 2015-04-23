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
			throw new OutOfBoundsException('No entity matched the provided properties. Properties: <'.json_encode($props).'>, collection: <'.json_encode($collection).'>.');
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

	public static function constructEntityFromArgs($entityClass, $constructArgs, $client) {
		$newEntity = new $entityClass($constructArgs, $client);
		return $newEntity;
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
					throw new Bf_UnserializationException($errorString);
				}
			} else {
				$thisClass = $this->getClassName();
				$errorString = "Construction of entity <$class> inside entity <$thisClass> failed; expected array or <$class> entity. Instead received: <$constructArgs>";
				throw new Bf_UnserializationException($errorString);
			}
		} else {
			$newEntity = Bf_BillingEntity::constructEntityFromArgs($class, $constructArgs, $client);
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
		// empty IDs are no good!
		if (!$id) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$encoded = rawurlencode($id);

		$endpoint = "/$encoded";

		try {
			return static::getFirst($endpoint, $options, $customClient);
		} catch(Bf_NoMatchingEntityException $e) {
			// rethrow with better message
			$callingClass = static::getClassName();
			$responseClass = $callingClass;
			throw new Bf_NoMatchingEntityException("No results returned by API for '$callingClass::getByID('$id')' (GET to '$endpoint'). Expected at least 1 '$responseClass' entity.");
		}
	}

	protected static function prefixPathWithController($path) {
		$controller = static::getResourcePath()->getPath();
		$qualified = "$controller/$path";
		return $qualified;
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		return static::getCollection('', $options, $customClient);
	}

	protected static function postEntityAndGrabFirst($endpoint, $entity, $responseEntity = NULL) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		return static::postAndGrabFirst($endpoint, $serial, $client, $responseEntity);
	}

	protected static function postEntityAndGrabCollection($endpoint, $entity, $responseEntity = NULL) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		return static::postAndGrabCollection($endpoint, $serial, $client, $responseEntity);
	}

	protected static function postAndGrabFirst($endpoint, $payload, $customClient = NULL, $responseEntity = NULL) {
		$client = is_null($customClient) ? static::getSingletonClient() : $customClient;
		
		$url = static::prefixPathWithController($endpoint);
		$response = $client->doPost($url, $payload);

		$constructedEntity = static::responseToFirstEntity($response, $client, $responseEntity);
		return $constructedEntity;
	}

	protected static function postAndGrabCollection($endpoint, $payload, $customClient = NULL, $responseEntity = NULL) {
		$client = is_null($customClient) ? static::getSingletonClient() : $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doPost($url, $payload);

		$constructedEntities = static::responseToEntityCollection($response, $client, $responseEntity);
		return $constructedEntities;
	}

	protected static function putAndGrabFirst($endpoint, $payload, $customClient = NULL, $responseEntity = NULL) {
		$client = is_null($customClient) ? static::getSingletonClient() : $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doPut($url, $payload);

		$updatedEntity = static::responseToFirstEntity($response, $client, $responseEntity);
		return $updatedEntity;
	}

	protected static function retireAndGrabFirst($endpoint, $payload, $customClient = NULL, $responseEntity = NULL) {
		$client = is_null($customClient) ? static::getSingletonClient() : $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doRetire($url, $payload);

		$retiredEntity = static::responseToFirstEntity($response, $client, $responseEntity);
		return $retiredEntity;
	}

	protected static function responseToEntityCollection(Bf_RawAPIOutput $response, $client, $responseEntity = NULL) {
		$entityClass = is_null($responseEntity) ? static::getClassName() : $responseEntity;

		$results = $response->getResults();
		$entities = array();

		foreach($results as $value) {
			$constructedEntity = Bf_BillingEntity::constructEntityFromArgs($entityClass, $value, $client);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}

	protected static function responseToFirstEntity(Bf_RawAPIOutput $response, $client, $responseEntity = NULL) {
		$entityClass = is_null($responseEntity) ? static::getClassName() : $responseEntity;

		try {
			$firstMatch = $response->getFirstResult();	
		} catch(Bf_NoMatchingEntityException $e) {
			// rethrow with better message
			$responseClass = $entityClass::getClassName();
			throw new Bf_NoMatchingEntityException("No results returned in API response. Expected at least 1 '$responseClass' entity.");
		}

		$constructedEntity = Bf_BillingEntity::constructEntityFromArgs($entityClass, $firstMatch, $client);
		return $constructedEntity;
	}

	protected static function getResponseRaw($endpoint, $options = NULL, $customClient = NULL, $responseEntity = NULL) {
		$client = is_null($customClient) ? static::getSingletonClient() : $customClient;
		$entityClass = is_null($responseEntity) ? static::getClassName() : $responseEntity;

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$fullRoute = $apiRoute.$endpoint;
		
		$response = $client->doGet($fullRoute, $options);
		return $response;
	}

	protected static function getCollection($endpoint, $options = NULL, $customClient = NULL, $responseEntity = NULL) {
		$client = is_null($customClient) ? static::getSingletonClient() : $customClient;

		$response = static::getResponseRaw($endpoint, $options, $client);

		$entities = static::responseToEntityCollection($response, $client, $responseEntity);
		return $entities;
	}

	protected static function getFirst($endpoint, $options = NULL, $customClient = NULL, $responseEntity = NULL) {
		$client = is_null($customClient) ? static::getSingletonClient() : $customClient;
		
		$response = static::getResponseRaw($endpoint, $options, $client);

		$constructedEntity = static::responseToFirstEntity($response, $client, $responseEntity);
		return $constructedEntity;
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

    /**
     * Returns date formatted as BillForward's UTC ISO8601 string.
     * @param int The timestamp (for example generated by time())
     * @return string The BillForward-formatted time. (Example: '2015-04-23T17:13:37Z')
     */
    public static function makeBillForwardDate($time) {
    	// convert to UTC ISO8601 date
		$isoFormatted = gmdate(DATE_ISO8601, $time);
		// replace "+0000 with Z"
		$formattedTimezone = substr($isoFormatted, 0, strlen($isoFormatted)-5).'Z';

		return $formattedTimezone;
    }

    /**
     * Returns PHP time integer from BillForward's UTC ISO8601 string.
     * @param string The BillForward-formatted time (Example: '2015-04-23T17:13:37Z')
     * @return int The timestamp
     */
    public static function makeUTCTimeFromBillForwardDate($date) {
    	$dateTime = new DateTime($date, new DateTimeZone('UTC'));
    	return $dateTime->getTimestamp();
    }

    /**
     * Fetches (if necessary) entity by ID from API.
     * Otherwise returns entity as-is.
     * @param union[string $id | static $entity] Reference to the entity. <string>: ID by which the entity can be gotten. <static>: The gotten entity.
     * @return static The gotten entity.
     */
    public static function fetchIfNecessary($entityReference) {
    	if (is_string($entityReference)) {
    		// fetch entity by ID
    		return static::getByID($entityReference);
    	}
    	if (is_subclass_of($entityReference, get_called_class())) {
    		return $entityReference;
    	}
    	throw new Bf_MalformedEntityReferenceException('Cannot fetch entity; referenced entity is neither an ID, nor an object extending the desired entity class.');
    }

    /**
     * Unifies type of 'entity' (which owns an identifier) and 'string' identifiers; enables consumer to distill from the reference a string identifier.
     * @param union[string ($id | $name) | static $entity] Reference to the entity. <string>: $id or $name of the entity. <static>: An $entity object from which $entity->id can be ascertained.
     * @return string ID by which the referenced entity can be gotten.
     */
    public static function getIdentifier($entityReference) {
    	if (is_null($entityReference)) {
    		throw new Bf_EntityLacksIdentifierException('Cannot distill identifier from referenced entity; Expected: <ID, or object extending desired entity class> Received: <NULL>.');
    	}
    	if (is_array($entityReference)) {
    		throw new Bf_EntityLacksIdentifierException('Cannot distill identifier from referenced entity; Expected: <ID, or object extending desired entity class> Received: <array>.');
    	}
    	if (is_string($entityReference)) {
    		// already an identifier; return verbatim
    		return $entityReference;
    	}
    	if ($entityReference->getClassName() === static::getClassName()) {
    		// pluck identifier out of entity object
    		if ($entityReference->id)
    		return $entityReference->id;
    		throw new Bf_EntityLacksIdentifierException('Cannot distill identifier from referenced entity; referenced entity does not declare an ID. Perhaps this entity is not a persisted entity retrieved from the API?');
    	}
    	throw new Bf_EntityLacksIdentifierException('Cannot distill identifier from referenced entity; referenced entity is neither an ID, nor an object extending the desired entity class.');
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
