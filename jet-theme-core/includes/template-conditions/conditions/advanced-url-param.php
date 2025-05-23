<?php
namespace Jet_Theme_Core\Template_Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Url_Param extends Base {

	/**
	 * Condition slug
	 *
	 * @return string
	 */
	public function get_id() {
		return 'url-param';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'URL Parameter', 'jet-theme-core' );
	}

	/**
	 * Condition group
	 *
	 * @return string
	 */
	public function get_group() {
		return 'advanced';
	}

	/**
	 * @return int
	 */
	public  function get_priority() {
		return 95;
	}

	/**
	 * @return string
	 */
	public function get_body_structure() {
		return 'jet_page';
	}

	/**
	 * [get_control description]
	 * @return [type] [description]
	 */
	public function get_control() {
		return [
			'type'        => 'input',
			'placeholder' => __( 'Input params string', 'jet-theme-core' ),
		];
	}

	/**
	 * [get_label_by_value description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_label_by_value( $value = '' ) {
		return $value;
	}

	/**
	 * Condition check callback
	 *
	 * @return bool
	 */
	public function check( $arg = '' ) {

		if ( empty( $arg ) || empty( $_REQUEST ) ) {
			return false;
		}

		$params_data = [];

		if ( strpos( $arg, '&' ) ) {
			$param_list = explode( '&', $arg );

			if ( ! empty( $param_list ) && is_array( $param_list ) ) {
				foreach ( $param_list as $key => $value ) {
					$param_pair = $this->explode_to_pairs( $value );

					if ( $param_pair ) {
						$params_data[ $param_pair[0] ] = $param_pair[1];
					}
				}
			}
		} else {
			$param_pair = $this->explode_to_pairs( $arg );

			if ( $param_pair ) {
				$params_data[ $param_pair[0] ] = $param_pair[1];
			}
		}

		$request_data = $_REQUEST;

		$is_match = false;

		foreach ( $request_data as $request_param => $request_value ) {

			if ( array_key_exists( $request_param, $params_data ) && $request_value === $params_data[ $request_param ] ) {
				$is_match = true;

				break;
			}
		}

		return $is_match;
	}

	/**
	 * [explode_to_pairs description]
	 * @return [type] [description]
	 */
	public function explode_to_pairs( $string = false ) {

		if ( ! $string || empty( $string ) ) {
			return false;
		}

		if ( ! strpos( $string, '=' ) ) {
			return false;
		}

		return explode( '=', $string );

	}

}
