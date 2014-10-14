<?php
class Bf_ResourcePath {
	protected $path;
	protected $entityName;

	public function __construct($path, $entityName) {
		$this->path = $path;
		$this->entityName = $entityName;
	}

	/**
	 * Gets base API route for this entity.
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Gets the XmlRootElement of the Entity. We see this in @type field of a response.
	 * @return string
	 */
	public function getEntityName() {
		return $this->entityName;
	}
}
