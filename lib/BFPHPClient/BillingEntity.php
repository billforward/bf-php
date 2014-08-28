<?php
abstract class Bf_BillingEntity extends \ArrayObject {
	public function getClient() {
		return $this->_client;
	}

	public function setClient(BfClient &$client = NULL) {
		$this->_client = $client;
	}

	protected $_client = NULL;

	protected $_registeredEntities = [];
	protected $_registeredEntityArrays = [];

	public function __construct(BfClient &$client = NULL, array $stateParams = NULL) {
		if ($stateParams == NULL) {
			$stateParams = [];
		}

		$this->setClient($client);

		$this->doUnserialize($stateParams);
	}

	public static function getClassName() {
		// late static bindings, PHP version 5.3+ only
		return get_called_class();
	}

	public static function getResourcePath() {
		return static::$_resourcePath;
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
		$entityArray = [];
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
			$newEntity = new $class($client, $constructArgs);
		}
		return $newEntity;
	}

	public function getSerialized() {
		$outputArray = [];
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
			$tempArray = [];
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

    public function __get($name) {
    	return $this->offsetGet($name);
    }

    public function __set($name, $value) {
    	return $this->offsetSet($name, $value);
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
