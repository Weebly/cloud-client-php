<?php
/**
 * Site class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a mutable and deletable Weebly Cloud site.
 */
class Site extends Utils\CloudResource
{
    use Traits\DeletableTrait, Traits\MutableTrait;
    /**
     * ID of the user the site belongs to.
     *
     * @var string $user_id
     */
    private $user_id;

    /**
     * Unique ID of the site.
     *
     * @var string $site_id
     */
    private $site_id;

    /**
     * Creates a new Site object.
     *
     * @param string $user_id ID of the user who owns the site.
     * @param string $site_id Unique ID of the site.
     * @param bool $initialize Whether or not to retrieve this site's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the Site's properties if initialize
     *          is false.
     * @return Site
     */
    public function __construct($user_id, $site_id, $initialize = true, $existing = null)
    {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->url = "user/$user_id/site/$site_id";
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }

    /**
     * Extracts properties from Site's unique JSON response format.
     *
     * @param string $json JSON from an API response.
     *
     * @return object
     */
    protected function propertiesFromJSON($json)
    {
        return json_decode($json)->site;
    }

    /**
     * Publishes a site.
     */
    public function publish()
    {
        $client = Utils\CloudClient::getClient();
        $client->post($this->url . "/publish");
    }

    /**
     * Unpublishes a site.
     */
    public function unpublish()
    {
        $client = Utils\CloudClient::getClient();
        $client->post($this->url . "/unpublish");
    }

    /**
     * Generates a one-time login link for the user that
     * automatically redirects them to the site editor for
     * this site.
     *
     * @return string
     */
    public function loginLink()
    {
        $client = Utils\CloudClient::getClient();
        return json_decode($client->post($this->url . "/loginLink")->body)->link;
    }

    /**
     * Sets publish credentials for a given site. If a user's site
     * will not be hosted by Weebly, publish credentials can be
     * provided. When these values are set, the site will be
     * published to the location specified.
     *
     * @param array $data The publish credentials. Required properties
     *                    are publish_host, publish_username, publish_password,
     *                    and publish_path.
     */
    public function setPublishCredentials($data)
    {
        $client = Utils\CloudClient::getClient();
        $client->post($this->url . "/setPublishCredentials", $data);
    }

    /**
     * Restores a deleted site to the exact state it
     * was in when deleted.
     *
     * @param string $domain The domain of the site.
     */
    public function restore($domain)
    {
        $client = Utils\CloudClient::getClient();
        $client->post(
            $this->url . "/restore",
            ["domain"=>$domain]
        );
    }

    /**
     * Enables a site, allowing it to be edited.
     */
    public function enable()
    {
        $client = Utils\CloudClient::getClient();
        $client->post($this->url . "/enable");
    }

    /**
     * Disables a site, preventing the user from
     * accessing it through the editor.
     */
    public function disable()
    {
        $client = Utils\CloudClient::getClient();
        $client->post($this->url . "/disable");
    }

    /**
     * Gets the Plan assigned to the site.
     *
     * @return Plan
     */
    public function getPlan()
    {
        $res = Utils\CloudClient::getClient()->get($this->url . "/plan")->body;
        return Plan::arrayFromJSON([], $res)[0];
    }

    /**
     * Assigns a plan to the site.
     *
     * @param string $plan_id ID of the plan to assign to the site.
     * @param int $term Optional term length, defaults to 1.
     */
    public function setPlan($plan_id, $term = 1)
    {
        $client = Utils\CloudClient::getClient();
        $client->post(
            $this->url . "/plan",
            ["plan_id"=>$plan_id, "term"=>$term]
        );
    }

    /**
     * Assigns a theme to the site by ID.
     *
     * @param string $theme_id The ID of the theme.
     * @param bool $is_custom Whether or not the theme is a custom theme.
     */
    public function setTheme($theme_id, $is_custom)
    {
        $client = Utils\CloudClient::getClient();
        $client->post(
            $this->url . "/theme",
            ["theme_id"=>$theme_id, "is_custom"=>$is_custom]
        );
    }

