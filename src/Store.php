<?php
/**
 * Store class file
 *
 * @package WeeblyCloud
 * @author Benjamin Dean <benjamin@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Respresents a mutable site store.
 */
class Store extends Utils\CloudResource
{
    use Traits\MutableTrait;
    /**
     * ID of the user the store belongs to.
     *
     * @var string $user_id
     */
    private $user_id;

    /**
     * ID of the site the store belongs to.
     *
     * @var string $site_id
     */
    private $site_id;

    /**
     * Creates a new Store object.
     *
     * @param string $user_id ID of the user the store belongs to.
     * @param string $site_id ID of the site the store belongs to.
     * @param bool $initialize Whether or not to retrieve this store's properties from the server upon instantiation. The properties can later be retrieved by calling get().
     * @param object $existing Object to use as the Store's properties if initialize is false.
     */
    public function __construct($user_id, $site_id, $initialize = true, $existing = null)
    {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->url = "user/$user_id/site/$site_id/store";
        if ($initialize) {
            $this->get();
        }
    }


    /**
     * Update information about this store.
     *
     * @param array $data field names and respective values to update
     *
     * @return Store 
     */
    public function updateStore($data)
    {
        $client = Utils\CloudClient::getClient();
        return json_decode($client->patch($this->url, $data)->body);
    }

    /**
     * Returns a CloudList of Products belonging to this store.
     *
     * @param array $search_params Search query parameters. See the API
     *              documentation for valid parameters.
     *
     * @return Utils\CloudList
     */
    public function listProducts($search_params = [])
    {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/product", $search_params);
        return new Utils\CloudList(
            $res,
            "WeeblyCloud\Product",
            array("user_id"=>$this->user_id, "site_id"=>$this->site_id)
        );
    }

    /**
     * Returns the count of Products for this store..
     *
     * @return Integer 
     */
    public function getProductCount()
    {
        $client = Utils\CloudClient::getClient();
        return json_decode($client->get($this->url . "/product/count")->body)->count;
    }

    /**
     * Creates a new product with the given specified data and optional properties.
     *
     * @param string $email The user's email. Must be unique.
     * @param array $data Associative array of user properties.
     *
     * @return User
     */
    public function createProduct($product_name, $product_skus, $data = [])
    {
        $client = Utils\CloudClient::getClient();
        $product_data =  json_decode($client->post(
            $this->url . "/product",
            array_merge(["name"=>$product_name], ["skus"=>$product_skus], $data)
        )->body);
        return new Product($this->user_id, $this->site_id, $product_data->product_id, false, $product_data);
    }

    /**
     *  Product with the given id.
     *
     * @param string $product_id ID of the Product to return.
     *
     * @return Product 
     */
    public function getProduct($product_id)
    {
        return new Product($this->user_id, $this->site_id, $product_id);
    }
}
