<?php
/**
 * CloudException class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud\Utils;

/**
 * A response from the Weebly Cloud API.
 */
class CloudResponse
{
    /**
     * The body of the response in JSON format.
     *
     * @var object $body
     */
    public $body;

    /**
     * The endpoint URL used to retrieve the response.
     *
     * @var string $url
     */
    public $url;

    /**
     * The total number of objects in the result set,
     * which is NOT necessarily the number of objects
     * in the response body, if the result is paginated.
     *
     * @var int $total
     */
    public $total;

    /**
     * The page of the result.
     *
     * @var int $page
     */
    public $page;

    /**
     * The total number of pages.
     *
     * @var int $page_count
     */
    public $page_count;

    /**
     * The maximum number of results per page.
     *
     * @var int $page_limit
     */
    public $page_limit;

    /**
     * The query parameters used to retrieve the response.
     *
     * @var array $parameters
     */
    public $parameters;

    /**
     * Whether or not the response is paginated.
     *
     * @var boolean $is_paginated
     */
    public $is_paginated;

    /**
     * Creates a CloudResponse.
     *
     * @param string $header The headers from the HTTP response.
     * @param string $response The body of the response in JSON format.
     * @param string $url The endpoint URL used to retrieve the response
     * @param array $parameters The query parameters used to retrieve the response.
     */
    public function __construct($header, $response, $url, $parameters) {
        //Extract custom fields from the header string
        preg_match_all("/^X-Resultset-([^:]+): ([\d]+).+$/m", $header, $p);
        $header_fields = array_combine($p[1], $p[2]);

        $this->total = $header_fields['Total'] ?: -1;
        $this->page_limit = $header_fields['Limit'] ?: -1;
        $this->page = $header_fields['Page'] ?: -1;
        $this->page_count = (int) ceil($this->total / $this->page_limit);
        $this->is_paginated = (($this->total) > 0);
        $this->body = $response;
        $this->parameters = $parameters;
        $this->url = $url;
    }

    /**
    * Returns the CloudResponse containing the next page
    * if there is a next page, null if there is no next page.
    *
    * @return CloudResponse
    */
    public function getNextPage() {
        if((!$this->is_paginated) || ($this->page >= $this->page_count)) {
            return null;
        }

        return CloudClient::getClient()->get(
            $this->url,
            array_merge($this->parameters, array("page" => ($this->page + 1)))
        );
    }

    /**
    * Returns the CloudResponse containing the previous page
    * if there is a previous page, null if there is no previous page.
    *
    * @return instance
    */
    public function getPreviousPage() {
        if((!$this->is_paginated) || ($this->page <= 1)) {
            return null;
        }

        return CloudClient::getClient()->get(
            $this->url,
            array_merge($this->parameters, array("page" => ($this->page - 1)))
        );
    }
}
