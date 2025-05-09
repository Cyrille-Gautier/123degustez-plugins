<?php // phpcs:ignore
/**
 * Snapshot controllers: S3 Destination AJAX controller class
 *
 * @package snapshot
 */

namespace WPMUDEV\Snapshot4\Controller\Ajax\Destination;

use WPMUDEV\Snapshot4\Controller;
use WPMUDEV\Snapshot4\Task;
use WPMUDEV\Snapshot4\Model;
use WPMUDEV\Snapshot4\Helper;
use WPMUDEV\Snapshot4\Helper\Log;

/**
 * S3 Destination AJAX controller class
 */
class S3 extends Controller\Ajax\Destination {

	/**
	 * Boots the controller and sets up event listeners.
	 */
	public function boot() {
		if ( ! is_admin() ) {
			return false;
		}

		// Request the service actions regarding s3 destinations.
		add_action( 'wp_ajax_snapshot-s3_connection', array( $this, 'json_s3_connection' ) );
	}

	/**
	 * Handles requesting the service for testing a destination's config.
	 */
	public function json_s3_connection() {
		$this->do_request_sanity_check( 'snapshot_s3_connection', self::TYPE_POST );

		$data = array(
			'tpd_accesskey'  => isset( $_POST['tpd_accesskey'] ) ? $_POST['tpd_accesskey'] : null, // phpcs:ignore
			'tpd_secretkey'  => isset( $_POST['tpd_secretkey'] ) ? $_POST['tpd_secretkey'] : null, // phpcs:ignore
			'tpd_bucketname' => isset( $_POST['tpd_bucketname'] ) ? $_POST['tpd_bucketname'] : null, // phpcs:ignore
			'tpd_region'     => isset( $_POST['tpd_region'] ) ? $_POST['tpd_region'] : null, // phpcs:ignore
			'tpd_action'     => isset( $_POST['tpd_action'] ) ? $_POST['tpd_action'] : null, // phpcs:ignore
			'tpd_path'       => isset( $_POST['tpd_path'] ) ? $_POST['tpd_path'] : null, // phpcs:ignore
			'tpd_name'       => isset( $_POST['tpd_name'] ) ? $_POST['tpd_name'] : null, // phpcs:ignore
			'tpd_type'       => isset( $_POST['tpd_type'] ) ? $_POST['tpd_type'] : null, // phpcs:ignore
			'tpd_limit'      => isset( $_POST['tpd_limit'] ) ? intval( $_POST['tpd_limit'] ) : null, // phpcs:ignore
			'tpd_save'       => isset( $_POST['tpd_save'] ) ? intval( $_POST['tpd_save'] ) : null, // phpcs:ignore
		);

		if ( 'aws' === $data['tpd_type'] ) {
			$data['tpd_bucket'] = isset( $_POST['tpd_bucket'] ) ? $_POST['tpd_bucket'] : null; // phpcs:ignore
		}

		if ( 's3_other' === $data['tpd_type'] ) {
			$data['tpd_endpoint'] = isset( $_POST['tpd_endpoint'] ) ? $_POST['tpd_endpoint'] : null; // phpcs:ignore
		}

		$task = new Task\Request\Destination\S3( $data['tpd_action'], $data['tpd_type'] );

		$validated_data = $task->validate_request_data( $data );
		if ( is_wp_error( $validated_data ) ) {
			wp_send_json_error( $validated_data );
		}

		$args                  = $validated_data;
		$args['request_model'] = new Model\Request\Destination\S3();
		$result                = $task->apply( $args );

		if ( $task->has_errors() ) {
			foreach ( $task->get_errors() as $error ) {
				Log::error( $error->get_error_message() );
			}
			wp_send_json_error(
				array(
					'api_response' => $result,
				)
			);
		}

		wp_send_json_success(
			array(
				'api_response' => $result,
			)
		);
	}
}