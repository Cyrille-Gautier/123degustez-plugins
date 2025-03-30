<?php

class TD_Notifications {
	private $_url;
	private $_connection;
	private $_request_params;

	public function __construct() {
		$this->_connection = TD_TTW_Connection::get_instance();
		$this->_request_params = [
			'user_id'              => $this->_connection->ttw_id,
			'user_email'           => $this->_connection->ttw_email,
			'last_notification_id' => get_option( 'td_last_notification_id' ),
		];

        $this->_url = TD_TTW_Connection::get_ttw_url() . '/wp-json/in-app-notification/v1/get-notifications';
	}

	public function handle_notifications() {
        if ( $this->is_transients_exist() ) {
            return;
        }

		$notifications = $this->get($this->_request_params);

		if ( ! $notifications || ! is_array( $notifications ) || is_wp_error( $notifications ) ) {
			return;
		}

		foreach ( $notifications as $notification ) {
			$this->add_notification( $notification );
		}
	}

	public function check_notification() {
		$this->handle_notifications();

		$notifications = $this->get_notification();
		return $notifications;
	}

	private function get_notification( $update_call=false ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'td_app_notifications';
		$current_time = current_time('Y-m-d H:i:s');

        if ( $this->is_transients_exist() && !$update_call ) {
            return get_transient('td_app_notifications_transients');
        }

		$active_notifications_query = $wpdb->prepare(
			"SELECT * FROM $table_name
			 WHERE start <= %s
		   		AND (end IS NULL OR end >= %s)
	        	AND dismissed = 0
			 ORDER BY start DESC",
			$current_time, $current_time
		);

		$active_notifications = $wpdb->get_results($active_notifications_query, ARRAY_A);

		$dismissed_notifications_query = $wpdb->prepare( " SELECT * 
											    FROM $table_name 
											    WHERE dismissed = 1
											    AND (end IS NULL OR end >= %s)
											    ORDER BY start DESC
											", $current_time );

		$dismissed_notifications = $wpdb->get_results($dismissed_notifications_query, ARRAY_A);

		if ( ! $active_notifications && ! $dismissed_notifications ) {
			return false;
		}

        $notifications = [
            'active'    => $active_notifications,
            'dismissed' => $dismissed_notifications
        ];

        if( !$update_call ) {
            $this->store_transients($notifications);
        }

		return $notifications;
	}

	private function add_notification( $notification ) {
		if ( ! $notification || ! is_array( $notification ) || empty( $notification ) ) {
			return false;
		}
		global $wpdb;
		$table_name = $wpdb->prefix . 'td_app_notifications';

		$last_notification_id = get_option( 'td_last_notification_id', 0 );

		if ( isset( $notification['btns'] ) ) {
			foreach ( $notification['btns'] as $key => $value ) {
				if ( $key === 'main' ) {
					$notification['button1_label']  = sanitize_text_field( $value['text'] );
					$notification['button1_action'] = esc_url_raw( $value['url'] );
				}
				if ( $key === 'alt' ) {
					$notification['button2_label']  = sanitize_text_field( $value['text'] );
					$notification['button2_action'] = esc_url_raw( $value['url'] );
				}
			}
		}

		if( isset( $notification['btns'] ) ) {
			unset( $notification['btns'] );
		}

		$additional_data = [
			'created'   => current_time( 'Y-m-d H:i:s' ),
			'updated'   => current_time( 'Y-m-d H:i:s' ),
		];

		$notification = array_merge($notification, $additional_data);

		if( isset( $notification['notification_id'] ) && $last_notification_id < $notification['notification_id'] ) {
			update_option( 'td_last_notification_id', $notification['notification_id'] );
		}

		$wpdb->insert( $table_name, $notification );
	}

	private function get($args = []) {
		$request_params = [
			'body'    => $args,
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'timeout' => 30,
			'sslverify' => false,
		];

		$notifications = wp_remote_get( $this->_url, $request_params );

		$response_code = wp_remote_retrieve_response_code( $notifications );

		if ( $response_code != 200 ) {
			return [];
		}

		if ( is_wp_error( $notifications ) ) {
			return false;
		}
		$notifications = json_decode( wp_remote_retrieve_body( $notifications ), true );

		if ( ! is_array( $notifications ) ) {
			return false;
		}

		return $notifications;
	}

	private function is_exist($notification_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'td_app_notifications';

		$notification = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE notification_id = %d", $notification_id ) );

		if ( $notification ) {
			return true;
		}

		return false;
	}

    private function is_transients_exist() {
        $transient_name = 'td_app_notifications_transients';
        $transient = get_transient( $transient_name );

        if ( $transient ) {
            return true;
        }

        return false;
    }

    private function store_transients( $notifications ) {
        if ( ! $notifications || ! is_array($notifications) || is_wp_error($notifications)) {
            return;
        }

        set_transient('td_app_notifications_transients', $notifications, 60 * 60 * 3);
    }

    public function update_transients() {
        $this->store_transients( $this->get_notification( true ) );
    }
}
