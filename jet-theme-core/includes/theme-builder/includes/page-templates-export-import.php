<?php
namespace Jet_Theme_Core\Theme_Builder;
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Page_Templates_Export_Import {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Jet_Theme_Core
	 */
	private static $instance = null;

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Jet_Theme_Core
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @param $page_template_id
	 *
	 * @return string
	 */
	public function get_page_template_export_link( $page_template_id ) {
		return add_query_arg(
			[
				'action'           => 'jet_theme_core_export_page_template',
				'page_template_id' => $page_template_id,
				'nonce'            => wp_create_nonce( 'jet-theme-core-builder-nonce' ),
			],
			admin_url( 'admin-ajax.php' )
		);
	}

	/**
	 *
	 */
	public function export_page_template_action() {

		if ( ! isset( $_GET['action'] ) ) {
			return;
		}

		if ( 'jet_theme_core_export_page_template' !== $_GET['action'] && ! isset( $_GET['page_template_id'] ) ) {
			return;
		}

		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'jet-theme-core-builder-nonce' ) ) {
			wp_send_json_error( __( 'Page has expired. Please reload this page.', 'jet-theme-core' ) );
		}

		$this->export_page_template( $_GET['page_template_id'] );
	}

	/**
	 * [export_template description]
	 * @param  [type] $popup_id [description]
	 * @return [type]           [description]
	 */
	public function export_page_template( $page_template_id ) {
		$file_data = $this->prepare_page_template( $page_template_id );

		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: public' );
		header( 'Content-Description: File Transfer' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="'. $file_data['name'] . '"' );
		header( 'Content-Transfer-Encoding: binary' );

		session_write_close();

		// Output file data.
		echo $file_data['data'];

		die();
	}

	/**
	 * [prepare_popup_export description]
	 * @param  [type] $popup_id [description]
	 * @return [type]           [description]
	 */
	public function prepare_page_template( $page_template_id ) {

		$layout = get_post_meta( $page_template_id, '_layout', true );
		$conditions = get_post_meta( $page_template_id, '_conditions', true );
		$relation_type = get_post_meta( $page_template_id, '_relation_type', true );
		$type = get_post_meta( $page_template_id, '_type', true );
		$template_ids = [];
		$template_data_to_export = [];

		if ( ! empty( $layout ) ) {
			foreach ( $layout as $layout_name => $layout_data ) {
				if ( false !== $layout_data['id'] ) {
					$template_ids[] = $layout_data['id'];
				}
			}
		}

		if ( ! empty( $template_ids ) ) {
			$template_data_to_export = jet_theme_core()->templates->export_import_manager->prepare_template_data_to_export( $template_ids );
		}

		$export_data = [
			'version'          => JET_THEME_CORE_VERSION,
			'pageTemplateName' => get_the_title( $page_template_id ),
			'conditions'       => $conditions,
			'relationType'     => $relation_type,
			'layout'           => $layout,
			'type'             => $type,
			'templateList'     => $template_data_to_export['templateList'],
		];

		return [
			'name' => 'jet-page-template-' . $page_template_id . '-' . date( 'Y-m-d' ) . '.json',
			'data' => wp_json_encode( $export_data ),
		];
	}

	/**
	 * Process page template import
	 */
	public function process_import() {

		if ( ! current_user_can( 'import' ) ) {
			wp_send_json_error( __( 'You don\'t have permissions to do this', 'jet-theme-core' ) );
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'jet-theme-core-builder-nonce' ) ) {
			wp_send_json_error( __( 'Page has expired. Please reload this page.', 'jet-theme-core' ) );
		}

		if ( empty( $_FILES['_file'] ) ) {
			wp_send_json_error( __( 'File not passed', 'jet-theme-core' ) );
		}

		$file = $_FILES['_file'];

		if ( 'application/json' !== $file['type'] ) {
			wp_send_json_error( __( 'Format not allowed', 'jet-theme-core' ) );
		}

		$content = file_get_contents( $file['tmp_name'] );
		$content = json_decode( $content, true );

		if ( ! $content ) {
			wp_send_json_error( __( 'No data found in file', 'jet-theme-core' ) );
		}

		$template_name          = isset( $content[ 'pageTemplateName' ] ) ? $content[ 'pageTemplateName' ] : '';
		$template_conditions    = isset( $content[ 'conditions' ] ) ? $content[ 'conditions' ] : [];
		$template_relation_type = isset( $content[ 'relationType' ] ) ? $content[ 'relationType' ] : [];
		$template_layout        = isset( $content[ 'layout' ] ) ? $content[ 'layout' ] : [];
		$template_type          = isset( $content[ 'type' ] ) ? $content[ 'type' ] : [];
		$template_list          = isset( $content[ 'templateList' ] ) ? $content[ 'templateList' ] : [];

		if ( ! empty( $template_list ) ) {
			foreach ( $template_list as $templateData ) {
				$create_template_handler = jet_theme_core()->templates->export_import_manager->create_imported_template( $templateData );

				if ( 'success' === $create_template_handler['type'] ) {
					$body_type_map = apply_filters( 'jet-theme-core/templates-import/body-type-map', [ 'jet_page', 'jet_archive', 'jet_single' ] );

					if ( 'jet_header' === $templateData['type'] ) {
						$template_layout['header']['id'] = $create_template_handler['template_id'];
					}

					if ( in_array( $templateData['type'], $body_type_map ) ) {
						$template_layout['body']['id'] = $create_template_handler['template_id'];
					}

					if ( 'jet_footer' === $templateData['type'] ) {
						$template_layout['footer']['id'] = $create_template_handler['template_id'];
					}
				}
			}
		}

		$create_template_data = jet_theme_core()->theme_builder->page_templates_manager->create_page_template( $template_name, $template_conditions, $template_layout, $template_type );

		wp_send_json_success( [
			'newTemplateId'    => $create_template_data[ 'data' ][ 'newTemplateId' ],
			'pageTemplatesList'    => $create_template_data[ 'data' ][ 'list' ],
			'templatesList' => jet_theme_core()->templates->get_template_list(),
			'message'          => $create_template_data[ 'message' ]
		] );
	}

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'export_page_template_action' ] );
		add_action( 'wp_ajax_jet_theme_core_import_page_template', array( $this, 'process_import' ) );
	}

}
