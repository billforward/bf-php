<?php

class Bf_Notification extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('notifications', 'notifications');
	}

	/**
	 * Gets Bf_Notifications that occurred during the given time range. Optionally, the search can be constrained (by ID) to a specific webhook.
	 * Time range is at granularity of seconds, and is exclusive of timeStart but inclusive of timeEnd
	 * i.e. 'Second at which notification was created' > $timeStart
	 *   && 'Second at which notification was created' <= $timeEnd
	 * As with all GETs, by default 10 records are returned, so use the `options` field if you want to page through using for example array('records' => 10, `offset`=30)
	 * @param union[int|string] <int>: Unix timestamp (for example generated by time()). <string>: UTC ISO8601 string
	 * @param union[int|string|NULL] (Default: NULL) <int>: Unix timestamp (for example generated by time()). <string>: UTC ISO8601 string. <NULL>: Interpreted as 'Now'.
	 * @param union[string $id | Bf_Webhook $entity | NULL] (Default: NULL) The webhook for which you wish to GET notifications. <string>: ID of the Bf_Webhook. <Bf_Webhook>: The Bf_Webhook. <NULL>: GET notifications of all types.
	 * @return Bf_Notification[]
	 */
	public static function getForTimeRange($timeStart, $timeEnd = NULL, $webhook = NULL, $options = NULL, $customClient = NULL) {
		if (is_null($timeEnd)) {
			$timeEnd = time();
		}

		$webhookID = is_null($webhook) ? NULL : Bf_Webhook::getIdentifier($webhook);

		// empty IDs are no good!
		if (!$timeStart) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty start time!");
		}
		if (!$timeEnd) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty end time!");
		}

		if (is_int($timeStart)) {
			$timeStart = Bf_BillingEntity::makeBillForwardDate($timeStart);
		}
		if (is_int($timeEnd)) {
			$timeEnd = Bf_BillingEntity::makeBillForwardDate($timeEnd);
		}

		// path param expects format like: '2015-04-23T11:05:53'
		$timeStart = rtrim($timeStart, "Z");
		$timeEnd = rtrim($timeEnd, "Z");

		$endpoint = sprintf("%s/%s%s",
			$timeStart,
			$timeEnd,
			is_null($webhookID)
			? "" 
			: sprintf("/%s", rawurlencode($webhookID))
			);

		return static::getCollection($endpoint, $options, $customClient);
	}
}
Bf_Notification::initStatics();