<?php
/**
 * MutableTrait trait file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 *
 */

namespace WeeblyCloud\Traits;

use WeeblyCloud\Utils;

/**
 * Allows the resource to be changed.
 */
trait MutableTrait
{
    /**
     * Saves changed values to the database.
     */
    public function save() {
        Utils\CloudClient::getClient()->patch($this->url, $this->changed);
        $this->changed = array();
    }

    /**
     * Sets a property of the resource. This change is NOT
     * saved in the database until save() is called.
     *
     * @param string $property
     * @param mixed $value
     * @return boolean
     */
    public function setProperty($property, $value) {
        $this->changed[$property] = $value;

        if (!$this->got) {
            $this->get();
        }

        if(isset($this->properties->$property)) {
            $this->properties->$property = $value;
            return true;
        }

        return false;
    }
}
