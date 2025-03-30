<?php
/**
 * Admin Options Page
 *
 * @package     First Comment Redirect
 * @subpackage  Admin Options Page
 * @copyright   Copyright (c) 2013, Rabin Shrestha
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * FCR admin class
 *
 * @since 1.0
 */
class First_Comment_Redirect_Settings_API {

	/**
	 * @var First_Comment_Redirect_Settings_API
	 * @since 1.0
	 */
	private static $instance_settings;

    /**
     * settings sections
     *
     * @var array
     * @since 1.0
     */
	private $settings_section = array();

	/**
     * settings fields
     *
     * @var array
     * @since 1.0
     */
	private $settings_fields = array();

	/**
     * settings options
     *
     * @var array
     * @since 1.0
     */
	public $fcr_options = array();

	/**
	 * Constructor is called whenever an object in instantiated
	 * @access private
	 * @since 1.0
 	 */
	private function __construct(){
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action('admin_init', array( $this, 'register_general_settings') );
	}

	/**
	 * Main First Comment Redirect Settings Instance
	 *
	 * Insures that only one instance of First_Comment_Redirect_Settings_API exists in memory at any one
	 * time. In singleton class you cannot create a second instance
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance_settings
	 * @return The one true First_Comment_Redirect_Settings_API
	 */
	public static function getInstance() {
		if ( !isset( self::$instance_settings ) ) {	
			self::$instance_settings = new self();
		    self::$instance_settings->settings_sections = self::$instance_settings->get_settings_sections();
			self::$instance_settings->settings_fields = self::$instance_settings->get_settings_fields();
			self::$instance_settings->fcr_options = self::$instance_settings->get_all_options();
		}
		return self::$instance_settings;
	}

