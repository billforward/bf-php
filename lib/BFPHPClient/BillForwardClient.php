<?php
/**
 * Class Bf_RawAPIOutput
 */
class Bf_RawAPIOutput {
    /**
     * @param mixed $info
     * @param RestAPIResponse $response
     */
    public function __construct($info, $response, $error) {
        $this->info = $info;
        $this->response = $response;
        $this->error = $error;
    }

    /**
    * Currently used to "get the entire payload returned by the API", as PHP arrays
    */
    public function payloadArray() {
        return Bf_Util::jsonStrToAssociativeArray($this->payloadStr());
    }

    /**
    * Currently used to "get the entire payload returned by the API", as a string
    */
    public function payloadStr() {
        return $this->response;
    }

    public function getInfo() {
        return $this->info;
    }

    public function getCurlError() {
        return $this->error;
    }

    public function getResults() {
        $json = $this->payloadArray();
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
	private $curlProxy = NULL;

    private static $singletonClient = NULL;

	public function __construct($access_token, $urlRoot, $curlProxy = NULL) {
		$this->access_token = $access_token;
		$this->urlRoot = $urlRoot;
        $this->curlProxy = $curlProxy;
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
     * @param null|string $curlProxy (Optional) URL of a local proxy (such as Fiddler or Proxy.app) through which you wish to forward your request. Example for Proxy.app: '127.0.0.1:4651'
     * @return BillForwardClient The new default client
     */
    public static function makeDefaultClient($access_token, $urlRoot, $curlProxy = NULL) {
        $client = new BillForwardClient($access_token, $urlRoot, $curlProxy);
        static::setDefaultClient($client);
        return static::$singletonClient;
    }

    protected static function handleError($response) {
        $info = $response
        ->getInfo();
        $payloadArray = $response
        ->payloadArray();
        $payloadStr = $response
        ->payloadStr();

        $httpCode = $info['http_code'];

        //var_export($payloadStr);

        //if ($info['http_code'] != 200) {
            if (is_null($payloadArray)) {
                if (is_null($payloadStr) || $payloadStr === false) {
                    // I think this means you cannot connect to API.
                    $errorString = sprintf(
                        "\n====\nNo message returned by API.\nHTTP code: \t<%d>\n%s====",
                        $httpCode,
                        $response->getCurlError()
                        ? sprintf(
                            "cURL error: \t<%s>\n",
                            $response->getCurlError()
                            )
                        : ''
                        );
                    throw new Bf_NoAPIResponseException($errorString, $httpCode, NULL);
                } else {
                    // I think this means you can connect to API, but it is in a bad state.
                    $errorString = sprintf(
                        "\n====\nNo message returned by API.\nHTTP code: \t<%d>\nRaw response: \t<%s>\n%s====",
                        $httpCode,
                        $payloadStr,
                        $response->getCurlError()
                        ? sprintf(
                            "cURL error: \t<%s>\n",
                            $response->getCurlError()
                            )
                        : ''
                        );
                    throw new Bf_NoAPIResponseException(
                        $errorString,
                        $httpCode,
                        $payloadStr
                        );
                }
            } else {
                if (array_key_exists('errorType', $payloadArray)) {
                    // API up and running, but your request is unsuccessful
                    $errorType = $payloadArray['errorType'];
                    $errorMessage = $payloadArray['errorMessage'];

                    $errorParameters = NULL;
                    if (array_key_exists('errorParameters', $payloadArray)) {
                        $errorParameters = $payloadArray['errorParameters'];
                        // var_export($payloadArray['errorParameters']);
                    }

                    $errorString = sprintf("\n====\nReceived error from API.\nError code: \t<%d>\nError type: \t<%s>\nError message:\t<%s>.\n====", $httpCode, $errorType, $errorMessage);
                    throw new Bf_APIErrorResponseException(
                        $errorString,
                        $httpCode,
                        $payloadStr,
                        $payloadArray,
                        $errorType,
                        $errorMessage,
                        $errorParameters
                        );
                }
            }
        //}
    }

	public function doGet(
        $endpoint,
        $queryParams = array()
        ) {
		$urlFull = $this->urlRoot.$endpoint;

		$response = $this->doCurl(
            'GET',
            $urlFull,
            null,
            $queryParams
            );

        static::handleError($response);

		return $response;
	}

	public function doPost(
        $endpoint,
        $payload,
        $queryParams = array()
        ) {
		$urlFull = $this->urlRoot.$endpoint;

        $response = $this->doCurl(
            'POST',
            $urlFull,
            $payload,
            $queryParams
            );

        static::handleError($response);

		return $response;
	}

	public function doPut(
        $endpoint,
        $payload,
        $queryParams = array()
        ) {
		$urlFull = $this->urlRoot.$endpoint;

        $response = $this->doCurl(
            'PUT',
            $urlFull,
            $payload,
            $queryParams
            );

        static::handleError($response);

		return $response;
	}

    public function doRetire(
        $endpoint,
        $payload = null,
        $queryParams = array()
        ) {
        $urlFull = $this->urlRoot.$endpoint;

        $response = $this->doCurl(
            'DELETE',
            $urlFull,
            $payload,
            $queryParams
            );

        static::handleError($response);

        return $response;
    }

    /**
     * @param $verb "GET"/"POST"/...
     * @param $url
     * @param array|null $payload
     * @param bool $json
     * @return Bf_RawAPIOutput
     */
    private function doCurl(
        $verb,
        $url,
        $payload,
        $queryParams = array()
        ) {
        $curl = curl_init();
        $header = array();

        $payloadStr = is_null($payload)
        ? null
        : Bf_Util::associativeArrayToJsonStr($payload);

        $hasPayload = !is_null($payloadStr) && is_string($payloadStr);
        $hasQueryParams = !is_null($queryParams) && is_array($queryParams) && count($queryParams);

        switch ($verb) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
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
                strpos($url, '?') ? '&' : '?',
                http_build_query($queryParams)
                );
        }

        if (!is_null($this->curlProxy)) {
            curl_setopt($curl, CURLOPT_PROXY, $this->curlProxy);
        }

        if ($hasPayload) {
            // has JSON payload
            array_push($header, 'Content-Type: application/json; charset=UTF-8');
            /*
            * `strlen` is fine even for UTF-8, because it counts bytes rather than characters. And bytes is indeed what curl wants.
            * For posterity: here are alternatives if we actually want to count characters:
            * mb_strlen($payloadStr, 'utf8')
            * mb_strlen($payloadStr)
            */
            array_push($header, 'Content-Length: ' . strlen($payloadStr));

            curl_setopt($curl, CURLOPT_POSTFIELDS, $payloadStr);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_ENCODING, '');

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $error = $info === false
        ? curl_error($curl)
        : NULL;

        curl_close($curl);

        return new Bf_RawAPIOutput($info, $response, $error);
    }
}
