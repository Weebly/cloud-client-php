<?php
/**
 * Form class file
 *
 * @package WeeblyCloud
 * @author Benjamin Dean <benjamin@weebly.com>
 */
namespace WeeblyCloud;
/**
 * Respresents a mutable and deletable Product(s) on a site.
 */
class Product extends Utils\CloudResource
{
    use Traits\DeletableTrait, Traits\MutableTrait;
    /**
     * ID of the user the product belongs to.
     *
     * @var string $user_id
     */
    private $user_id;
    /**
     * ID of the site the product belongs to.
     *
     * @var string $site_id
     */
    private $site_id;
    /**
     * Unique ID of the product.
     *
     * @var string $product_id
     */
    private $product_id;
    /**
     * Creates a new Product object.
     *
     * @param string $user_id ID of the user the product belongs to.
     * @param string $site_id ID of the site the product belongs to.
     * @param string $product_id ID of the product.
     * @param bool $initialize Whether or not to retrieve this product's properties from the server upon instantiation. The properties can later be retrieved by calling get().
     * @param object $existing Object to use as the Product's properties if initialize is false.
     */
    public function __construct($user_id, $site_id, $product_id, $initialize = true, $existing = null)
    {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->product_id = $product_id;
        $this->url = "user/$user_id/site/$site_id/store/product/$product_id";
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }
    /**
     * Publishes this product.
     */
    public function publish()
    {
        $client = Utils\CloudClient::getClient();
        $client->patch($this->url,
            ["published"=>true]
        );
    }
    /**
     * Unpublishes this product.
     */
    public function unpublish()
    {
        $client = Utils\CloudClient::getClient();
        $client->patch($this->url,
            ["published"=>false]
        );
    }

    /**
     * Converts a JSON response into an array of
     * Product objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the Products 
     *              (user_id).
     * @param string $json JSON of a list of sites.
     *
     * @return array
     */
    public static function arrayFromJSON($ids, $json)
    {
        $user_id = $ids["user_id"];
        $products = array();
        $arr = json_decode($json);
        var_dump($arr);
        foreach ($arr as $product) {
            $products[] = new Product($user_id, $site->site_id, false, $product);
        }
        return $products;
    }
}
