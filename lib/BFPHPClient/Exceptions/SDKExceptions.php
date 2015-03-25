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

class Bf_APIException extends Bf_SDKException {}
class Bf_NoAPIResponseException extends Bf_APIException {}
class Bf_APIErrorResponseException extends Bf_APIException {}