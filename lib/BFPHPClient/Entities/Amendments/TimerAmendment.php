<?php

class Bf_TimerAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('TimerAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}