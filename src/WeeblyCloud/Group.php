<?php
/**
 * Group class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a mutable and deletable group of site members.
 */
class Group extends Utils\CloudResource
{
	use Traits\DeletableTrait, Traits\MutableTrait;
	/**
	 * ID of the user the group belongs to.
	 *
	 * @var string $user_id
	 */
	private $user_id;

	/**
	 * ID of the site the group belongs to.
	 *
	 * @var string $site_id
	 */
	private $site_id;

	/**
	 * Unique ID of the group.
	 *
	 * @var string $group_id
	 */
	private $group_id;

	/**
	 * Creates a new Group object.
	 *
	 * @param string $user_id ID of the user the group belongs to.
	 * @param string $site_id ID of the site the group belongs to.
	 * @param string $group_id Unique ID of the group.
	 * @param bool $initialize Whether or not to retrieve this group's properties
	 * 			from the server upon instantiation. The properties can later
	 * 			be retrieved by calling get().
	 * @param object $existing Object to use as the Group's properties if initialize
	 * 			is false.
	 * @return Group
	 */
	public function __construct($user_id, $site_id, $group_id, $initialize = true, $existing = null){
		$this->user_id = $user_id;
		$this->site_id = $site_id;
		$this->group_id = $group_id;
		$this->url = "user/$user_id/site/$site_id/group/$group_id";
		if ($initialize) {
			$this->get();
		} else {
			$this->properties = $existing;
		}
	}

	/**
	 * Converts a JSON response into an array of
	 * Group objects. Because the formatting of responses
	 * and the IDS needed for instantiation are
	 * inconsistent across endpoints, this is handled
	 * on a class-by-class basis.
	 *
	 * @param array $ids The IDs necessary to construct the Groups
	 *				(user_id and site_id).
	 * @param string $json JSON of a list of groups.
	 * @return CloudList
	 */
	public static function arrayFromJSON($ids, $json) {
		$user_id = $ids["user_id"];
		$site_id = $ids["site_id"];
		$groups = array();
		$arr = json_decode($json);
		foreach ($arr as $group) {
			$groups[] = new Group($user_id, $site_id, $group->group_id, false, $group);
		}
		return $groups;
	}

}
