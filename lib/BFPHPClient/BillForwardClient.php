<?php
/**
 * Class Bf_RawAPIOutput
 */
class Bf_RawAPIOutput {
    /**
     * @param mixed $info
     * @param RestAPIResponse $response
     */
    public function __construct($info, $response) {
        $this->info = $info;
        $this->response = $response;
    }

    private function asUTF8($str) {
        return utf8_encode($str);
    }

    public function json() {
        return json_decode($this->rawResponse(), true);
    }

    public function rawResponse() {
        return $this->asUTF8($this->response);
    }

    public function getInfo() {
        return $this->info;
    }

    public function getResults() {
        $json = $this->json();
        $results = $json['results'];
        return $results;
    }

    public function getFirstResult() {
        $results = $this->getResults();

        if (count($results) <= 0) {
            throw new Bf_NoMatchingEntityException('No results returned - therefore cannot lookup first member.');
        }

        $firstResult = $results[0];
        return $firstResult;
    }
}

class BillForwardClient {
	private $access_token = NULL;
	private $urlRoot = NULL;

    private static $singletonClient = NULL;

	public function __construct($access_token, $urlRoot) {
		$this->access_token = $access_token;
		$this->urlRoot = $urlRoot;
	}

    public static function getDefaultClient() {
        $client = static::$singletonClient;
        if (is_null($client)) {
            throw new Bf_SetupException('No default BillForwardClient found; cannot make API requests.');
        }
        return $client;
    }

    /**
     * Sets the specified client to be used as the default client.
     * @param BillForwardClient &$client The client to designate as default client
     * @return BillForwardClient The new default client
     */
    public static function setDefaultClient(BillForwardClient &$client = NULL) {
        static::$singletonClient = $client;
        return static::$singletonClient;
    }

    /**
     * Constructs a client, and sets it to be used as the default client.
     * @param string $access_token Access token to connect to BillForward API
     * @param string $urlRoot URL of BillForward API version you wish to connect to
     * @return BillForwardClient The new default client
     */
    public static function makeDefaultClient($access_token, $urlRoot) {
        $client = new BillForwardClient($access_token, $urlRoot);
        static::setDefaultClient($client);
        return static::$singletonClient;
    }

    protected static function handleError($response) {
        $info = $response
        ->getInfo();
        $payload = $response
        ->json();
        $responseRaw = $response
        ->rawResponse();

        $httpCode = $info['http_code'];

        //var_export($responseRaw);

        //if ($info['http_code'] != 200) {
            if (is_null($payload)) {
                if (is_null($responseRaw)) {
                    // I think this means you cannot connect to API.
                    $errorString = sprintf("\n====\nNo message returned by API.\nHTTP code: \t<%d>\n====", $httpCode);
                    throw new Bf_NoAPIResponseException($errorString);
                } else {
                    // I think this means you can connect to API, but it is in a bad state.
                    $errorString = sprintf("\n====\nNo message returned by API.\nHTTP code: \t<%d>\nRaw response: \t<%s>\n====", $httpCode, $responseRaw);
                    throw new Bf_NoAPIResponseException($errorString);
                }
            } else {
                if (array_key_exists('errorType', $payload)) {
                    // API up and running, but your request is unsuccessful
                    $errorType = $payload['errorType'];
                    $errorMessage = $payload['errorMessage'];

                    $errorString = sprintf("\n====\nReceived error from API.\nError code: \t<%d>\nError type: \t<%s>\nError message:\t<%s>.\n====", $httpCode, $errorType, $errorMessage);
                    throw new Bf_APIErrorResponseException($errorString);
                }
            }
        //}
    }

	public function doGet($endpoint, array $queryParams = array()) {
		$urlFull = $this->urlRoot.$endpoint;
		$response = $this->doCurl('GET', $urlFull, null, $queryParams);

        static::handleError($response);

		return $response;
	}

	public function doPost($endpoint, array $payload, array $queryParams = array()) {
		$urlFull = $this->urlRoot.$endpoint;

        $response = $this->doCurl('POST', $urlFull, json_encode($payload), $queryParams);

        static::handleError($response);

		return $response;
	}

	public function doPut($endpoint, array $payload, array $queryParams = array()) {
		$urlFull = $this->urlRoot.$endpoint;

        $response = $this->doCurl('PUT', $urlFull, json_encode($payload), $queryParams);

        static::handleError($response);

		return $response;
	}

    public function doRetire($endpoint, array $payload = null, array $queryParams = array()) {
        $urlFull = $this->urlRoot.$endpoint;
        $data = is_null($payload)
        ? null
        : json_encode($payload);

        $response = $this->doCurl('DELETE', $urlFull, $data, $queryParams);

        static::handleError($response);

        return $response;
    }

    /**
     * @param $verb "GET"/"POST"/...
     * @param $url
     * @param bool|array $data
     * @param bool $json
     * @return Bf_RawAPIOutput
     */
    private function doCurl($verb, $url, $payloadStr, array $queryParams = array()) {
        $curl = curl_init();
        $header = array();

        $hasPayload = !is_null($payloadStr) && is_string($payloadStr);
        $hasQueryParams = !is_null($queryParams) && is_array($queryParams) && count($queryParams);

        switch ($verb) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }

        if ($hasPayload) {
            array_push($header, "Authorization: Bearer " . $this->access_token);
        } else {
            // if we put auth in the query param: we can avoid an OPTIONS preflight
            if (!$hasQueryParams) {
                $queryParams = array();
            }
            $queryParams['access_token'] = $this->access_token;
            $hasQueryParams = true;
        }

        if ($hasQueryParams) {
            foreach($queryParams as $key => $value) {
                if(is_bool($value)) {
                    $queryParams[$key] = $value ? 'true' : 'false';
                }
            }

            $url = sprintf(
                "%s%s%s",
                $url,
                strpos($url, '?')
                ? '&'
                : '?',
                http_build_query($queryParams)
                );
        }

        // curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:4651');

        if ($hasPayload) {
            // has JSON payload
            array_push($header, 'Content-Type: application/json');
            array_push($header, 'Content-Length: ' . strlen($payloadStr));

            curl_setopt($curl, CURLOPT_POSTFIELDS, $payloadStr);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        return new Bf_RawAPIOutput($info, $response);
    }
}
