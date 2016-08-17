<?php
/**
 * DeletableTrait trait file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 *
 */

namespace WeeblyCloud\Traits;

use WeeblyCloud\Utils;

/**
 * Allows the resource to be deleted.
 */
trait DeletableTrait
{
    /**
     * Deletes the object from the database.
     */
    public function delete() {
            Utils\CloudClient::getClient()->delete($this->url);
    }
}
