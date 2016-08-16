<?php
/**
 * Page class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a mutable page on a Weebly Cloud site.
 */
class Page extends Utils\CloudResource
{
    use Traits\MutableTrait;
    /**
     * ID of the user the page belongs to.
     *
     * @var string $user_id
     */
    private $user_id;

    /**
     * ID of the site the page belongs to.
     *
     * @var string $site_id
     */
    private $site_id;

    /**
     * Unique ID of the page.
     *
     * @var string $page_id
     */
    private $page_id;

    /**
     * Creates a new Page object.
     *
     * @param string $user_id ID of the user the page belongs to.
     * @param string $site_id ID of the site the page belongs to.
     * @param string $page_id Unique ID of the page.
     * @param bool $initialize Whether or not to retrieve this page's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the Page's properties if initialize
     *          is false.
     * @return Page
     */
    public function __construct($user_id, $site_id, $page_id, $initialize=true, $existing = null) {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->page_id = $page_id;
        $this->url = "user/$user_id/site/$site_id/page/$page_id";
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }

    /**
     * Converts a JSON response into an array of
     * Page objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the Pages
     *              (user_id and site_id).
     * @param string $json JSON of a list of pages.
     * @return CloudList
     */
    public static function arrayFromJSON($ids, $json) {
        $user_id = $ids["user_id"];
        $site_id = $ids["site_id"];
        $pages = array();
        $arr = json_decode($json);
        foreach ($arr as $page) {
            $pages[] = new Page($user_id, $site_id, $page->page_id, false, $page);
        }
        return $pages;
    }
}
