<?php
/**
 * FormEntry class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a form entry on a form.
 */
class FormEntry extends Utils\CloudResource
{
    /**
     * ID of the user the form entry belongs to.
     *
     * @var string $user_id
     */
    private $user_id;

    /**
     * ID of the site the form entry belongs to.
     *
     * @var string $site_id
     */
    private $site_id;

    /**
     * ID of the form the entry belongs to.
     *
     * @var string $form_id
     */
    private $form_id;

    /**
     * Unique ID of the form entry.
     *
     * @var string $entry_id
     */
    private $entry_id;

    /**
     * Creates a new FormEntry object.
     *
     * @param string $user_id ID of the user the entry belongs to.
     * @param string $site_id ID of the site the entry belongs to.
     * @param string $form_id ID of the form the entry belongs to.
     * @param string $entry_id Unique ID of the form entry.
     * @param bool $initialize Whether or not to retrieve this form entry's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the FormEntry's properties if initialize
     *          is false.
     */
    public function __construct($user_id, $site_id, $form_id, $entry_id, $initialize = true, $existing = null) {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->form_id = $form_id;
        $this->entry_id = $entry_id;
        $this->url = "user/$user_id/site/$site_id/form/$form_id/entry/$entry_id";
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }

    /**
     * Converts a JSON response into an array of
     * FormEntry objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the FormEntries
     *              (user_id, site_id, and form_id).
     * @param string $json JSON of a list of form entries.
     *
     * @return array
     */
    public static function arrayFromJSON($ids, $json) {
        $user_id = $ids["user_id"];
        $site_id = $ids["site_id"];
        $form_id = $ids["form_id"];
        $entries = array();
        $arr = json_decode($json);
        foreach ($arr as $entry) {
            $entries[] = new FormEntry($user_id, $site_id, $form_id, $entry->form_entry_id, false, $entry);
        }
        return $entries;
    }

}
