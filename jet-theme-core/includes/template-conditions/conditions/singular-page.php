<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Page extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'singular-page';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Page', 'jet-theme-core' );
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
	 * @return string
	 */
	public function get_sub_group() {
		return 'page-singular';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 60;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_page';
	}

	/**
	 * @return array
	 */
	public function get_node_data() {
		return [
			'node'   => $this->get_id(),
			'parent' => 'entire',
			'inherit' => [ 'entire' ],
			'label' => __( 'Page', 'jet-theme-core' ),
			'nodeInfo'  => [
				'title' => __( 'Singular Page', 'jet-theme-core' ),
				'desc' => __( 'Description Singular Page', 'jet-theme-core' ),
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
			'placeholder' => __( 'Select page', 'jet-theme-core' ),
		];
	}

	/**
	 * [ajax_action description]
	 * @return [type] [description]
	 */
	public function ajax_action() {
		return [
			'action' => 'get-static-pages',
			'params' => []
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {
		return get_the_title( $value );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( empty( $arg ) || 'all' === $arg ) {
			return is_page();
		}

		$page_id = apply_filters( 'jet-theme-core/template-conditions/singular-page-condition/page-id', $arg );

		return is_page( $page_id );
	}

}
