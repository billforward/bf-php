<?php
class Bf_SDKException extends \Exception {}

class Bf_InvocationException extends Bf_SDKException {}
class Bf_SetupException extends Bf_InvocationException {}
class Bf_PreconditionFailedException extends Bf_InvocationException {}
class Bf_UnsupportedParameterException extends Bf_InvocationException {}
class Bf_UnsupportedMethodException extends Bf_InvocationException {}
class Bf_EmptyArgumentException extends Bf_InvocationException {}
class Bf_UnserializationException extends Bf_InvocationException {}

class Bf_MalformedInputException extends Bf_InvocationException {}
class Bf_MalformedEntityReferenceException extends Bf_MalformedInputException {}
class Bf_EntityLacksIdentifierException extends Bf_MalformedInputException {}

class Bf_NoMatchingEntityException extends Bf_SDKException {}
class Bf_SearchLimitReachedException extends Bf_SDKException {}

class Bf_APIException extends Bf_SDKException {
	protected $httpCode = NULL;

	public function __construct($exceptionMessage, $httpCode) {
		parent::__construct($exceptionMessage);

		$this->httpCode = $httpCode;
	}

	public function getHttpCode() {
		return $this->httpCode;
	}
}

class Bf_NoAPIResponseException extends Bf_APIException {
	protected $rawResponse = NULL;

	public function __construct($exceptionMessage, $httpCode, $rawResponse) {
		parent::__construct($exceptionMessage, $httpCode);

		$this->rawResponse = $rawResponse;
	}

	public function getRawResponse() {
		return $this->rawResponse;
	}
}
class Bf_APIErrorResponseException extends Bf_APIException {
	protected $rawResponse = NULL;
	protected $parsedResponse = NULL;
	protected $bfErrorType = NULL;
	protected $bfErrorMessage = NULL;
	protected $bfErrorParameters = NULL;

	public function __construct(
		$exceptionMessage,
		$httpCode,
		$rawResponse,
		$parsedResponse,
		$bfErrorType,
		$bfErrorMessage,
		$bfErrorParameters
		) {
		parent::__construct($exceptionMessage, $httpCode);

		$this->rawResponse = $rawResponse;
		$this->parsedResponse = $parsedResponse;
		$this->bfErrorType = $bfErrorType;
		$this->bfErrorMessage = $bfErrorMessage;
		$this->bfErrorParameters = $bfErrorParameters;
	}

	public function getBFErrorType() {
		return $this->bfErrorType;
	}

	public function getBFErrorMessage() {
		return $this->bfErrorMessage;
	}

	public function getBFErrorParameters() {
		return $this->bfErrorParameters;
	}

	public function getRawResponse() {
		return $this->rawResponse;
	}

	public function getParsedResponse() {
		return $this->parsedResponse;
	}
}