    /**
     * Returns a CloudList of Blogs on this Site.
     *
     * @return Utils\CloudList
     */
    public function listBlogs()
    {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/blog");
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\Blog",
            ["user_id"=>$this->user_id, "site_id"=>$this->site_id]
        );
    }

    /**
     * Returns the Blog with the given ID.
     *
     * @param string $blog_id ID of the Blog to return.
     *
     * @return Blog
     */
    public function getBlog($blog_id)
    {
        return new Blog($this->user_id, $this->site_id, $blog_id);
    }

    /**
     * Returns a CloudList of Forms on this Site.
     *
     * @param array $search_params Optional search query parameters. See the API
     *              documentation for valid parameters.
     * @return Utils\CloudList
     */
    public function listForms($search_params = [])
    {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/form", $search_params);
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\Form",
            array("user_id"=>$this->user_id, "site_id"=>$this->site_id)
        );
    }

    /**
     * Returns the Form with the given id.
     *
     * @param string $form_id ID of the Form to return.
     *
     * @return Form
     */
    public function getForm($form_id)
    {
        return new Form($this->user_id, $this->site_id, $form_id);
    }

    /**
     * Returns a CloudList of Pages on this Site.
     *
     * @param array $search_params Optional search query parameters. See the API
     *              documentation for valid parameters.
     *
     * @return Utils\CloudList
     */
    public function listPages($search_params = [])
    {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/page", $search_params);
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\Page",
            array("user_id"=>$this->user_id, "site_id"=>$this->site_id)
        );
    }

    /**
     * Returns the Page with the given id.
     *
     * @param string $page_id ID of the Page to return.
     *
     * @return Page
     */
    public function getPage($page_id)
    {
        return new Page($this->user_id, $this->site_id, $page_id);
    }

    /**
     * Returns a CloudList of Groups on this Site.
     *
     * @param array $search_params Optional search query parameters. See the API
     *              documentation for valid parameters.
     *
     * @return Utils\CloudList
     */
    public function listGroups($search_params = [])
    {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/group", $search_params);
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\Group",
            array("user_id"=>$this->user_id, "site_id"=>$this->site_id)
        );
    }

    /**
     * Returns the Group with the given id.
     * @param string $group_id ID of the Group to return.
     *
     * @return Group
     */
    public function getGroup($group_id)
    {
        return new Group($this->user_id, $this->site_id, $group_id);
    }

    /**
     * Creates a new Group of members for the site.
     *
     * @param string $name The name of the group to be created.
     *
     * @return Group
     */
    public function createGroup($name)
    {
        $client = Utils\CloudClient::getClient();
        $group = json_decode($client->post($this->url . "/group", ["name"=>$name])->body);
        return new Group($this->user_id, $this->site_id, $group->group_id, false, $group);
    }

    /**
     * Returns a CloudList of Members on this Site.
     *
     * @param array $search_params Optional search query parameters. See the API
     *              documentation for valid parameters.
     *
     * @return Utils\CloudList
     */
    public function listMembers($search_params = [])
    {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/member", $search_params);
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\Member",
            array("user_id"=>$this->user_id, "site_id"=>$this->site_id)
        );
    }

    /**
     * Returns the Member with the given id.
     *
     * @param string $member_id ID of the Member to return.
     *
     * @return Member
     */
    public function getMember($member_id)
    {
        return new Member($this->user_id, $this->site_id, $member_id);
    }

    /**
     * Creates a new Member of the site in the database.
     *
     * @param array $data The properties of the new member. See the
     * API for required properties.
     *
     * @return Member
     */
    public function createMember($data)
    {
        $client = Utils\CloudClient::getClient();
        $member = json_decode($client->post($this->url . "/member", $data)->body);
        return new Member($this->user_id, $this->site_id, $member->member_id, false, $member);
    }

    /**
     * Converts a JSON response into an array of
     * Site objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the Sites
     *              (user_id).
     * @param string $json JSON of a list of sites.
     *
     * @return array
     */
    public static function arrayFromJSON($ids, $json)
    {
        $user_id = $ids["user_id"];
        $sites = array();
        $arr = json_decode($json)->sites;
        foreach ($arr as $site) {
            $sites[] = new Site($user_id, $site->site_id, false, $site);
        }
        return $sites;
    }
}
