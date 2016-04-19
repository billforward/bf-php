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

    /**
    * Constructs a 'sham' instance of an entity -- i.e. a model that only has an ID defined.
    * This is useful if you want to invoke the member methods of its instance, and know its ID,
    * but do not wish for the round-trip of grabbing the full entity by getByID().
    */
    public static function shamWithID($id, $client = NULL) {
    	if (is_null($id)) {
    		throw new Bf_EmptyArgumentException("Received NULL id.");
    	}
    	$stateParams = array(
    		'id' => $id
    		);
    	return new static($stateParams, $client);
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

	protected static function constructEntityFromArgs($entityClass, $constructArgs, $client) {
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

	public static function getByID(
		$id,
		$queryParams = array(),
		$customClient = NULL
		) {
		$identifier = static::getIdentifier($id);

		$endpoint = sprintf("%s",
			rawurlencode($id)
			);

		try {
			return static::getFirst(
				$endpoint,
				$queryParams,
				$customClient
				);
		} catch(Bf_NoMatchingEntityException $e) {
			// rethrow with better message
			$callingClass = static::getClassName();
			$responseClass = $callingClass;
			throw new Bf_NoMatchingEntityException("No results returned by API for '$callingClass::getByID('$id')' (GET to '$endpoint'). Expected at least 1 '$responseClass' entity.");
		}
	}

	protected static function prefixPathWithController($path) {
		$controller = static::getResourcePath()->getPath();
		$qualified = sprintf("%s/%s",
			$controller,
			$path
			);
		return $qualified;
	}

	public static function getAll($queryParams = array(), $customClient = NULL) {
		return static::getCollection(
			'',
			$queryParams,
			$customClient
			);
	}

	protected static function postEntityAndGrabFirst(
		$endpoint,
		$entity,
		$responseEntity = NULL
		) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		return static::postAndGrabFirst(
			$endpoint,
			$serial,
			$client,
			$responseEntity
			);
	}

	protected static function postEntityAndGrabCollection(
		$endpoint,
		$entity,
		$responseEntity = NULL
		) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		return static::postAndGrabCollection(
			$endpoint,
			$serial,
			$client,
			$responseEntity
			);
	}

	protected static function postAndGrabFirst(
		$endpoint,
		$payload,
		$customClient = NULL,
		$responseEntity = NULL
		) {
		$client = is_null($customClient)
		? static::getSingletonClient()
		: $customClient;
		
		$url = static::prefixPathWithController($endpoint);
		$response = $client->doPost($url, $payload);

		$constructedEntity = static::responseToFirstEntity(
			$response,
			$client,
			$responseEntity
			);
		return $constructedEntity;
	}

	protected static function postAndGrabCollection(
		$endpoint,
		$payload,
		$customClient = NULL,
		$responseEntity = NULL
		) {
		$client = is_null($customClient)
		? static::getSingletonClient()
		: $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doPost($url, $payload);

		$constructedEntities = static::responseToEntityCollection(
			$response,
			$client,
			$responseEntity
			);
		return $constructedEntities;
	}

	protected static function putEntityAndGrabFirst(
		$endpoint,
		$entity,
		$responseEntity = NULL
		) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		return static::putAndGrabFirst(
			$endpoint,
			$serial,
			$client,
			$responseEntity
			);
	}

	protected static function putAndGrabFirst(
		$endpoint,
		$payload,
		$queryParams = array(),
		$customClient = NULL,
		$responseEntity = NULL
		) {
		$client = is_null($customClient)
		? static::getSingletonClient()
		: $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doPut(
			$url,
			$payload,
			$queryParams
			);

		$updatedEntity = static::responseToFirstEntity(
			$response,
			$client,
			$responseEntity
			);
		return $updatedEntity;
	}

	protected static function retireAndGrabFirst(
		$endpoint,
		$payload,
		$queryParams = array(),
		$customClient = NULL,
		$responseEntity = NULL
		) {
		$client = is_null($customClient)
		? static::getSingletonClient()
		: $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doRetire($url, $payload);

		$retiredEntity = static::responseToFirstEntity(
			$response,
			$client,
			$responseEntity
			);
		return $retiredEntity;
	}

	protected static function retireAndGrabCollection(
		$endpoint,
		$payload,
		$queryParams = array(),
		$customClient = NULL,
		$responseEntity = NULL
		) {
		$client = is_null($customClient)
		? static::getSingletonClient()
		: $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doRetire($url, $payload);

		$retiredEntities = static::responseToEntityCollection(
			$response,
			$client,
			$responseEntity
			);
		return $retiredEntities;
	}

	protected static function responseToEntityCollection(Bf_RawAPIOutput $response, $client, $responseEntity = NULL) {
		$entityClass = is_null($responseEntity)
		? static::getClassName()
		: $responseEntity;

		$results = $response->getResults();
		$entities = array();

		foreach($results as $value) {
			$constructedEntity = Bf_BillingEntity::constructEntityFromArgs(
				$entityClass,
				$value,
				$client
				);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}

	protected static function responseToFirstEntity(Bf_RawAPIOutput $response, $client, $responseEntity = NULL) {
		$entityClass = is_null($responseEntity)
		? static::getClassName()
		: $responseEntity;

		try {
			$firstMatch = $response->getFirstResult();	
		} catch(Bf_NoMatchingEntityException $e) {
			// rethrow with better message
			$responseClass = $entityClass::getClassName();
			throw new Bf_NoMatchingEntityException("No results returned in API response. Expected at least 1 '$responseClass' entity.");
		}

		$constructedEntity = Bf_BillingEntity::constructEntityFromArgs(
			$entityClass,
			$firstMatch,
			$client
			);
		return $constructedEntity;
	}

	protected static function getCollection(
		$endpoint,
		$queryParams = array(),
		$customClient = NULL,
		$responseEntity = NULL
		) {
		$client = is_null($customClient)
		? static::getSingletonClient()
		: $customClient;

		$url = static::prefixPathWithController($endpoint);
		$response = $client->doGet($url, $queryParams);

		$entities = static::responseToEntityCollection(
			$response,
			$client,
			$responseEntity
			);
		return $entities;
	}

	protected static function getFirst(
		$endpoint,
		$queryParams = array(),
		$customClient = NULL,
		$responseEntity = NULL
		) {
		$client = is_null($customClient)
		? static::getSingletonClient()
		: $customClient;
		
		$url = static::prefixPathWithController($endpoint);
		$response = $client->doGet($url, $queryParams);

		$constructedEntity = static::responseToFirstEntity($response, $client, $responseEntity);
		return $constructedEntity;
	}

	protected static function getAllThenGrabAllWithProperties(
		array $properties,
		$queryParams = array(),
		$customClient = NULL
		) {
		$entities = self::getAll($queryParams, $customClient);
		return self::fromCollectionFindAllWhoMatchProperties(
			$entities,
			$properties
			);
	}

	protected static function getAllThenGrabFirstWithProperties(array $properties,
		$queryParams = array(),
		$customClient = NULL
		) {
		$entities = self::getAll($queryParams, $customClient);
		return self::fromCollectionFindFirstWhoMatchesProperties(
			$entities,
			$properties
			);
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

    protected static function getFinalArgDefault($method) {
    	$reflectionMethod = new ReflectionMethod($method);
    	$methodParams = $reflectionMethod->getParameters();
    	$methodParamsCount = $reflectionMethod->getNumberOfParameters();
    	$finalParamIndex = $methodParamsCount - 1;
    	$finalParam = $methodParams[$finalParamIndex];
    	$finalParamDefaultValue = $finalParam->getDefaultValue();
    	return $finalParamDefaultValue;
    }

    /**
     * Returns whether entity is a member of this class.
     * @param mixed $entity Possible entity.
     * @param mixed $class Name of class.
     * @return boolean Whether the entity is a member of this class.
     */
    protected static function isEntityOfGivenClass($entityReference, $class) {
    	return is_a($entityReference, $class);
    }

    /**
     * Returns whether entity is a member of this class.
     * @param mixed $entity Possible entity.
     * @return boolean Whether the entity is a member of this class.
     */
    protected static function isEntityOfThisClass($entityReference) {
    	return static::isEntityOfGivenClass($entityReference, static::getClassName());
    }

    /**
     * Fetches (if necessary) entity by ID from API.
     * Otherwise returns entity as-is.
     * @param union[string $id | static $entity] Reference to the entity. <string>: ID by which the entity can be gotten. <static>: The gotten entity.
     * @return static The gotten entity.
     */
    protected static function fetchIfNecessary($entityReference) {
    	if (is_string($entityReference)) {
    		// fetch entity by ID
    		return static::getByID($entityReference);
    	}
    	if (static::isEntityOfThisClass($entityReference)) {
    		return $entityReference;
    	}
    	throw new Bf_MalformedEntityReferenceException('Cannot fetch entity; referenced entity is neither an ID, nor an object extending the desired entity class.');
    }

    protected static function mergeUserArgsOverNonNullDefaults($defaultMethod, array $composedArgs, array $userInput) {
    	return array_merge(
    		array_filter(
    			array_merge(static::getFinalArgDefault($defaultMethod), $composedArgs),
    			function($value) {
					return !is_null($value);
				}),
			$userInput
			);
    }

    /**
     * Unifies type of 'entity' (which owns an identifier) and 'string' identifiers; enables consumer to distill from the reference a string identifier.
     * @param union[string ($id | $name) | static $entity] Reference to the entity. <string>: $id or $name of the entity. <static>: An $entity object from which $entity->id can be ascertained.
     * @return string ID by which the referenced entity can be gotten.
     */
    protected static function getIdentifier($entityReference) {
    	if (is_null($entityReference)) {
    		throw new Bf_EntityLacksIdentifierException('Cannot distill identifier from referenced entity; Expected: <ID, or object extending desired entity class> Received: <NULL>.');
    	}
    	if (is_array($entityReference)) {
    		throw new Bf_EntityLacksIdentifierException('Cannot distill identifier from referenced entity; Expected: <ID, or object extending desired entity class> Received: <array>.');
    	}
    	if (is_string($entityReference)) {
    		// already an identifier

    		if ($entityReference === '') {
    			throw new Bf_EmptyArgumentException("Cannot distill identifier from empty string. Expected: <non-empty ID string, or object extending desired entity class> Received: <''>");
    		}
    		// return verbatim
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

    protected static function renameKey(array &$associative, $nominalKey, $newName) {
    	if (!array_key_exists($nominalKey, $associative)) {
    		return NULL;
    	}
    	// only overwrite if no param by that name is presently present.
    	if (!array_key_exists($newName, $associative)) {
	    	$associative[$newName] = $associative[$nominalKey];
	    }
    	return static::popKey($associative, $nominalKey);
    }

    protected static function popKey(array &$associative, $nominalKey) {
    	if (!array_key_exists($nominalKey, $associative)) {
    		return NULL;
    	}
    	$value = $associative[$nominalKey];
    	unset($associative[$nominalKey]);
    	return $value;
    }

    /**
	 * Mutates any key in the referenced array, by applying it to some static lambda
	 * @param array $stateParams Map possibly containing time key that desires parsing.
	 * @param string $key Key of the pertinent time field
	 * @param string $class Name of the class to which the static call will be forwarded
	 * @param string $lambda Name of the static function on this class that will return the parsed time
	 * @param array $lambdaParams Params to be added into the lambda call.
	 * @return static The modified array.
	 */
	protected static function mutateKeyByStaticLambda(
		array &$stateParams,
		$key,
		$lambda,
		array $lambdaParams = array()
		) {
		$originalValue = static::popKey($stateParams, $key);
		if (is_null($originalValue)) {
			// means the user wanted to not send this kvp; let's not parse it.
			return $stateParams;
		}

		$parsedTime = forward_static_call_array(
			is_array($lambda)
			? $lambda
			: array(get_called_class(), $lambda),
			array_merge(
				(array)$originalValue,
				$lambdaParams
				)
			);

		if (!is_null($parsedTime)) {
			$stateParams[$key] = $parsedTime;
		}
		return $stateParams;
	}

	/**
	 * Mutates keys in the referenced array
	 * @param array $stateParams Map possibly containing time key that desires parsing.
	 * @param array (string $key => union[string | callable])  $keyLambdaMap Map of $stateParams keys to the parseTime lambda which will be used to 591 them
	 * @param array $lambdaParams Params to be added into the lambda call.
	 * @return static The modified array.
	 */
	protected function mutateKeysByStaticLambdas(
		array &$stateParams,
		array $keyLambdaMap,
		array $keyLambdaParams = array()
		) {
		$mutator = array(get_called_class(), 'mutateKeyByStaticLambda');
		array_map(function($key, $lambda) use(&$stateParams, $mutator, $keyLambdaParams) {
			$params = array_key_exists($key, $keyLambdaParams)
				? $keyLambdaParams[$key]
				: array();

			call_user_func_array($mutator,
				array(
					&$stateParams,
					$key,
					$lambda,
					$params
					)
				);
		},
		array_keys($keyLambdaMap),
		$keyLambdaMap);
	}

	/**
	 * Parses into a BillForward timestamp the actioning time for some amendment
	 * @param {@see Bf_Amendment::parseActioningTime(mixed)} $actioningTime When to action the amendment
	 * @param union[NULL | union[string $id | Bf_Subscription $entity]] (Default: NULL) (Optional unless 'AtPeriodEnd' actioningTime specified) Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return string The BillForward-formatted time.
	 */
	protected static function parseActioningTime($actioningTime, $subscription = NULL) {
		return Bf_Amendment::parseActioningTime($actioningTime, $subscription);
	}

	/**
	 * Calls the GET method multiple times with increasing paging offsets, until it has gotten all entities
	 * @param string $lambda The name of the GET method to invoke
	 *
	 *  Example:
	 *   $subscription->callMethodAndPageThrough('getCharges');
	 *
	 * @param array $lambdaParams (Default: array()) A list of params with which to invoke the lambda
	 *
	 *  Example:
	 *   $invoice->callMethodAndPageThrough('getCharges' array());
	 *
	 * @param (callable returns boolean) $filter (Default: NULL) Return only entities for whom the filter callback returns true
	 *
	 *  Example:
	 *   $invoice->callMethodAndPageThrough('getCharges' array(), function($invoice) {
	 *    	   return $charge->state === 'Unpaid';
	 *   });
	 *
	 * @param boolean $breakOnFirst (Default: false) (Requires that $filter be specified)
	 *
	 *  Example:
	 *   $invoice->callMethodAndPageThrough('getCharges' array(), function($invoice) {
	 *    	   return $charge->state === 'Unpaid';
	 *   }, true);
	 *
	 * @param int $initialPageSize (Default: 10) How many records to return in each page. This window scales by 50% with each request.
	 *
	 * @return mixed Returns all entities meeting the criteria (or just the first, if $breakOnFirst is specified)
	 */
	public function callMethodAndPageThrough(
		$lambda,
		array $lambdaParams = array(),
		$filter = NULL,
		$breakOnFirst = false,
		$initialPageSize = 20,
		$recordLimit = 1000
		) {
		$reflectionMethod = new ReflectionMethod($this, $lambda);
		return forward_static_call_array(
			array(get_called_class(), 'callFunctionAbstractAndPageThrough'),
			array_merge(
				array($this),
				array_replace(func_get_args(), array($reflectionMethod))
				)
			);
	}

	/**
	 * Calls the GET method multiple times with increasing paging offsets, until it has gotten all entities
	 * @param callable $lambda The GET method to invoke
	 *
	 *  Example:
	 *   Bf_Subscription::callFunctionAndPageThrough('getAll');
	 *
	 * @param array $lambdaParams (Default: array()) A list of params with which to invoke the lambda
	 *
	 *  Example:
	 *   Bf_Subscription::callFunctionAndPageThrough('getByProductID' array('PRO-65F14D63-D027-4E2F-9DC0-4FFEFBCB'));
	 *
	 * @param (callable returns boolean) $filter (Default: NULL) Return only entities for whom the filter callback returns true
	 *
	 *  Example:
	 *   Bf_Amendment::callFunctionAndPageThrough('getForSubscription' array($subscription), function($amendment) {
	 *    	   return $amendment->amendmentType === 'Cancellation';
	 *   });
	 *
	 * @param boolean $breakOnFirst (Default: false) (Requires that $filter be specified)
	 *
	 *  Example:
	 *   Bf_Amendment::callFunctionAndPageThrough('getForSubscription' array($subscription), function($amendment) {
	 *    	   return $amendment->amendmentType === 'ServiceEnd';
	 *   }, true);
	 *
	 * @return mixed Returns all entities meeting the criteria (or just the first, if $breakOnFirst is specified)
	 */
	public static function callFunctionAndPageThrough(
		$lambda,
		array $lambdaParams = array(),
		$filter = NULL,
		$breakOnFirst = false,
		$initialPageSize = 20,
		$recordLimit = 1000
		) {
		$reflectionMethod = new ReflectionMethod(get_called_class(), $lambda);
		return forward_static_call_array(
			array(get_called_class(), 'callFunctionAbstractAndPageThrough'),
			array_merge(
				array(NULL),
				array_replace(func_get_args(), array($reflectionMethod))
				)
			);
	}

	protected static function callFunctionAbstractAndPageThrough(
		$caller,
		ReflectionMethod $extendsReflectionFunctionAbstract,
		array $lambdaParams = array(),
		$filter = NULL,
		$breakOnFirst = false,
		$initialPageSize = 20,
		$recordLimit = 1000
		) {
		$optionsParams = array_filter($extendsReflectionFunctionAbstract->getParameters(),
			function($param) {
				return $param->name === 'options';
			});
		if (count($optionsParams) <= 0) {
			throw new Bf_InvocationException(sprintf("The method '%s' has no 'options' parameter with which we can page through its results", $lambda));
		}

		$paramKeys = array_keys($optionsParams);
		$optionParamPosition = $paramKeys[0];

		if (!array_key_exists($optionParamPosition, $lambdaParams)) {
			$lambdaParams[$optionParamPosition] = array();
		}
		if (!is_array($lambdaParams[$optionParamPosition])) {
			throw new Bf_InvocationException(sprintf("Received in 'options' param slot a non-array value: '%s'", $lambdaParams[$optionParamPosition]));
		}

		$existingOptionsParams = $lambdaParams[$optionParamPosition];

		$pageSize = min($initialPageSize, $recordLimit);

		$recordsRequestedTotal = 0;
		$matchingRecordsEncountered = 0;

		$offset = 0;

		$accumulator = array();
		while(true) {
			$lambdaParams[$optionParamPosition] = array_replace(
				$lambdaParams[$optionParamPosition],
				array(
					'offset' => $offset,
					'records' => $pageSize
					)
				);

			$matchingResults = array();
			$newResults = $extendsReflectionFunctionAbstract->invokeArgs($caller, $lambdaParams);
			$resultsForAccumulator = $newResults;

			// var_export($recordsRequestedTotal);

			if (is_callable($filter)) {
				$matchingResults = array_values(array_filter($newResults, $filter));
				$resultsForAccumulator = $matchingResults;
			}
			$matchingRecordsEncountered += count($resultsForAccumulator);
			$recordsRequestedTotal += $pageSize;

			$accumulator = array_merge($accumulator, $resultsForAccumulator);
			if ($breakOnFirst) {
				if (count($accumulator) > 0) {
					$matchingEntities = array_values($accumulator);
					return $matchingEntities[0];
				}
			}
			if ($recordsRequestedTotal >= $recordLimit) {
				throw new Bf_SearchLimitReachedException(
					sprintf(
						"Failed to exhaust search space within the imposed record limit (%d).\nUltimately fetched '%d' records, and '%d' matched the search criteria.",
						$recordLimit,
						$recordsRequestedTotal,
						$matchingRecordsEncountered
						)
					);
			}
			if (count($newResults) < $pageSize) {
				// no further results expected
				break;
			}
			$offset += $pageSize;
			$pageSize = min(ceil($pageSize*1.5), $recordLimit-$recordsRequestedTotal);
		}

		return $accumulator;
	}

	//// TIME PARSING HELPERS

	/**
	 * Parses into a BillForward timestamp the Bf_TimeRequest 'From' time
	 * @param union[int $timestamp | string_ENUM['Now', 'CurrentPeriodEnd']] (Default: 'Immediate') When to action the amendment
	 *
	 *  int
	 *  'From' the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default)
	 *  'From' the time at which the request reaches the server
	 *
	 *  <ClientNow>
	 *  'From' the current time by this client's clock.
	 *  
	 *  <CurrentPeriodEnd>
	 *  'From' the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the amendment to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 *
	 * @param union[NULL | union[string $id | Bf_Subscription $entity]] (Default: NULL) (Optional unless 'CurrentPeriodEnd' actioningTime specified) Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return string The BillForward-formatted time.
	 */
	public static function parseTimeRequestFromTime($fromTime, $subscription = NULL) {
		$intSpecified = NULL;

		switch ($fromTime) {
			case 'ServerNow':
			case 'Immediate':
				return NULL;
			case 'CurrentPeriodEnd':
				// we need to consult subscription
				if (is_null($subscription)) {
					throw new Bf_EmptyArgumentException('Failed to consult subscription to ascertain CurrentPeriodEnd time, because a null reference was provided to the subscription.');
				}
				$subscriptionFetched = Bf_Subscription::fetchIfNecessary($subscription);
				return $subscriptionFetched->getCurrentPeriodEnd();
			case 'ClientNow':
				$intSpecified = time();
			default:
				if (is_int($fromTime)) {
					$intSpecified = $fromTime;
				}
				if (!is_null($intSpecified)) {
					return Bf_BillingEntity::makeBillForwardDate($intSpecified);
				}
				if (is_string($fromTime)) {
					return $fromTime;
				}
		}

		return NULL;
	}

	/**
	 * Parses into a BillForward timestamp the Bf_TimeRequest 'From' time
	 * @param union[int $timestamp | string_ENUM['Now', 'CurrentPeriodEnd']] (Default: 'Immediate') When to action the amendment
	 *
	 *  int
	 *  'From' the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default)
	 *  'To' the time at which the request reaches the server
	 *
	 *  <ClientNow>
	 *  'To' the current time by this client's clock.
	 *  
	 *  <CurrentPeriodEnd>
	 *  'To' the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the amendment to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 *
	 * @param union[NULL | union[string $id | Bf_Subscription $entity]] (Default: NULL) (Optional unless 'CurrentPeriodEnd' actioningTime specified) Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return string The BillForward-formatted time.
	 */
	public static function parseTimeRequestToTime($fromTime, $subscription = NULL) {
		$intSpecified = NULL;

		switch ($fromTime) {
			case 'ServerNow':
			case 'Immediate':
				return NULL;
			case 'CurrentPeriodEnd':
				// we need to consult subscription
				if (is_null($subscription)) {
					throw new Bf_EmptyArgumentException('Failed to consult subscription to ascertain CurrentPeriodEnd time, because a null reference was provided to the subscription.');
				}
				$subscriptionFetched = Bf_Subscription::fetchIfNecessary($subscription);
				return $subscriptionFetched->getCurrentPeriodEnd();
			case 'ClientNow':
				$intSpecified = time();
			default:
				if (is_int($fromTime)) {
					$intSpecified = $fromTime;
				}
				if (!is_null($intSpecified)) {
					return Bf_BillingEntity::makeBillForwardDate($intSpecified);
				}
				if (is_string($fromTime)) {
					return $fromTime;
				}
		}

		return NULL;
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
