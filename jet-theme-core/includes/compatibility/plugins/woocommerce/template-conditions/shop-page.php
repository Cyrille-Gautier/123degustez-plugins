<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Shop_Page extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-shop-page';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Shop Page', 'jet-theme-core' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'woocommerce';
	}

	/**
	 * @return string
	 */
	public function get_sub_group() {
		return 'woocommerce-page';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 8;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_products_archive';
	}

	/**
	 * @return array
	 */
	public function get_node_data() {
		return [
			'node'   => $this->get_id(),
			'parent' => 'entire',
			'inherit' => [ 'entire' ],
			'label' => $this->get_label(),
			'nodeInfo'  => [
				'title' => __( 'Shop Pages', 'jet-theme-core' ),
				'desc'  => __( 'Woocommerce pages', 'jet-theme-core' ),
			],
			'previewLink' => $this->get_preview_link(),
		];
	}

	/**
	 * @return void
	 */
	public function get_preview_link() {
		return get_permalink( wc_get_page_id( 'shop' ) );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $args ) {
		return is_shop();
	}

}
