<?php
/**
 * Handle licensing for the Premium addon.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/admin
 */

/**
 * Handle licensing for the Premium addon.
 *
 * @since      1.0.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/admin
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_License {

	/**
	 *  EDD store to contact.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $store EDD store to contact.
	 */
	private static $store = 'https://bootstrapped.ventures';

	/**
	 *  Premium products on this website.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      array $products Premium products on this website.
	 */
	private static $products = array();

	/**
	 * Register actions and filters.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		self::set_premium_bundle();

		add_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'check_license_status' ) );

		add_filter( 'wprm_settings_structure', array( __CLASS__, 'settings_structure' ) );
		add_filter( 'wprm_settings_update', array( __CLASS__, 'check_license_key_on_settings_update' ), 10, 2 );

		if ( is_admin() ) {
			add_filter( 'wprm_should_load_admin_assets', array( __CLASS__, 'load_admin_assets' ) );
			add_action( 'admin_notices', array( __CLASS__, 'license_inactive_notice' ) );
			add_action( 'admin_init', array( __CLASS__, 'edd_plugin_updater' ) );

			if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				include( WPRMP_DIR . 'vendor/edd/EDD_SL_Plugin_Updater.php' );
			}
		}
	}

	/**
	 * Set correct bundle as product.
	 *
	 * @since    2.0.0
	 */
	public static function set_premium_bundle() {
		switch ( WPRMP_BUNDLE ) {
			case 'Elite':
				self::$products['elite'] = array(
					'item_id' => 23343,
					'name' => 'WP Recipe Maker Premium - Elite Bundle',
					'file' => WPRMP_DIR . 'wp-recipe-maker-premium.php',
					'version' => WPRMP_VERSION,
				);
				break;
			case 'Pro':
				self::$products['pro'] = array(
					'item_id' => 23292,
					'name' => 'WP Recipe Maker Premium - Pro Bundle',
					'file' => WPRMP_DIR . 'wp-recipe-maker-premium.php',
					'version' => WPRMP_VERSION,
				);
				break;
			case 'Premium':
			default:
				self::$products['premium'] = array(
					'item_id' => 4684,
					'name' => 'WP Recipe Maker Premium',
					'file' => WPRMP_DIR . 'wp-recipe-maker-premium.php',
					'version' => WPRMP_VERSION,
				);
				break;
		}
	}

	/**
	 * Get all the WP Recipe Maker products on this website.
	 *
	 * @since    1.3.0
	 */
	public static function get_products() {
		return apply_filters( 'wprmp_edd_products', self::$products );
	}

	/**
	 * Set up plugin updater to check for plugin updates.
	 *
	 * @since    1.0.0
	 */
	public static function edd_plugin_updater() {
		$products = self::get_products();

		foreach ( $products as $id => $product ) {
			new EDD_SL_Plugin_Updater( self::$store, $product['file'], array(
					'version' 	=> $product['version'],
					'license' 	=> WPRM_Settings::get( 'license_' . $id ),
					'item_id' 	=> $product['item_id'],
					'author' 	=> 'Bootstrapped Ventures',
				)
			);
		}
	}

	/**
	 * Add license key settings.
	 *
	 * @since    3.0.0
	 * @param    array $structure Settings structure.
	 */
	public static function settings_structure( $structure ) {
		require( WPRMP_DIR . 'templates/admin/settings/license.php' );
		return array( 'licenseKey' => $license_key ) + $structure;
	}

	/**
	 * Check if the license key was updated.
	 *
	 * @since    3.0.0
	 * @param    array $new_settings Settings after update.
	 * @param    array $old_settings Settings before update.
	 */
	public static function check_license_key_on_settings_update( $new_settings, $old_settings ) {
		$products = self::get_products();

		foreach ( $products as $id => $product ) {
			$old_license = isset( $old_settings[ 'license_' . $id ] ) ? $old_settings[ 'license_' . $id ] : '';
			$new_license = isset( $new_settings[ 'license_' . $id ] ) ? $new_settings[ 'license_' . $id ] : '';

			// License hasn't changed and status is active: do nothing.
			if ( $old_license === $new_license && 'valid' === self::get_license_status( $id ) ) {
				continue;
			}
			
			// Something changed, so clear the status.
			self::update_license_status( $id, '' );

			// Deactivate the old license if there was one.
			if ( $old_license ) {
				self::deactivate_license( $id, $old_license );
			}

			// Activate the new license.
			self::activate_license( $id, $new_license );
		}

		return $new_settings;
	}

	public static function check_license_status( $transient ) {
		$products = self::get_products();

		foreach ( $products as $id => $product ) {
			self::update_license( $id, WPRM_Settings::get( 'license_' . $id ) );
		}

		// Only check once.
		remove_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'check_license_status' ) );

		return $transient;
	}

	/**
	 * Update the status of the license key.
	 *
	 * @since    1.0.0
	 * @param    mixed $id     ID of the product we are updating the license for.
	 * @param    mixed $status Status to set.
	 */
	public static function update_license_status( $id, $status ) {
		update_option( 'wprm_license_' . $id . '_status', $status, false );
	}

	/**
	 * Get the status of the license key.
	 *
	 * @since    3.0.0
	 * @param    mixed $id ID of the product we are getting the license status for.
	 */
	public static function get_license_status( $id ) {
		$status = get_option( 'wprm_license_' . $id . '_status', false );

		// Backwards compatibility.
		if ( false === $status ) {
			$status = WPRM_Settings::get( 'license_' . $id . '_status' );
		}

		return $status;
	}

	/**
	 * Activate a license key.
	 *
	 * @since    1.0.0
	 * @param    mixed $id     ID of the product we are activating the license for.
	 * @param    mixed $license License key to activate.
	 */
	public static function activate_license( $id, $license ) {
		$products = self::get_products();
		$product = $products[ $id ];

		$api_params = array(
			'edd_action' => 'activate_license',
			'license' 	 => $license,
			'item_id' 	 => $product['item_id'],
			'url'        => home_url(),
		);

		// Call the EDD license API.
		$response = wp_remote_post( self::$store, array( 'timeout' => 60, 'sslverify' => false, 'body' => $api_params ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data ) {
			self::update_license_status( $id, $license_data->license );
		}
	}

	/**
	 * Deactivate a license key.
	 *
	 * @since    1.0.0
	 * @param    mixed $id     ID of the product we are deactivating the license for.
	 * @param    mixed $license License key to deactivate.
	 */
	public static function deactivate_license( $id, $license ) {
		$products = self::get_products();
		$product = $products[ $id ];

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license' 	 => $license,
			'item_id'    => $product['item_id'],
			'url'        => home_url(),
		);

		// Call the EDD license API.
		$response = wp_remote_post( self::$store, array( 'timeout' => 60, 'sslverify' => false, 'body' => $api_params ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data && 'deactivated' === $license_data->license ) {
			return true;
		}
	}

	/**
	 * Update the license key status.
	 *
	 * @since	1.0.0
	 * @param	mixed $id     ID of the product we are updating the license for.
	 * @param	mixed $license License key to update.
	 */
	public static function update_license( $id, $license ) {
		$products = self::get_products();
		$product = $products[ $id ];

		$api_params = array(
			'edd_action' => 'check_license',
			'license' 	 => $license,
			'item_id' 	 => $product['item_id'],
			'url'        => home_url(),
		);

		// Call the EDD license API.
		$response = wp_remote_post( self::$store, array( 'timeout' => 60, 'sslverify' => false, 'body' => $api_params ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data ) {
			self::update_license_status( $id, $license_data->license );
		}
	}

	/**
	 * Load admin assets on plugins page to make sure license activation works there.
	 *
	 * @since    6.8.0
	 */
	public static function load_admin_assets( $load ) {
		$screen = get_current_screen();

		if ( $screen && 'plugins' === $screen->id && current_user_can( 'manage_options' ) ) {
			$products = self::get_products();

			foreach ( $products as $id => $product ) {
				if ( ! in_array( self::get_license_status( $id ), array( 'valid', 'expired' ) ) ) {
					return true;
				}
			}
		}

		return $load;
	}

	/**
	 * Show a notice on the plugin page if the license is inactive.
	 *
	 * @since    1.0.0
	 */
	public static function license_inactive_notice() {
		$screen = get_current_screen();

		if ( $screen && 'plugins' === $screen->id && current_user_can( 'manage_options' ) ) {
			$products = self::get_products();

			foreach ( $products as $id => $product ) {
				if ( 'expired' === self::get_license_status( $id ) ) {
					require( WPRMP_DIR . 'templates/admin/settings/license_expired.php' );
				} elseif ( 'valid' !== self::get_license_status( $id ) ) {
					require( WPRMP_DIR . 'templates/admin/settings/license_invalid.php' );
				}
			}
		}
	}
}

WPRMP_License::init();
