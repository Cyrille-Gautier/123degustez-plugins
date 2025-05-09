<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Thrive_Dash_Api_Drip {
	private $api_token = '';
	private $api_end_point = 'https://api.getdrip.com/v2/';

	const GET = 1;
	const POST = 2;
	const DELETE = 3;
	const PUT = 4;

	/**
	 * Accepts the token and saves it internally.
	 *
	 * @param string $api_token
	 *
	 * @throws Exception
	 */
	public function __construct( $api_token ) {
		$api_token = trim( $api_token );

		if ( empty( $api_token ) || ! preg_match( '#^[\w-]+$#si', $api_token ) ) {
			throw new Exception( 'Missing or invalid Drip API token.' );
		}

		$this->api_token = $api_token;
	}

	/**
	 * Requests the campaigns for the given account.
	 *
	 * @param $params
	 *
	 * @return array|bool
	 * @throws Exception
	 * @throws Thrive_Dash_Api_Drip_Exception
	 * @throws Thrive_Dash_Api_Drip_Exception_Unsubscribed
	 */
	public function get_campaigns( $params ) {
		if ( empty( $params['account_id'] ) ) {
			throw new Exception( 'Account ID not specified' );
		}

		$account_id = $params['account_id'];
		unset( $params['account_id'] ); // clear it from the params

		if ( isset( $params['status'] ) ) {
			if ( ! in_array( $params['status'], array( 'active', 'draft', 'paused', 'all' ) ) ) {
				throw new Exception( 'Invalid campaign status.' );
			}
		} elseif ( 0 ) {
			$params['status'] = 'active'; // api defaults to all but we want active ones
		}

		$url = $this->api_end_point . "$account_id/campaigns";
		$res = $this->make_request( $url, $params );

		return empty( $res ) ? false : $res;
	}

	/**
	 * Requests the accounts for the given account.
	 *
	 * @param void
	 *
	 * @return bool/array
	 */
	public function get_accounts() {
		$url = $this->api_end_point . 'accounts';
		$res = $this->make_request( $url );

		return empty( $res ) ? false : $res;
	}

	/**
	 * Sends a request to add a subscriber and returns its record or false
	 *
	 * @param $params
	 *
	 * @return array|bool
	 * @throws Exception
	 * @throws Thrive_Dash_Api_Drip_Exception
	 * @throws Thrive_Dash_Api_Drip_Exception_Unsubscribed
	 */
	public function create_or_update_subscriber( $params ) {
		if ( empty( $params['account_id'] ) ) {
			throw new Exception( 'Account ID not specified' );
		}

		$account_id = $params['account_id'];
		unset( $params['account_id'] ); // clear it from the params

		$api_action = "/$account_id/subscribers";
		$url        = $this->api_end_point . $api_action;

		// The API wants the params to be JSON encoded
		$req_params = array( 'subscribers' => array( $params ) );

		$res = $this->make_request( $url, $req_params, static::POST );

		return empty( $res ) ? false : $res;
	}

	/**
	 * Does a request to Drip for custom fields added by the user
	 *
	 * @param $params
	 *
	 * @return array|false
	 * @throws Exception
	 */
	public function get_custom_fields( $params ) {

		if ( empty( $params['account_id'] ) ) {
			return false;
		}

		$account_id = $params['account_id'];
		unset( $params['account_id'] ); // clear it from the params

		$api_action = "/$account_id/custom_field_identifiers";
		$url        = $this->api_end_point . $api_action;

		try {
			$res = $this->make_request( $url );
		} catch ( Exception $e ) {
			return false;
		}

		return empty( $res ) ? false : $res['custom_field_identifiers'];
	}


	public function delete_subscriber( $params ) {
		if ( empty( $params['account_id'] ) ) {
			throw new Exception( 'Account ID not specified' );
		}

		$account_id = $params['account_id'];
		unset( $params['account_id'] ); // clear it from the params

		$api_action = "$account_id/subscribers/" . $params['email'] . '/remove';
		$url        = $this->api_end_point . $api_action;

		// The API wants the params to be JSON encoded
		$req_params = array( 'subscribers' => array( $params ) );

		return $this->make_request( $url, $req_params, static::POST );
	}

	/**
	 * Subscribes a user to a given campaign for a given account.
	 *
	 * @param array $params
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function subscribe_subscriber( $params ) {
		if ( empty( $params['account_id'] ) ) {
			throw new Exception( 'Account ID not specified' );
		}

		$account_id = $params['account_id'];
		unset( $params['account_id'] ); // clear it from the params

		$campaign_id = $params['campaign_id'] ?: ''; // campaign_id isn't really required
		unset( $params['campaign_id'] ); // clear it from the params

		if ( empty( $params['email'] ) ) {
			throw new Exception( 'Email not specified' );
		}

		if ( ! isset( $params['double_optin'] ) ) {
			$params['double_optin'] = true;
		}

		if ( ! isset( $params['reactivate_if_removed'] ) ) {
			$params['reactivate_if_removed'] = true;
		}

		$api_action = "$account_id/campaigns/$campaign_id/subscribers";
		$url        = $this->api_end_point . $api_action;

		// The API wants the params to be JSON encoded
		$req_params = array( 'subscribers' => array( $params ) );

		$res = $this->make_request( $url, $req_params, static::POST );

		return empty( $res ) ? false : $res;
	}

	/**
	 * Posts an event specified by the user.
	 *
	 * @param array $params
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function record_event( $params ) {
		if ( empty( $params['account_id'] ) ) {
			throw new Exception( 'Account ID not specified' );
		}

		if ( empty( $params['action'] ) ) {
			throw new Exception( 'Action was not specified' );
		}

		$account_id = $params['account_id'];
		unset( $params['account_id'] ); // clear it from the params

		$api_action = "$account_id/events";
		$url        = $this->api_end_point . $api_action;

		// The API wants the params to be JSON encoded
		$req_params = array( 'events' => array( $params ) );
		$res        = $this->make_request( $url, $req_params, static::POST );

		return empty( $res ) ? false : $res;
	}

	/**
	 * @param       $url
	 * @param array $params
	 * @param int   $req_method
	 *
	 * @return array
	 * @throws Thrive_Dash_Api_Drip_Exception
	 * @throws Thrive_Dash_Api_Drip_Exception_Unsubscribed
	 */
	public function make_request( $url, $params = array(), $req_method = self::GET ) {

		switch ( $req_method ) {
			case static::DELETE:
				$fn             = 'tve_dash_api_remote_post';
				$args['method'] = 'delete';
				break;
			case static::GET:
				$fn = 'tve_dash_api_remote_get';
				break;
			default:
				$params = json_encode( $params );
				$fn     = 'tve_dash_api_remote_post';
		}

		$args = array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( $this->api_token . ':' ),
			),
			'body'    => $params,
		);

		$result = $fn( $url, $args );

		if ( $result instanceof WP_Error ) {
			throw new Thrive_Dash_Api_Drip_Exception( $result->get_error_message() );
		}

		$http_code = $result['response']['code'];
		$body      = json_decode( $result['body'], true );

		if ( $http_code == '422' ) {
			throw new Thrive_Dash_Api_Drip_Exception_Unsubscribed( 'API call failed. Server returned status code ' . $http_code . ' with message: <b>' . $body['errors'][0]['message'] . '</b>' );
		}

		if ( ! ( $http_code == '200' || $http_code == '201' || $http_code == '204' ) ) {
			throw new Thrive_Dash_Api_Drip_Exception( 'API call failed. Server returned status code ' . $http_code . ' with message: <b>' . $result['response']['message'] . '</b>' );
		}

		return $body;
	}

	/**
	 * Apply single tag [multiple tagging not permitted by the API]
	 *
	 * @param $email
	 * @param $tag
	 * @param $account_id
	 *
	 * @return array|bool
	 * @throws Exception
	 * @throws Thrive_Dash_Api_Drip_Exception
	 * @throws Thrive_Dash_Api_Drip_Exception_Unsubscribed
	 */
	public function apply_tag( $email, $tag, $account_id ) {
		if ( empty( $email ) || empty( $tag ) || empty( $account_id ) ) {
			throw new Exception( 'Tags error: missing required argument' );
		}

		$api_action = "/$account_id/tags";
		$url        = $this->api_end_point . $api_action;

		$params = array(
			'email' => $email,
			'tag'   => $tag,
		);

		// The API wants the params to be JSON encoded
		$req_params = array( 'tags' => array( $params ) );

		$res = $this->make_request( $url, $req_params, static::POST );

		return empty( $res ) ? false : $res;
	}
}