	/**
	 * Adds a plugin settings menu in the dashboard
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
        add_menu_page( 
        	__( 'First Comment Redirect', 'fcr' ),	//Title tag display text
        	__( 'First Comment Redirect', 'fcr' ),	//Menu label
        	'edit_theme_options',					//Capability required
        	'first_comment_redirect_settings',		//menu slug
        	array( $this, 'show_settings_page' ) 	//call back function that renders the settings page
        );
    }

    /**
	 * Setting section array
	 * 
	 * @since 1.0
	 * @return array $sections
	 */
    public function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'fcr_basics',
                'title' => __( 'Basic Settings', 'fcr' )
            )
        );
        return $sections;
    }

    /**
	 * Setting fields array
	 *
	 * @since 1.0
	 * @return array $fcr_settings
	 */
    public function get_settings_fields() {
    	// Setup some default option sets
		$pages = get_pages();
		$pages_options = array( 0 => '' ); // Blank option
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$pages_options[ $page->ID ] = $page->post_title;
			}
		}

    	$fcr_settings = array(
			/** General Settings */
			'general' => apply_filters( 'fcr_settings_general',
				array(
					'redirect' => array(
						'id' => 'redirect',
						'name' => __( 'Redirect to', 'fcr' ),
						'desc' => __( 'Choose whether to redirect comment to any page or custom link', 'fcr' ),
						'type' => 'select',
						'options' => array(
							'page' => __( 'Page', 'fcr' ),
							'custom_link' => __( 'Custom link', 'fcr' )
						)
					),
					'page' => array(
						'id' => 'page',
						'name' => __( 'Page', 'fcr' ),
						'desc' => __( 'Redirect to Page', 'fcr' ),
						'type' => 'select',
						'options' => $pages_options
					),
					'custom_link' => array(
						'id' => 'custom_link',
						'name' => __( 'Custom Link', 'fcr' ),
						'desc' => __( 'Redirect to Custom Link', 'fcr' ),
						'type' => 'text'
					),
					'redirect_all_comment' => array(
					'id' => 'redirect_all_comment',
					'name' => __( 'Redirect All Comment', 'fcr' ),
					'desc' => __( 'Check this to redirect all comments', 'fcr' ),
					'type' => 'checkbox',
					)
				)
			)
		);
		if ( false == get_option( 'section_general' ) ) {
			add_option( 'section_general' );
		}
		return $fcr_settings;
    }

    /**
	 * registers admin settings
	 *
	 * @since 1.0
	 * @return void
	 */
    public function register_general_settings() {
    	//register admin settings
		register_setting( 'fcr_settings_general', 'fcr_settings_general',array( $this,'fcr_settings_sanitize' ) );

		add_settings_section( 
			'section_general',					// Unique identifier for the settings section
			__( 'General Plugin Settings','fcr'), 			// Section title
			'__return_false', 					// Section callback (we don't want anything)
			'first_comment_redirect_settings'	// Menu slug, used to uniquely identify the page;
		);
		foreach ( $this->settings_fields['general'] as $option ) {
			add_settings_field(
				'section_general[' . $option['id'] . ']',		// Unique identifier for the field for this section
				$option['name'],								// Setting field label
				array($this, 'callback_' . $option['type']),	// Function that renders the settings field
				'first_comment_redirect_settings',				// Menu slug, used to uniquely identify the page;
				'section_general',								// Settings section. Same as the first argument in the add_settings_section() above
				array(
					'id' => $option['id'],
					'desc' => $option['desc'],
					'name' => $option['name'],
					'section' => 'general',
					'size' => isset( $option['size'] ) ? $option['size'] : null, 
					'options' => isset( $option['options'] ) ? $option['options'] : '',
					'std' => isset( $option['std'] ) ? $option['std'] : ''
				)
			);
		}
	}

	/**
	 * Retrieves all plugin settings and returns as array.
	 *
	 * @since 1.0
	 * @return FCR settings
	 */
	public function get_all_options() {
		$fcr_options = get_option('fcr_settings_general');
		return $fcr_options;
	}

	/**
     * Displays a selectbox for a settings field
     *
     * @since 1.0
     * @param array $args settings field args
     * @return void
     */
    public function callback_select( $args ) {
        $fcr_options = $this->fcr_options;

		if ( isset( $fcr_options[ $args['id'] ] ) )
			$value = $fcr_options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$html = '<select id="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']" name="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<label for="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
    }

    /**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $fcr_options Array of all the FCR Options
	 * @return void
	 */
	public function callback_text( $args ) {
		$fcr_options = $this->fcr_options;

		if ( isset( $fcr_options[ $args['id'] ] ) )
			$value = $fcr_options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = isset( $args['size'] ) && !is_null($args['size']) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']" name="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
		$html .= '<label for="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $fcr_options Array of all the FCR Options
	 * @return void
	 */
	public function callback_checkbox( $args ) {
		$fcr_options = $this->fcr_options;

		$checked = isset($fcr_options[$args['id']]) ? checked(1, $fcr_options[$args['id']], false) : '';
		$html = '<input type="checkbox" id="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']" name="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
		$html .= '<label for="fcr_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * sanitize the admin options value
	 *
	 *
	 * @since 1.0
	 * @param array $options options value for plugin admin settings	
	 * @return array $fcr_options_validated sanitized options value
	 */
	public function fcr_settings_sanitize( $options ) {
		$fcr_options = $options;
		$fcr_options_validated = array();
		if ( isset( $fcr_options[ 'redirect' ] ) ) {
			$fcr_options_validated[ 'redirect' ] = esc_html( $fcr_options[ 'redirect' ] );
		}

		if ( isset( $fcr_options[ 'redirect' ] ) ) {
			$fcr_options_validated[ 'redirect' ] = esc_html( $fcr_options[ 'redirect' ] );
		}

		if ( isset( $fcr_options[ 'page' ] ) ) {
			$fcr_options_validated[ 'page' ] = absint( $fcr_options[ 'page' ] );
		}
		if ( isset( $fcr_options[ 'custom_link' ] ) ) {
			$fcr_options_validated[ 'custom_link' ] = esc_url_raw( $fcr_options[ 'custom_link' ] );
		}
		if ( isset( $fcr_options[ 'redirect_all_comment' ] ) ) {
			$fcr_options_validated[ 'redirect_all_comment' ] = $fcr_options[ 'redirect_all_comment' ];
		}

		return $fcr_options_validated;
	}

	/**
	 * Displays the settings page in admin
	 *
	 * @since 1.0	
	 * @return void
	 */
	public function show_settings_page() { 
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general'?>
			<div class="wrap">
				<h2 class="nav-tab-wrapper">
					<a href="<?php echo add_query_arg('tab', 'general', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'fcr'); ?></a>
				</h2>
				<div id="tab_container">
					<form method="post" action="options.php">
						<?php
							settings_fields( 'fcr_settings_general' );
							do_settings_sections( 'first_comment_redirect_settings' );
							submit_button();
						?>
					</form>
				</div><!-- #tab_container-->
			</div><!-- .wrap -->
	<?php
	}

}
?>