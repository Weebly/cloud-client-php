<?php
/**
 * Form class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a form on a site.
 */
class Form extends Utils\CloudResource
{

    /**
     * ID of the user the form belongs to.
     *
     * @var string $user_id
     */
    private $user_id;

    /**
     * ID of the site the form belongs to.
     *
     * @var string $site_id
     */
    private $site_id;

    /**
     * Unique ID of the form.
     *
     * @var string $form_id
     */
    private $form_id;

    /**
     * Creates a new Form object.
     *
     * @param string $user_id ID of the user the form belongs to.
     * @param string $site_id ID of the site the form belongs to.
     * @param string $form_id Unique ID of the form.
     * @param bool $initialize Whether or not to retrieve this form's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the Form's properties if initialize
     *          is false.
     */
    public function __construct($user_id, $site_id, $form_id, $initialize = true, $existing = null)
    {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->form_id = $form_id;
        $this->url = "user/$user_id/site/$site_id/form/$form_id";
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }

    /**
     * Returns a CloudList of FormEntries for this Form.
     *
     * @param array $search_params Optional search query parameters. See the API
     *              documentation for valid parameters.
     *
     * @return CloudList
     */
    public function listFormEntries($search_params = [])
    {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/entry", $search_params);
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\FormEntry",
            array("user_id"=>$this->user_id, "site_id"=>$this->site_id, "form_id"=>$this->form_id)
        );
    }

    /**
     * Returns the FormEntry with the given id.
     *
     * @param string $form_entry_id ID of the FormEntry to return.
     *
     * @return FormEntry
     */
    public function getFormEntry($form_entry_id)
    {
        return new FormEntry($this->user_id, $this->site_id, $this->form_id, $form_entry_id);
    }

    /**
     * Converts a JSON response into an array of
     * Form objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the Forms
     *              (user_id and site_id).
     * @param string $json JSON of a list of forms.
     *
     * @return array
     */
    public static function arrayFromJSON($ids, $json)
    {
        $user_id = $ids["user_id"];
        $site_id = $ids["site_id"];
        $forms = array();
        $arr = json_decode($json);
        foreach ($arr as $form) {
            $forms[] = new Form($user_id, $site_id, $form->form_id, false, $form);
        }
        return $forms;
    }
}
