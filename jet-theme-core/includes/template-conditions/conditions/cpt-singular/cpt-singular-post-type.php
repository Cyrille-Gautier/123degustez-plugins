<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class CPT_Singular_Post_Type extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'singular-post-type';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'CPT Single(legacy)', 'jet-theme-core' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'singular';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 45;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_single';
	}

	/**
	 * @return array
	 */
	public function get_node_data() {
		return [
			'node'   => $this->get_id(),
			'parent' => 'archive-all',
			'label' => __( 'CPT Taxonomy(legacy)', 'jet-theme-core' ),
			'subNode' => true,
			'nodeInfo'  => [
				'title'     => __( 'CPT Single(legacy)', 'jet-theme-core' ),
				'desc'      => __( 'Templates for CPT single(legacy)', 'jet-theme-core' ),
			]
		];
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'select',
			'placeholder' => __( 'Select post type', 'jet-theme-core' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-post-types',
			'params' => []
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {

		$obj = get_post_type_object( $value );

		if ( ! $obj ) {
			return __( 'Undefined', 'jet-theme-core' );
		}

		return $obj->labels->singular_name;
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( empty( $arg ) ) {
			return is_singular();
		}

		return is_singular( $arg );
	}

}
