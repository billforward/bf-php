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

    public function json() {
        return json_decode($this->response, true);
    }

    public function rawResponse() {
        return $this->response;
    }

    public function getInfo() {
        return $this->info;
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
            // check for existence of static client instead
            $client = static::$singletonClient;
            if (is_null($client)) {
                throw new Exception('No default BillForwardClient found; cannot make API requests.');
            }
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
                    throw new \Exception($errorString);
                } else {
                    // I think this means you can connect to API, but it is in a bad state.
                    $errorString = sprintf("\n====\nNo message returned by API.\nHTTP code: \t<%d>\nRaw response: \t<%s>\n====", $httpCode, $responseRaw);
                    throw new \Exception($errorString);
                }
            } else {
                if (array_key_exists('errorType', $payload)) {
                    // API up and running, but your request is unsuccessful
                    $errorType = $payload['errorType'];
                    $errorMessage = $payload['errorMessage'];

                    $errorString = sprintf("\n====\nReceived error from API.\nError code: \t<%d>\nError type: \t<%s>\nError message:\t<%s>.\n====", $httpCode, $errorType, $errorMessage);
                    throw new \Exception($errorString);
                }
            }
        //}
    }

	public function doGet($endpoint, $data = null) {
		$urlFull = $this->urlRoot.$endpoint;
		$response = $this->CallAPI_Unvalidated('GET', $urlFull, $data, false);

        static::handleError($response);

		return $response;
	}

	public function doPost($endpoint, array $params) {
		$urlFull = $this->urlRoot.$endpoint;

        $response = $this->CallAPI_Unvalidated('POST', $urlFull, json_encode($params), true);

        static::handleError($response);

		return $response;
	}

	public function doPut($endpoint, array $params) {
		$urlFull = $this->urlRoot.$endpoint;

        $response = $this->CallAPI_Unvalidated('PUT', $urlFull, json_encode($params), true);

        static::handleError($response);

		return $response;
	}

    public function doRetire($endpoint, array $params) {
        $urlFull = $this->urlRoot.$endpoint;

        $response = $this->CallAPI_Unvalidated('RETIRE', $urlFull, json_encode($params), true);

        static::handleError($response);

        return $response;
    }

	    //todo google codeigniter rest
    /**
     * @param $method "GET"/"POST"/...
     * @param $request
     * @param bool|array $data
     * @param bool $json
     * @return Bf_RawAPIOutput
     */
    private function CallAPI_Unvalidated($method, $request, $data = false, $json = false) {
        $curl = curl_init();

        $url = $request;

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
 
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }

                break;
            case "PUT":
                if ($data) {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            case "GET":
                if ($data) {
                    $query = strpos($request, '?') !== FALSE ? '' : '?';
                    if($query == '?') {
                        $url = sprintf("%s?%s", $url, http_build_query($data));
                    } else {
                        $url = sprintf("%s&%s", $url, http_build_query($data));
                    }
                }
                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        //curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:8888');
        $header = array();

        if ($json) {
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: ' . strlen($data);
        }

        $header[] = "Authorization: Bearer " . $this->access_token;

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $res = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);


        return new Bf_RawAPIOutput($info, $res);
    }
}
