<?php
namespace Jet_Elements\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Elementor_Template extends Base {

	/**
	 * [$depended_scripts description]
	 * @var array
	 */
	public $depended_scripts = [];

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'elementor-template';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'id' => array(
				'default'    => '',
				'required'   => false,
			),
			'dev' => array(
				'default'    => 'false',
				'required'   => false,
			),
            'signature' => array(
                'default'  => '',
                'required' => true,
            ),
		);
	}

	public function callback( $request ) {

		$args = $request->get_params();

		$template_id = ! empty( $args['id'] ) ? $args['id'] : false;

		$dev = filter_var( $args['dev'], FILTER_VALIDATE_BOOLEAN ) ? true : false;

		if ( ! $template_id ) {
			return false;
		}

		$transient_key = md5( sprintf( 'jet_elements_elementor_template_data_%s', $template_id ) );

		$template_data = get_transient( $transient_key );

		if ( ! empty( $template_data ) && !$dev ) {
			return rest_ensure_response( $template_data );
		}

		$plugin = \Elementor\Plugin::instance();

		$content = '';

		$template_scripts = [];
		$template_styles = [];

		$fonts_link = $this->get_elementor_template_fonts_url( $template_id );

		if ( $fonts_link ) {
			$template_styles[ 'jet-elements-google-fonts-css-' . $template_id ] = $fonts_link;
		}

		$plugin->frontend->register_scripts();

		$content .= $plugin->frontend->get_builder_content( $template_id, true );

		$this->get_elementor_template_scripts( $template_id );

		$script_depends = array_unique( $this->depended_scripts );

		foreach ( $script_depends as $script ) {
			$template_scripts[ $script ] = $this->get_script_uri_by_handler( $script );
		}

		$template_data = [
			'template_content' => $content,
			'template_scripts' => $template_scripts,
			'template_styles'  => $template_styles,
		];

		set_transient( $transient_key, $template_data, 12 * HOUR_IN_SECONDS );

		return rest_ensure_response( $template_data );
	}

	/**
	 * [jet_popup_get_content description]
	 * @return [type] [description]
	 */
	public function get_elementor_template_fonts_url( $template_id ) {

		$post_css = new \Elementor\Core\Files\CSS\Post( $template_id );

		$post_meta = $post_css->get_meta();

		if ( ! isset( $post_meta['fonts'] ) ) {
			return false;
		}

		$google_fonts = $post_meta['fonts'];

		$google_fonts = array_unique( $google_fonts );

		if ( empty( $google_fonts ) ) {
			return false;
		}

		foreach ( $google_fonts as &$font ) {
			$font = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
		}

		$fonts_url = sprintf( 'https://fonts.googleapis.com/css?family=%s', implode( rawurlencode( '|' ), $google_fonts ) );

		$subsets = [
			'ru_RU' => 'cyrillic',
			'bg_BG' => 'cyrillic',
			'he_IL' => 'hebrew',
			'el'    => 'greek',
			'vi'    => 'vietnamese',
			'uk'    => 'cyrillic',
			'cs_CZ' => 'latin-ext',
			'ro_RO' => 'latin-ext',
			'pl_PL' => 'latin-ext',
		];

		$locale = get_locale();

		if ( isset( $subsets[ $locale ] ) ) {
			$fonts_url .= '&subset=' . $subsets[ $locale ];
		}

		return $fonts_url;
	}

	/**
	 * [get_elementor_template_scripts_url description]
	 * @param  [type] $template_id [description]
	 * @return [type]              [description]
	 */
	public function get_elementor_template_scripts( $template_id ) {

		$document = \Elementor\Plugin::$instance->documents->get( $template_id );

		$elements_data = $document->get_elements_raw_data();

		$this->find_widgets_script_handlers( $elements_data );
	}

	/**
	 * [find_widgets_script_handlers description]
	 * @param  [type] $elements_data [description]
	 * @return [type]                [description]
	 */
	public function find_widgets_script_handlers( $elements_data ) {

		foreach ( $elements_data as $element_data ) {

			if ( 'widget' === $element_data['elType'] ) {
				$widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				$widget_script_depends = $widget->get_script_depends();

				if ( ! empty( $widget_script_depends ) ) {
					foreach ( $widget_script_depends as $key => $script_handler ) {
						$this->depended_scripts[] = $script_handler;
					}
				}

			} else {
				$element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				$childrens = $element->get_children();

				foreach ( $childrens as $key => $children ) {
					$children_data[$key] = $children->get_raw_data();

					$this->find_widgets_script_handlers( $children_data );
				}
			}
		}
	}

	/**
	 * [get_script_uri_by_handler description]
	 * @param  [type] $handler [description]
	 * @return [type]          [description]
	 */
	public function get_script_uri_by_handler( $handler ) {
		global $wp_scripts;

		if ( isset( $wp_scripts->registered[ $handler ] ) ) {

			$src = $wp_scripts->registered[ $handler ]->src;

			if ( 0 === strpos( $src, site_url() ) ) {
				return $src;
			} else {
				return site_url() . $src;
			}
		}

		return false;
	}

	/**
     * Generate a unique ID for your website
    */

	function generate_template_signature( $template_id ) {
		$unique_id = get_option( 'jet_elements_unique_id' );
	
		if ( empty( $unique_id ) ) {
			$unique_id = time();
			update_option( 'jet_elements_unique_id', $unique_id );
		}

		$key = defined( 'NONCE_KEY' ) ? NONCE_KEY : '';
	
		return md5( $unique_id . $template_id . $key );
	}

	/**
	 * Is public endpoint.
	 *
	 * @return bool
	 */

    public function permission_callback( $request ) {
        $args = $request->get_params();
        $template_id = isset( $args['id'] ) ? intval( $args['id'] ) : 0;
        $received_signature = isset( $args['signature'] ) ? sanitize_text_field( $args['signature'] ) : '';

        if ( ! $template_id || empty( $received_signature ) ) {
            return new WP_Error( 'rest_forbidden', __( 'Access denied.', 'jet-elements' ), [ 'status' => 403 ] );
        }

        $expected_signature = $this->generate_template_signature( $template_id );

        if ( $received_signature !== $expected_signature ) {
            return new WP_Error( 'rest_forbidden', __( 'Invalid request signature.', 'jet-elements' ), [ 'status' => 403 ] );
        }

        return true;
    }
}
