<?php
/**
 * Extended Bricks query object
 */

namespace Jet_Engine\Bricks_Views\Listing;

/**
 * Define render class
 */
class Bricks_Query extends \Bricks\Query {

	public $is_component_listing = false;

	/**
	 * Class constructor
	 *
	 * @param array $element
	 */
	public function __construct( $element = [] ) {
		$this->register_query();

		$this->element_id   = ! empty( $element['id'] ) ? $element['id'] : 'jet-listing';
		$this->object_type  = 'jet-engine-query';
		$this->settings     = ! empty( $element['settings'] ) ? $element['settings'] : [];
		$this->query_result = [];
		$this->is_looping   = true;
	}
}
