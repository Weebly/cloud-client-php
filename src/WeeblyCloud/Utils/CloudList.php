<?php
/**
 * CloudList class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud\Utils;

/**
* List of CloudResources that hides pagination.
*/
class CloudList implements \Iterator{
	/**
	 * A CloudResponse for getting the next page if needed.
	 *
	 * @var CloudResponse $res
	 */
	public $res;

	/**
	 * The array of CloudResources.
	 *
	 * @var array $list
	 */
	private $list;

	/**
	 * The fully qualified class name of the class
	 * stored in the list.
	 *
	 * @var string $class
	 */
	private $class;

	/**
	 * The index of the current element in the list.
	 *
	 * @var int $index
	 */
	private $index;

	/**
	 * The size of the list. May be greater than count($this->list)
	 * if the endpoint is paginated and not all pages have been loaded.
	 *
	 * @var $size
	 */
	private $size;

	/**
	 * The ids required to construct the CloudResources
	 * in list.
	 *
	 * @var string $class
	 */
	private $ids;

	/**
	 * Whether or not the CloudList is paginated.
	 *
	 * @var boolean $is_paginated
	 */
	private $is_paginated;

	/**
	 * Creates a new CloudList object.
	 *
	 * @param CloudResponse $res A CloudResponse returned by a GET call.
	 * @param string $class The fully qualified class name of the class stored in the list.
	 * @param array $ids The ids required to construct the CloudResources in list
	 *				(e.g. user_id for Site objects).
	 * @return CloudList
	 */
	public function __construct($res, $class, $ids) {
		$this->res = $res;
		$this->class = $class;
		$this->ids = $ids;
		$this->index = 0;
		$this->is_paginated = ($this->res->is_paginated);
		$this->list = $class::arrayFromJSON($this->ids, $res->body);
		$this->size = $this->is_paginated? $res->total : count($this->pages[0]);
	}

	/**
	 * Returns the size of the list. May be greater than count($this->list)
	 * if the endpoint is paginated and not all pages have been loaded.
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Appends the next page of responses to the list array. Returns true if
	 * there is a next page, false otherwise.
	 *
	 * @return boolean
	 */
	private function nextPage(){
		if (!$this->isPaginated() || !$this->res) {
			return false;
		}
		$class = $this->class;
		$this->res = $this->res->getNextPage();
		if ($this->res) {
			$this->list = array_merge(
				$this->list,
				$class::arrayFromJSON($this->ids, $this->res->body)
			);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Rewinds the list to the beginning.
	 */
	public function rewind() {
		$this->index = 0;
	}

	/**
	 * Returns the current CloudResource.
	 *
	 * @return CloudResource
	 */
	public function current() {
		return $this->list[$this->index];
	}

	/**
	 * Returns the current index.
	 *
	 * @return int
	 */
	public function key() {
		return $this->index;
	}

	/**
	 * Increments the list index and returns the element
	 * that the index was previously pointing at. If there
	 * is no element at that index, returns null.
	 *
	 * @return CloudResource
	 */
	public function next(){
		$this->index++;
		if ($this->index > (count($this->list)) &&
			(!$this->isPaginated() || !$this->nextPage())) {
			return null;
		}

		return $this->list[$this->index - 1];
	}

	/**
	 * True if the current index is valid; false otherwise.
	 *
	 * @return boolean
	 */
	public function valid() {
		return ($this->index < ($this->size));
	}

	/**
	 * Whether or not the CloudList is paginated.
	 *
	 * @return boolean
	 */
	public function isPaginated() {
		return $this->is_paginated;
	}

}
