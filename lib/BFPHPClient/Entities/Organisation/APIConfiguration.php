<?php

class Bf_APIConfiguration extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		throw new Bf_UnsupportedMethodException('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it. '
		 .'The entity can be created through cascade only (i.e. instantiated within another entity).');
	}
}