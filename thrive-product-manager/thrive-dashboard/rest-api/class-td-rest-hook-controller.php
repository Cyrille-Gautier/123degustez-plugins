<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TD_REST_Hook_Controller
 *
 * Used to implement Zapier Integration but it can be extended
 * For further REST Hooks
 */
class TD_REST_Hook_Controller extends TD_REST_Controller {

	/**
	 * The base of this controller's route.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	protected $rest_base;

	/**
	 * REST Hook Name
	 * - saves the webhook based on this name
	 *
	 * @var string
	 */
	protected $_hook_name;

	/**
	 * Needed to decide webhook's name
	 *
	 * @var string
	 */
	protected $_hook_prefix = 'td_';

	/**
	 * Needed to decide webhook's name
	 *
	 * @var string
	 */
	protected $_hook_suffix = '_webhook';

	/**
	 * TD_REST_Hook_Controller constructor.
	 *
	 * @param string $hook_name
	 */
	public function __construct( $hook_name = '' ) {

		parent::__construct();

		$this->_hook_name = (string) $hook_name;
		$this->rest_base  = trailingslashit( $hook_name ) . 'subscription';
	}

	/**
	 * Register routes
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			$this->rest_base,
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'subscribe' ),
				'permission_callback' => array( $this, 'permission_callback' ),
				'args'                => $this->route_args(),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->rest_base . '/sample',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'sample' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->rest_base . '/specific_form_data',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'specific_form_data' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->rest_base,
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'unsubscribe' ),
				'permission_callback' => array( $this, 'permission_callback' ),
				'args'                => $this->route_args(),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->rest_base . '/all_lg_forms',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'all_lg_forms' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
	}

	/**
	 * The endpoint where the Integration subscribes the webhook
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error
	 */
	public function subscribe( $request ) {

		// Param from Lead Generation auth
		$hook_url = $request->get_param( 'hook_url' );

		// Param from Contact Form auth
		if ( ! $hook_url ) {
			$hook_url = $request->get_param( 'hookUrl' );
		}

		if ( filter_var( $hook_url, FILTER_VALIDATE_URL ) ) {
			update_option( $this->_get_option_name(), $hook_url );

			$result = array(
				'id' => $this->_get_option_name(),
			);
		} else {
			$result = new WP_Error( 'td_invalid_hook_url', __( 'Invalid Hook URL', 'thrive-dash' ) );
		}

		return $result;
	}

	/**
	 * The endpoint where the Integration unsubscribes the webhook
	 *
	 * @return true
	 */
	public function unsubscribe() {

		/**
		 * Mind that if option does not exist false is return by delete_option()
		 */
		delete_option( $this->_get_option_name() );

		return true;
	}

	/**
	 * Required endpoint for creating the trigger in Zapier
	 * provide a sample of fields and data
	 *
	 * @return mixed|WP_REST_Response
	 */
	public function sample() {

		// For LG Subscription
		$response_sample = array(
			array(
				'name'              => 'Full name',
				'email'             => 'name@email.com',
				'phone'             => '1231231231',
				'ip_address'        => '192.168.1.1',
				'tags'              => array(
					'tag1',
					'tag2',
					'tag3',
				),
				'message'           => array(
					'message1',
					'message2',
					'message3',
				),
				'number'            => '123.45',
				'date'              => '24/09/2024',
				'website'           => 'https://yourwebsite.com/',
				'source_url'        => 'https://thrivethemes.com',
				'thriveleads_group' => 'Group 1',
				'thriveleads_type'  => 'Lightbox',
				'thriveleads_name'  => 'First Lightbox',
			),
		);

		// For CF Subscription [DEPRECATED should be removed when considering that old CF forms are no longer connected on the users side]
		if ( 'cf-optin' === $this->_hook_name ) {
			$response_sample = array(
				array(
					'first_name' => 'First name',
					'last_name'  => 'Last name',
					'full_name'  => 'Full name',
					'email'      => 'name@email.com',
					'message'    => 'Sample message',
					'phone'      => '1231231231',
					'website'    => 'https://yourwebsite.com/',
					'source_url' => 'https://thrivethemes.com',
					'ip_address' => '192.168.1.1',
					'tags'       => array( 'tag1', 'tag2', 'tag3' ),
				),
			);
		}

		return rest_ensure_response( $response_sample );
	}

	/**
	 * Required endpoint for creating the trigger in Zapier
	 * provide fields and data from specific form subscription
	 *
	 * @return mixed|WP_REST_Response
	 */
	public function specific_form_data() {
		$response_array = array();
		$form_id        = ! empty( $_GET['form_id'] ) ? sanitize_text_field( $_GET['form_id'] ) : '';

		if ( ! empty( $form_id ) ) {
			// get all fields using the form_id from postmeta table.
			global $wpdb;
			$query   = $wpdb->prepare(
				"SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '%s'",
				'_tve_lead_gen_form_'.$form_id
			);
			$results = $wpdb->get_results( $query, ARRAY_A );

			if ( ! empty( $results ) ) {
				foreach ( $results as $row ) {
					$meta_value = unserialize( $row['meta_value'] ) ?? [];
					$inputs     = $meta_value['inputs'] ?? [];

					// Format/Rename all the fields.
					$messages = array();
					$checkbox_count = 1;
					$file_url_count = 1;
					foreach ( $inputs as $input ) {
						if ( strpos( $input['id'], 'mapping_textarea_' ) === 0 ) {
							$messages[] = $input['label'];
						} elseif ( strpos( $input['id'], 'mapping_checkbox_' ) === 0 ) {
							$response_array[ 'checkbox_' . $checkbox_count ] = $input['label'];
							$checkbox_count++;
						} elseif ( strpos( $input['id'], 'mapping_file_' ) === 0 ) {
							$response_array[ 'file_url_' . $file_url_count ] = $input['label'];
							$file_url_count++;
						} else {
							$response_array[ $input['id'] ] = $input['label'];
						}
					}

					if ( ! empty( $messages ) ) {
						$response_array['message'] = $messages;
					}
				}
			}
		}

		// For LG Subscription.
		$response_sepcific_form_data = array(
			$response_array,
		);

		return rest_ensure_response( $response_sepcific_form_data );
	}

	/**
	 * Uses hook's prefix, name, suffix to establish the option name
	 * to save the webhook
	 *
	 * @return string
	 */
	protected function _get_option_name() {

		return $this->_hook_prefix . $this->_hook_name . $this->_hook_suffix;
	}


	/**
	 * Get list of all the LG forms in the site
	 *
	 * @return mixed|WP_REST_Response
	 */
	public function all_lg_forms() {

		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT wpm.meta_key, wpm.meta_value, wp.post_title
			FROM $wpdb->postmeta as wpm, $wpdb->posts as wp
			WHERE wpm.post_id = wp.ID AND wpm.meta_key LIKE '_tve_lead_gen_form\_%'
			ORDER BY wp.post_title"
		);

		$results = $wpdb->get_results( $query, ARRAY_A );
		$response_forms = [];

		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$meta_value = unserialize( $row['meta_value'] ) ?? [];
				$apis       = $meta_value['apis'] ?? [];
				if( ! empty( $apis ) && in_array( 'zapier', $apis, true ) ) {
					$response_forms[] = [
						'id'   => $row['meta_key'] ? str_replace( '_tve_lead_gen_form_', '', $row['meta_key'] ) : '',
						'name' => $row['post_title'] ?? '',
					];
				}

			}
		}

		return rest_ensure_response( $response_forms );
	}
}
