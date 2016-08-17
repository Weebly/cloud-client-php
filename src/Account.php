<?php
/**
 * Account class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a mutable Weebly Cloud admin account.
 */
class Account extends Utils\CloudResource
{
    use Traits\MutableTrait;
    /**
     * Creates a new Account object.
     *
     * @param bool $initialize Whether or not to retrieve this account's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the Account's properties if initialize
     *          is false.
     *
     * @return Account
     */
    public function __construct($initialize = true, $existing = null) {
        $this->url = "account";
        if($initialize) {
            $this->get();
        }
    }

    /**
     * Extracts properties from Account's unique JSON response format.
     *
     * @param string $json JSON from an API response.
     *
     * @return object
     */
    protected function propertiesFromJSON($json) {
        return json_decode($json)->account;
    }

    /**
     * Creates a new user with the given email and optional properties.
     *
     * @param string $email The user's email. Must be unique.
     * @param array $data Associative array of user properties.
     *
     * @return User
     */
    public function createUser($email, $data = []) {
        $client = Utils\CloudClient::getClient();
        $user =  json_decode($client->post(
            "user",
            array_merge(["email"=>$email],$data)
        )->body)->user;
        return new User($user->user_id, false, $user);
    }

    /**
    * Gets a CloudList of available plans.
    *
    * @return Utils\CloudList
    */
    public function listPlans() {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList("plan");
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\Plan",
            array()
        );
    }

    /**
    * Gets a single Plan by ID.
    * @param string $plan_id ID of the plan to return.
    *
    * @return Plan
    */
    public function getPlan($plan_id) {
        return new Plan($plan_id);
    }

}
