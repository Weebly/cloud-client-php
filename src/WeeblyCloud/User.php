<?php
/**
 * User class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a mutable Weebly Cloud user.
 */
class User extends Utils\CloudResource
{
	use Traits\MutableTrait;
	/**
	 * Unique ID of the user.
	 *
	 * @var string $user_id
	 */
	private $user_id;

	/**
	 * Creates a new User object.
	 *
	 * @param string $user_id Unique ID of the user.
	 * @param bool $initialize Whether or not to retrieve this user's properties
	 * 			from the server upon instantiation. The properties can later
	 * 			be retrieved by calling get().
	 * @param object $existing Object to use as the User's properties if initialize
	 * 			is false.
	 * @return User
	 */
	public function __construct($user_id, $initialize = true, $existing = null) {
		$this->user_id = $user_id;
		$this->url = "user/$user_id";
		if ($initialize) {
			$this->get();
		} else {
			$this->properties = $existing;
		}
	}

	/**
	 * Extracts properties from User's unique JSON response format.
	 *
	 * @param string $json JSON from an API response.
	 * @return object
	 */
	protected function propertiesFromJSON($json){
		return json_decode($json)->user;
	}

	/**
	 * Enables a user account after an account has been
	 * disabled. Enabling a user account will allow that user
	 * to log in and edit their sites. When a user is created,
	 * their account is automatically enabled.
	 */
	public function enable() {
		$client = Utils\CloudClient::getClient();
		$client->post($this->url . "/enable");
	}

	/**
	 * Disables a user account, preventing them from
	 * logging in or editing their sites.
	 */
	public function disable() {
		$client = Utils\CloudClient::getClient();
		$client->post($this->url . "/disable");
	}

	/**
	 * Generates a one-time login link. Will return an error
	 * if the user has been disabled.
	 */
	public function loginLink() {
		$client = Utils\CloudClient::getClient();
		$res = $client->post($this->url . "/loginLink");
		return json_decode($res->body)->link;
	}

	/**
	 * Returns an array of themes available to this
	 * user. The themes in the array are NOT resource
	 * objects, but are constructed directly from the
	 * response JSON.
	 *
	 * @param array $search_params Optional search parameters.
	 * @return array
	 */
	public function getAvailableThemes($search_params = []) {
		$client = Utils\CloudClient::getClient();
		$res = $client->getList($this->url . "/theme", $search_params);
		return json_decode($res->body)->data;
	}

	/**
	 * Adds a custom theme to a user.
	 *
	 * @param array $name The name of the theme.
	 * @param string $zip_url The URL of the .zip file
	 *			containing the theme. Must be publicly
	 *			accessible.
	 */
	public function createCustomTheme($name, $zip_url) {
		$client = Utils\CloudClient::getClient();
		$client->post(
			$this->url . "/theme",
			["name"=>$name, "zip_url"=>$zip_url]
		);
	}

	/**
	 * Creates a new Site belonging to this user in the database.
	 *
	 * @param string $domain The domain of the new site.
	 * @param array $data Associative array of site
	 *		properties. For the allowed properties, see the
	 *		API documentation.
	 */
	public function createSite($domain, $data = []) {
		$data = array_merge(["domain"=>$domain], $data);
		$client = Utils\CloudClient::getClient();
		$site_data = json_decode($client->post($this->url . "/site", $data)->body)->site;
		return new Site($this->user_id, $site_data->site_id, false, $site_data);
	}

	/**
	 * Returns a CloudList of Sites belonging to this user.
	 *
	 * @param array $search_params Search query parameters. See the API
	 *				documentation for valid parameters.
	 */
	public function listSites($search_params = []) {
		$client = Utils\CloudClient::getClient();
		$res =  $client->getList($this->url . "/site", $search_params);
		return new Utils\CloudList($res, "WeeblyCloud\Site", array("user_id"=>$this->user_id));
	}

	/**
	 * Returns the Site with the given id.
	 *
	 * @param string $site_id ID of the Site to return.
	 * @return Site
	 */
	public function getSite($site_id) {
		return new Site($this->user_id, $site_id);
	}
}
