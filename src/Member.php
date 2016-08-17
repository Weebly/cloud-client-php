<?php
/**
 * Member class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Respresents a mutable and deletable site member.
 */
class Member extends Utils\CloudResource
{
    use Traits\DeletableTrait, Traits\MutableTrait;
    /**
     * ID of the user the member belongs to.
     *
     * @var string $user_id
     */
    private $user_id;

    /**
     * ID of the site the member belongs to.
     *
     * @var string $site_id
     */
    private $site_id;

    /**
     * Unique ID of the member.
     *
     * @var string $member_id
     */
    private $member_id;

    /**
     * Creates a new Member object.
     *
     * @param string $user_id ID of the user the member belongs to.
     * @param string $site_id ID of the site the member belongs to.
     * @param string $member_id Unique ID of the member.
     * @param bool $initialize Whether or not to retrieve this member's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the Member's properties if initialize
     *          is false.
     */
    public function __construct($user_id, $site_id, $member_id, $initialize=true, $existing = null) {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->member_id = $member_id;
        $this->url = "user/$user_id/site/$site_id/member/$member_id";
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }

    /**
     * Converts a JSON response into an array of
     * Member objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the Members
     *              (user_id and site_id).
     * @param string $json JSON of a list of members.
     *
     * @return array
     */
    public static function arrayFromJSON($ids, $json) {
        $user_id = $ids["user_id"];
        $site_id = $ids["site_id"];
        $members = array();
        $arr = json_decode($json);
        foreach ($arr as $member) {
            $members[] = new Member($user_id, $site_id, $member->member_id, false, $member);
        }
        return $members;
    }

}
