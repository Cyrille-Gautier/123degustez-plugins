<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

// Include WordPress libraries to handle XML-RPC
require_once ABSPATH . '/wp-includes/class-IXR.php';
require_once ABSPATH . '/wp-includes/class-wp-http-ixr-client.php';

class Thrive_Dash_Api_Infusionsoft {
	public $api_key;
	public $error = false;
	public $subdomain;

	public function __construct( $subdomain = null, $api_key = null ) {
		$this->subdomain = $subdomain;
		$this->api_key   = $api_key;
		if ( empty( $this->subdomain ) || empty( $this->api_key ) ) {
			throw new Thrive_Dash_Api_Infusionsoft_InfusionsoftException( "You must provide a ClientID and API key for your Keap (Infusionsoft) application." );
		}
	}

	public function __call( $name, $arguments ) {
		/* make sure the xml extension is enabled on the server - needed by WP IXR classes */
		if ( ! function_exists( 'xml_parser_create' ) ) {
			throw new Thrive_Dash_Api_Infusionsoft_InfusionsoftException( __( "PHP's XML extension is not available. Please contact your hosting provider to enable PHP's XML extension." ) );
		}

		// Make sure no error already exists
		if ( $this->error ) {
			throw new Thrive_Dash_Api_Infusionsoft_InfusionsoftException( __( 'You must provide a subdomain and API key for your Keap (Infusionsoft) application.', 'thrive-dash' ) );
		}

		// Get the full method name with the service and method
		$method    = ucfirst( $name ) . 'Service' . '.' . array_shift( $arguments );
		$arguments = array_merge( array( $method, $this->api_key ), $arguments );

		// Initialize the client
		$client = new WP_HTTP_IXR_Client( 'https://api.infusionsoft.com/crm/xmlrpc' );

		// Set the headers to support Legacy XML-RPC keys
		$args = array(
			'headers' => array(
				'X-Keap-API-Key' => $this->api_key,
				'Content-Type' => 'application/xml',
			),
		);
		$client->headers = $args['headers'];

		// Call the function and return any error that happens
		if ( ! call_user_func_array( array( $client, 'query' ), $arguments ) ) {
			throw new Thrive_Dash_Api_Infusionsoft_InfusionsoftException( $client->getErrorMessage() );
		}

		// Pass the response directly to the user
		return $client->getResponse();
	}
}
