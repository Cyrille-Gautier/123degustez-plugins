<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Thrive_Dash_List_Connection_KlickTipp extends Thrive_Dash_List_Connection_Abstract {
	/**
	 * Key used for mapping custom fields
	 *
	 * @var string
	 */
	protected $_key = '_field';

	/**
	 * Return the connection type
	 *
	 * @return String
	 */
	public static function get_type() {
		return 'autoresponder';
	}

	/**
	 * @return string the API connection title
	 */
	public function get_title() {
		return 'KlickTipp';
	}

	/**
	 * @return bool
	 */
	public function has_tags() {

		return true;
	}

	/**
	 * Define support for custom fields.
	 *
	 * @return boolean
	 */
	public function has_custom_fields() {
		return true;
	}

	public function push_tags( $tags, $data = array() ) {

		if ( ! $this->has_tags() && ( ! is_array( $tags ) || ! is_string( $tags ) ) ) {
			return $data;
		}

		$_key = $this->get_tags_key();

		if ( ! isset( $data[ $_key ] ) ) {
			$data[ $_key ] = array();
		}

		if ( isset( $data['klicktipp_tag'] ) ) {
			$data[ $_key ][] = $data['klicktipp_tag'];
		}

		$existing_tags = $this->getTags();
		/** @var Thrive_Dash_Api_KlickTipp $api */
		$api = $this->get_api();

		try {
			$api->login();
		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			return $this->error( sprintf( __( 'Could not connect to Klick Tipp using the provided data (%s)', 'thrive-dash' ), $e->getMessage() ) );
		}

		foreach ( $tags as $key => $tag ) {

			$tag = trim( $tag );

			if ( empty( $tags ) ) {
				continue;
			}

			if ( ! in_array( $tag, $existing_tags ) ) {
				try {
					$data[ $_key ][] = (int) $api->createTag( $tag );
				} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
					$this->error = $e->getMessage();
				}
			} else {
				$data[ $_key ][] = array_search( $tag, $existing_tags );
			}
		}

		return $data;
	}

	/**
	 * output the setup form html
	 *
	 * @return void
	 */
	public function output_setup_form() {
		$this->output_controls_html( 'klicktipp' );
	}

	/**
	 * should handle: read data from post / get, test connection and save the details
	 *
	 * on error, it should register an error message (and redirect?)
	 *
	 * @return mixed
	 */
	public function read_credentials() {
		$user     = ! empty( $_POST['connection']['kt_user'] ) ? sanitize_text_field( $_POST['connection']['kt_user'] ) : '';
		$password = ! empty( $_POST['connection']['kt_password'] ) ? sanitize_text_field( $_POST['connection']['kt_password'] ) : '';

		if ( empty( $user ) || empty( $password ) ) {
			return $this->error( __( 'Email and password are required', 'thrive-dash' ) );
		}

		$this->set_credentials(
			array(
				'user'     => $user,
				'password' => $password,
			)
		);

		/** @var Thrive_Dash_Api_KlickTipp $api */
		$api = $this->get_api();

		try {
			$api->login();

			$result = $this->test_connection();

			if ( $result !== true ) {
				return $this->error( sprintf( __( 'Could not connect to Klick Tipp using the provided data: %s', 'thrive-dash' ), $this->_error ) );
			}

			/**
			 * finally, save the connection details
			 */
			$this->save();

			return $this->success( __( 'Klick Tipp connected successfully!', 'thrive-dash' ) );

		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			return $this->error( sprintf( __( 'Could not connect to Klick Tipp using the provided data (%s)', 'thrive-dash' ), $e->getMessage() ) );
		}
	}

	/**
	 * test if a connection can be made to the service using the stored credentials
	 *
	 * @return bool|string true for success or error message for failure
	 */
	public function test_connection() {
		return is_array( $this->_get_lists() );
	}

	/**
	 * instantiate the API code required for this connection
	 *
	 * @return mixed
	 */
	protected function get_api_instance() {
		return new Thrive_Dash_Api_KlickTipp( $this->param( 'user' ), $this->param( 'password' ) );
	}

	/**
	 * get all Subscriber Lists from this API service
	 *
	 * @return array|bool for error
	 */
	protected function _get_lists() {
		/** @var Thrive_Dash_Api_KlickTipp $api */
		$api = $this->get_api();

		try {
			$api->login();
		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			return $this->error( sprintf( __( 'Could not connect to Klick Tipp using the provided data (%s)', 'thrive-dash' ), $e->getMessage() ) );
		}

		try {
			$all = $api->getLists();

			$lists = array();
			foreach ( $all as $id => $name ) {
				if ( ! empty( $name ) ) {
					$lists[] = array(
						'id'   => $id,
						'name' => $name,
					);
				}
			}

			return $lists;
		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			$this->_error = $e->getMessage();

			return false;
		}
	}

	/**
	 * Subscribe an email. Requires to be logged in.
	 *
	 * @param mixed $list_identifier The id subscription process.
	 * @param mixed $arguments       (optional) Additional fields of the subscriber.
	 *
	 * @return An object representing the Klicktipp subscriber object.
	 */
	public function add_subscriber( $list_identifier, $arguments ) {
		/** @var Thrive_Dash_Api_KlickTipp $api */
		$api = $this->get_api();

		try {
			$api->login();
		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			return $this->error( sprintf( __( 'Could not connect to Klick Tipp using the provided data (%s)', 'thrive-dash' ), $e->getMessage() ) );
		}

		// Handle tags - get or create tag IDs BEFORE contact creation.
		if ( ! empty( $arguments['klicktipp_tags'] ) ) {
			$tag_names = explode( ',', trim( $arguments['klicktipp_tags'], ' ,' ) );
			$tag_names = array_map( 'trim', $tag_names );
			$tag_names = array_filter( $tag_names ); // Remove empty tags.

			// Get or create tag IDs.
			$tag_ids = $this->get_or_create_tag_ids( $tag_names ) ?? [];
		}

		// Prepare default fields
		if ( ! empty( $arguments['name'] ) ) {
			list( $first_name, $last_name ) = $this->get_name_parts( $arguments['name'] );
		}

		$fields = array();

		if ( ! empty( $first_name ) ) {
			$fields['fieldFirstName'] = $first_name;
		}

		if ( ! empty( $last_name ) ) {
			$fields['fieldLastName'] = $last_name;
		}

		// Add phone
		if ( ! empty( $arguments['phone'] ) ) {
			$fields['fieldPhone'] = sanitize_text_field( $arguments['phone'] );
		}

		// Handle custom fields mapping
		if ( ! empty( $arguments['tve_mapping'] ) ) {
			$custom_fields = $this->generateCustomFields( $arguments );
			$fields        = array_merge( $fields, $custom_fields );
		} elseif ( ! empty( $arguments['automator_custom_fields'] ) ) {
			$fields = array_merge( $fields, $arguments['automator_custom_fields'] );
		}

		try {
			$api->subscribe(
				$arguments['email'],
				$list_identifier,
				$tag_ids,
				! empty( $fields ) ? $fields : ''
			);

			// Tag user by email, array tags.
			if ( ! empty( $tag_ids ) ) {
				$api->tagByEmail( $arguments['email'], $tag_ids );
			}

			/**
			 * get redirect url if needed
			 */
			$return = true;
			if ( isset( $_POST['_submit_option'] ) && $_POST['_submit_option'] == 'klicktipp-redirect' ) {
				$return = $api->subscription_process_redirect( $list_identifier, $arguments['email'] );
			}

			$api->logout();

			return $return;
		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			return $e->getMessage();
		}
	}


	/**
	 * Get or create tag IDs.
	 *
	 * @param [type] $tag_names Tag names.
	 * @return array
	 */
	private function get_or_create_tag_ids( $tag_names ) {
		$tag_ids = array();

		try {
			// Get all existing tags first - GET /contact_tags.
			$existing_tags = $this->getTags();
			$tag_map       = array();

			// Create a map of tag name => tag_id (case-insensitive).
			if ( is_array( $existing_tags ) && ! empty( $existing_tags ) ) {
				foreach ( $existing_tags as $tag_id => $tag_name ) {
					if ( isset( $tag_name ) && isset( $tag_id ) ) {
						$tag_map[ strtolower( trim( $tag_name ) ) ] = $tag_id;
					}
				}
			}

			// Process each tag name.
			foreach ( $tag_names as $tag_name ) {
				$tag_key = strtolower( trim( $tag_name ) );

				// Check if tag already exists.
				if ( isset( $tag_map[ $tag_key ] ) ) {
					$tag_ids[] = $tag_map[ $tag_key ];
				} else {
					// Create new tag and get its ID.
					$new_tag_id = $this->create_new_tag( $tag_name );
					if ( $new_tag_id ) {
						$tag_ids[] = $new_tag_id;
					}
				}
			}
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'KlickTipp: Failed to get or create tag IDs - ' . $e->getMessage() );
			}
		}

		return $tag_ids;
	}


	/**
	 * Create a new tag using POST /contact_tags.
	 *
	 * @param string $tag_name Tag name.
	 * @return int|null Tag ID.
	 */
	private function create_new_tag( $tag_name ) {
		try {
			$tag_data = trim( $tag_name );

			$result = $this->get_api()->createTag( $tag_data );

			if ( isset( $result ) && is_numeric( $result ) ) {
				return $result;
			}
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'KlickTipp: Failed to create tag "' . $tag_name . '" - ' . $e->getMessage() );
			}
		}

		return false;
	}

	/**
	 * Gets a list of tags through GET /tag API
	 *
	 * @return array
	 */
	public function getTags() {
		$tags = array();

		try {
			/** @var Thrive_Dash_Api_KlickTipp $api */
			$api  = $this->get_api();
			$tags = $api->getTags();
		} catch ( Exception $e ) {

		}

		return $tags;
	}

	/**
	 * output any (possible) extra editor settings for this API
	 *
	 * @param array $params allow various different calls to this method
	 */
	public function get_extra_settings( $params = array() ) {
		$params['tags'] = $this->getTags();
		if ( ! is_array( $params['tags'] ) ) {
			$params['tags'] = array();
		}

		return $params;
	}

	/**
	 * output any (possible) extra editor settings for this API
	 *
	 * @param array $params allow various different calls to this method
	 */
	public function render_extra_editor_settings( $params = array() ) {
		$params['tags'] = $this->getTags();
		if ( ! is_array( $params['tags'] ) ) {
			$params['tags'] = array();
		}
		$this->output_controls_html( 'klicktipp/tags', $params );
	}

	/**
	 * Return the connection email merge tag
	 *
	 * @return String
	 */
	public static function get_email_merge_tag() {
		return '%Subscriber:EmailAddress%';
	}


	public function get_automator_add_autoresponder_mapping_fields() {
		return array( 'autoresponder' => array( 'mailing_list', 'api_fields', 'tag_select' ) );
	}

	public function get_automator_tag_autoresponder_mapping_fields() {
		return array( 'autoresponder' => array( 'tag_select' ) );
	}

	/**
	 * Get API custom fields
	 *
	 * @param mixed $params
	 * @param bool  $force
	 * @param bool  $get_all
	 *
	 * @return array
	 */
	public function get_api_custom_fields( $params, $force = false, $get_all = false ) {
		// Serve from cache if exists and requested
		$cached_data = $this->get_cached_custom_fields();
		if ( false === $force && ! empty( $cached_data ) ) {
			return $cached_data;
		}

		$custom_data = array();

		try {
			/** @var Thrive_Dash_Api_KlickTipp $api */
			$api = $this->get_api();
			$api->login();

			$custom_fields = $api->getCustomFields();

			if ( is_array( $custom_fields ) ) {
				foreach ( $custom_fields as $field ) {
					$custom_data[] = $this->normalize_custom_field( $field );
				}
			}

			$this->_save_custom_fields( $custom_data );

		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'KlickTipp: Failed to get custom fields - ' . $e->getMessage() );
			}
		}

		return $custom_data;
	}

	/**
	 * Normalize custom field data
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	protected function normalize_custom_field( $field ) {
		$field = (array) $field;

		return array(
			'id'    => ! empty( $field['id'] ) ? $field['id'] : '',
			'name'  => ! empty( $field['name'] ) ? $field['name'] : '',
			'type'  => $field['type'],
			'label' => ! empty( $field['label'] ) ? $field['label'] : ( ! empty( $field['name'] ) ? $field['name'] : '' ),
		);
	}

	/**
	 * Get available custom fields for this API connection
	 *
	 * @param null $list_id
	 *
	 * @return array
	 */
	public function get_available_custom_fields( $list_id = null ) {
		return $this->get_api_custom_fields( null, true );
	}

	/**
	 * Append custom fields to defaults
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function get_custom_fields( $params = array() ) {
		return array_merge( parent::get_custom_fields(), $this->_mapped_custom_fields );
	}

	/**
	 * Build mapped custom fields array based on form params
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function buildMappedCustomFields( $args ) {
		$mapped_data = array();

		// Should be always base_64 encoded of a serialized array
		if ( empty( $args['tve_mapping'] ) || ! tve_dash_is_bas64_encoded( $args['tve_mapping'] ) || ! is_serialized( base64_decode( $args['tve_mapping'] ) ) ) {
			return $mapped_data;
		}

		$form_data = thrive_safe_unserialize( base64_decode( $args['tve_mapping'] ) );

		$mapped_fields = $this->get_mapped_field_ids();

		foreach ( $mapped_fields as $mapped_field_name ) {
			// Extract an array with all custom fields (siblings) names from form data
			// {ex: [mapping_url_0, .. mapping_url_n] / [mapping_text_0, .. mapping_text_n]}
			$cf_form_fields = preg_grep( "#^{$mapped_field_name}#i", array_keys( $form_data ) );

			// Matched "form data" for current allowed name
			if ( ! empty( $cf_form_fields ) && is_array( $cf_form_fields ) ) {
				// Pull form allowed data, sanitize it and build the custom fields array
				foreach ( $cf_form_fields as $cf_form_name ) {
					if ( empty( $form_data[ $cf_form_name ][ $this->_key ] ) ) {
						continue;
					}

					$field_id = str_replace( $mapped_field_name . '_', '', $cf_form_name );

					$mapped_data[ $field_id ] = array(
						'type'  => $mapped_field_name,
						'value' => $form_data[ $cf_form_name ][ $this->_key ],
					);
				}
			}
		}

		return $mapped_data;
	}

	/**
	 * Generate custom fields array for API submission
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function generateCustomFields( $args ) {
		$custom_fields = $this->get_api_custom_fields( array() );

		$ids = $this->buildMappedCustomFields( $args );

		$result = array();
		$processed_fields = 0;
		$skipped_fields = 0;

		foreach ( $ids as $key => $id ) {
			// Find the matching field in API custom fields
			$field = array_filter(
				$custom_fields,
				function ( $item ) use ( $id ) {
					return $item['id'] === $id['value'];
				}
			);
			$field = array_values( $field );

			if ( ! isset( $field[0] ) ) {
				$skipped_fields++;
				continue;
			}

			$api_field_id = $field[0]['id'];

			// Try multiple possible field names in args.
			$possible_field_names = array();

			// Method 1: Use the actual API field ID directly.
			$possible_field_names[] = $api_field_id;

			// Method 2: Use the mapping key-based name (original logic).
			if ( strpos( $id['type'], 'mapping_' ) !== false ) {
				$possible_field_names[] = $id['type'] . '_' . $key;
			} else {
				// For non-mapping fields, try both the key and the type_key pattern.
				$possible_field_names[] = $key;
				$possible_field_names[] = $id['type'] . '_' . $key;  // This is the missing piece!
			}

			// Method 3: Use field name if different from ID.
			if ( ! empty( $field[0]['name'] ) && $field[0]['name'] !== $api_field_id ) {
				$possible_field_names[] = $field[0]['name'];
			}

			// Method 4: Use field label if different from name and ID.
			if ( ! empty( $field[0]['label'] ) &&
				$field[0]['label'] !== $api_field_id &&
				$field[0]['label'] !== $field[0]['name'] ) {
				$possible_field_names[] = $field[0]['label'];
			}

			$found_field_value = null;

			// Find the first matching field value without nested loop.
			$clean_field_names = array_map(
				function ( $name ) {
					return str_replace( '[]', '', $name );
				},
				$possible_field_names
			);
			$matching_fields   = array_filter(
				$clean_field_names,
				function ( $clean_name ) use ( $args ) {
					return isset( $args[ $clean_name ] ) && ! empty( $args[ $clean_name ] );
				}
			);

			if ( ! empty( $matching_fields ) ) {
				$first_matching_field = reset( $matching_fields );
				$found_field_value    = $args[ $first_matching_field ];
			}

			if ( null !== $found_field_value ) {
				// Convert date fields to unix timestamp.
				if ( 'date' === $id['type'] && ! empty( $found_field_value ) ) {
					$timestamp = $this->convertDateToTimestamp( $found_field_value );
					if ( false !== $timestamp ) {
						$found_field_value = $timestamp;
					} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log( "WARNING: Failed to convert date '$found_field_value' to timestamp" );
					}
				}

				$processed_value         = $this->process_field( $found_field_value );
				$result[ $api_field_id ] = $processed_value;

				++$processed_fields;
			} else {
				++$skipped_fields;
			}
		}

		return $result;
	}

	/**
	 * Convert various date formats to unix timestamp
	 *
	 * @param string $date_string The date string to convert
	 *
	 * @return int|false Unix timestamp or false on failure
	 */
	private function convertDateToTimestamp( $date_string ) {
		if ( empty( $date_string ) ) {
			return false;
		}

		// Clean up the date string.
		$date_string = trim( $date_string );

		// List of possible date formats to try.
		$formats = array(
			'd/m/Y',     // 29/09/2025 (European format)
			'd/m/y',     // 29/09/25
			'm/d/Y',     // 09/29/2025 (US format)
			'm/d/y',     // 09/29/25
			'Y-m-d',     // 2025-09-29 (ISO format)
			'd-m-Y',     // 29-09-2025
			'm-d-Y',     // 09-29-2025
			'd.m.Y',     // 29.09.2025 (German format)
			'm.d.Y',     // 09.29.2025
		);

		// Try each format.
		foreach ( $formats as $format ) {
			$date = DateTime::createFromFormat( $format, $date_string );
			if ( false !== $date ) {
				$timestamp = $date->getTimestamp();
				return $timestamp;
			}
		}

		// Fallback to strtotime() for other formats.
		$timestamp = strtotime( $date_string );
		if ( false !== $timestamp ) {
			return $timestamp;
		}

		return false;
	}

	/**
	 * Build custom fields mapping for automations.
	 *
	 * @param array $automation_data Automation data.
	 *
	 * @return array
	 */
	public function build_automation_custom_fields( $automation_data ) {
		$mapped_data = array();
		$fields      = $this->get_api_custom_fields( array() );

		if ( ! empty( $automation_data['api_fields'] ) && is_array( $automation_data['api_fields'] ) ) {
			foreach ( $automation_data['api_fields'] as $pair ) {
				foreach ( $fields as $field ) {
					if ( $field['id'] === $pair['key'] ) {
						$value = sanitize_text_field( $pair['value'] );
						if ( $value ) {
							$mapped_data[ $field['id'] ] = $value;
						}
					}
				}
			}
		}

		return $mapped_data;
	}

	/**
	 * Add custom fields to subscriber
	 *
	 * @param string $email Email.
	 * @param array  $custom_fields Custom fields.
	 * @param array  $extra Extra.
	 *
	 * @return bool|mixed
	 */
	public function add_custom_fields( $email, $custom_fields = array(), $extra = array() ) {
		if ( empty( $email ) || empty( $custom_fields ) ) {
			return false;
		}

		/** @var Thrive_Dash_Api_KlickTipp $api */
		$api = $this->get_api();

		try {
			$api->login();

			// Custom fields in KlickTipp are handled during subscription
			// This method could be used for future API enhancements
			return true;
		} catch ( Thrive_Dash_Api_KlickTipp_Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'KlickTipp: Failed to add custom fields - ' . $e->getMessage() );
			}
			return false;
		}
	}
}
