<?php
/**
 * Plan class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a Weebly Cloud plan.
 */
class Plan extends Utils\CloudResource
{
    /**
     * Unique ID of the plan.
     *
     * @var string $plan_id
     */
    private $plan_id;

    /**
     * Creates a new Plan object.
     *
     * @param string $plan_id Unique ID of the plan.
     * @param bool $initialize Whether or not to retrieve this plan's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the Plan's properties if initialize
     *          is false.
     */
    public function __construct($plan_id, $initialize = true, $existing = null)
    {
        $this->url = "plan/$plan_id";
        $this->plan_id = $plan_id;
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }

    /**
     * Extracts properties from Plan's unique JSON response format.
     *
     * @param string $json JSON from an API response.
     * @return object
     */
    protected function propertiesFromJSON($json)
    {
        $plan_id = $this->plan_id;
        return json_decode($json)->plans->$plan_id;
    }

    /**
     * Converts a JSON response into an array of
     * Plan objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the Plans
     *              (none).
     * @param string $json JSON of a list of plans.
     *
     * @return array
     */
    public static function arrayFromJSON($ids, $json)
    {
        $plans = array();
        $arr = json_decode($json)->plans;
        foreach ($arr as $id => $plan) {
            $plans[] = new Plan($id, false, $plan);
        }
        return $plans;
    }
}
