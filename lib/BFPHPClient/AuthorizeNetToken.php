<?php

class Bf_AuthorizeNetToken extends Bf_MutableEntity {
	protected static $_resourcePath;
	
	public static function initStatics() {
		/* WARNING: resource paths for Bf_AuthorizeNetTokens do not follow the usual pattern;
		 * instead of posting to 'root' of a URL root reserved for Bf_AuthorizeNetTokens, 
		 * this routing is a bit less standard; for example we can't GET from the same
		 * place we POST to.
		 */
		self::$_resourcePath = new Bf_ResourcePath('vaulted-gateways/authorize-net', 'authorizeNetToken');
	}
}
Bf_AuthorizeNetToken::initStatics();
