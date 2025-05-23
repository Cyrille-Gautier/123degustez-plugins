<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_Account_Login_Page extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woo-account-login-page';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Account Login Page', 'jet-theme-core' );
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
		return 'woocommerce-my-account';
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return 58;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_account_page';
	}

	/**
	 * @return array
	 */
	public function get_node_data() {
		return [
			'node'   => $this->get_id(),
			'parent' => 'woo-account-page',
			'inherit' => [ 'entire', 'woo-account-page' ],
			'label'  => __( 'Login Page', 'jet-theme-core' ),
			'previewLink' => $this->get_preview_link(),
		];
	}

	/**
	 * @return string|null
	 */
	public function get_preview_link() {
		$checkout_page_id = get_option( 'woocommerce_myaccount_page_id' );
		$checkout_url = get_permalink( $checkout_page_id );

		return esc_url( $checkout_url );
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $args ) {
		return is_account_page() && !is_user_logged_in();
	}

}
