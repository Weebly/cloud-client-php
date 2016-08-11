<?php
/**
 * CloudClient class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud\Utils;

/**
 * CloudClient for accessing the Weebly Cloud API.
 */
class CloudClient
{
	/**
	 * API domain.
	 */
	const BASE_URL = 'https://api.weeblycloud.com/';

	/**
	 * Admin API key.
	 *
	 * @var $api_key
	 */
	public $api_key;

	/**
	 * API secret key.
	 *
	 * @var $api_secret
	 */
	public $api_secret;

	/**
	 * Instance of CloudClient.
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Gets the instance of the CloudClient.
	 *
	 * @return instance
	 */
	public static function getClient() {
		if(!isset(static::$instance)) {
			throw new \Exception('Error: client not instantiated; must set keys before calling.');
		}

		return static::$instance;
	}

	/**
	 * Sets the instance of CloudClient using the API keys. Must
	 * be called before making a request.
	 *
	 * @param string $api_key
	 * @param string $api_secret
	 */
	public function setKeys($api_key, $api_secret){
		static::$instance = new static($api_key, $api_secret);
	}

	/**
	 * Creates a new CloudClient object.
	 *
	 * @param string $api_key
	 * @param string $api_secret
	 * @return instance
	 */
	protected function __construct($api_key, $api_secret) {
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
	}

	/**
	 * Makes a curl request to the Weebly Cloud API.
	 *
	 * @param string $url The endpoint url, not including domain or query string.
	 * @param string $method
	 * @param array $data
	 * @return \Weebly\CloudResponse
	 */
	private function makeRequest($url, $method, $data) {

		$content = in_array($method, ['POST','PUT','PATCH']) ? json_encode($data) : '[]';
		$parameters = in_array($method, ['DELETE','GET']) ? $data : [];
		$hash = hash_hmac('SHA256', $method . "\n" . $url . "\n" . $content, $this->api_secret);
		$hash = base64_encode($hash);

		$ch = curl_init();

		curl_setopt_array(
			$ch,
			[
				CURLOPT_URL => self::BASE_URL . $url . '?' . http_build_query($parameters),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_POSTFIELDS => $content,
				CURLOPT_HTTPHEADER => array(
					'Content-type: application/json',
					'X-Public-Key: ' . $this->api_key,
					'X-Signed-Request-Hash: ' . $hash
				),
				CURLOPT_HEADER => 1,
				CURLOPT_FOLLOWLOCATION => true
			]
		);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);

		curl_close($ch);

		$header_size = $info['header_size'];
		$header = substr($result, 0, $header_size);
		$response = substr($result, $header_size);

		if($info['http_code']!=204 && (!$response || json_decode($response)->error)) {
			$error = json_decode($response)->error;

			if($error) {
				$error_message = $error->message;
			} else {
				$error_message = 'No response';
			}

			throw new CloudException($error_message, $error->code);
		}

		return new CloudResponse($header, $response, $url, $parameters);
	}

	/**
	 * Makes a GET request to the Weebly Cloud API.
	 *
	 * @param string $url The endpoint url, not including domain or query string.
	 * @param array $parameters
	 */
	public function get($url, $parameters = []) {
		return $this->makeRequest($url, 'GET', $parameters);
	}

	/**
	 * Makes a GET request to an endpoint that returns multiple objects.
	 *
	 * @param string $url The endpoint url, not including domain or query string.
	 * @param array $search_params Optional search parameters.
	 * @param int $page_size The maximum number of results per page, if the
	 						endpoint is paginated.
	 */
	public function getList($url, $search_params = [], $page_size = null) {
		if ($page_size) {
			$search_params = array_merge($search_params, ["limit" => $page_size]);
		}
		return $this->get($url, $search_params);
	}

	/**
	 * Makes a DELETE request to the Weebly Cloud API.
	 *
	 * @param string $url The endpoint url, not including domain or query string.
	 * @param array $parameters
	 */
	public function delete($url, $parameters = []) {
		return $this->makeRequest($url, 'DELETE', $parameters);
	}

	/**
	 * Makes a POST request to the Weebly Cloud API.
	 *
	 * @param string $url The endpoint url, not including domain or query string.
	 * @param array $data
	 */
	public function post($url, $data = []) {
		return $this->makeRequest($url, 'POST', $data);
	}

	/**
	 * Makes a PATCH request to the Weebly Cloud API.
	 *
	 * @param string $url The endpoint url, not including domain or query string.
	 * @param array $data
	 */
	public function patch($url, $data = []) {
		return $this->makeRequest($url, 'PATCH', $data);
	}

	/**
	 * Makes a PUT request to the Weebly Cloud API.
	 *
	 * @param string $url The endpoint url, not including domain or query string.
	 * @param array $data
	 */
	public function put($url, $data = []) {
		return $this->makeRequest($url, 'PUT', $data);
	}
}
