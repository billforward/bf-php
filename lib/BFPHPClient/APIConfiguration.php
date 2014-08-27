<?php

class Bf_APIConfiguration extends Bf_MutableEntity {
	public function create() {
		trigger_error('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it. '
		 .'The entity can be created through cascade only (i.e. instantiated within another entity).',
		 E_USER_ERROR);
	}
}