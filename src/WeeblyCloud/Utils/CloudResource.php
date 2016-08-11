<?php
/**
 * CloudResource class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 *
 */

namespace WeeblyCloud\Utils;

/**
* Represents a Weebly Cloud resource.
*/
abstract class CloudResource {
	/**
	 * Unique URL of the resource.
	 *
	 * @var string $url
	 */
	public $url;

	/**
	 * Properties of the resource.
	 *
	 * @var object $properties
	 */
	protected $properties;

	/**
	 * Properties that have been changed by setProperty.
	 *
	 * @var array $changed
	 */
	protected $changed;

	/**
	 * False if get() has not yet been called.
	 *
	 * @var bool $got
	 */
	private $got;

	/**
	 * Gets the object's properties from the database.
	 */
	public function get() {

		$this->got = true;
		$res = CloudClient::getClient()->get($this->url);
		$this->properties = $this->propertiesFromJSON($res->body);
	}

	/**
	 * Gets a property of the resource.
	 *
	 * @param string $property The property name.
	 * @return mixed
	 */
	public function getProperty($property) {
		if (!$this->got && !isset($this->properties->$property)) {
			$this->get();
		}
		return $this->properties->$property;
	}

	/**
	* Extracts properties from normal JSON response format;
	* this is overriden in classes with special formats.
	* @param string $json
	*/
	protected function propertiesFromJSON($json){
		return json_decode($json);
	}
}
