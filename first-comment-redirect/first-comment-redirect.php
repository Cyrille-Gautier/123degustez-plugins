<?php
/**
 * Plugin Name: First Comment Redirect
 * Plugin URI: http://rabinshrestha.com/first-comment-redirect
 * Description: Redirects commenter to your desired page or custom link who just made their first comment on your site. You can use this page to promote your site by thanking the user for their comment and asking them to subscribe your blog, like your blog on facebook, follow on twitter and many more. Also has the option to redirect every comment.
 * Author: Rabin Shrestha
 * Author URI: http://rabinshrestha.com
 * Version: 1.0.3
 *
 * First Comment Redirect is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * First Comment Redirect is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with First Comment Redirect. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package First_Comment_Redirect
 * @author Rabin Shrestha <rabinstha.me@gmail.com>
 * @version 1.0
 */

/**
 * If this file is attempted to be accessed directly, we'll exit.
 *
 * The following check provides a level of security from other files
 * that request data directly.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'First_Comment_Redirect' ) ) {
	/**
	 * Main Class
	 *
	 * @since 1.0
	 */
	class First_Comment_Redirect {
		/**
		 * @var First_Comment_Redirect
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * First Redirect Comment theme options Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $setting_options;

		/**
		 * Private constructor prevents construction outside this class.
	 	 */
		Private function __construct() {
 		}

 		/**
		 * Main First Comment Redirect Instance
		 *
		 * Insures that only one instance of First_Comment_Redirect exists in memory at any one
		 * time. In singleton class you cannot create a second instance
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @uses First_Comment_Redirect::setup_constants() Setup the contants needed
	 	 * @uses First_Comment_Redirect::includes() Include the required files
	 	 * @uses First_Comment_Redirect::setup_actions() Setup the hooks and actions
	 	 * @uses First_Comment_Redirect::load_textdomain() loads the textdomain for tranlation
		 * @see FCR()
		 * @return The one true First_Comment_Redirect
		 */
 		public static function getInstance() {
			 if ( !isset( self::$instance ) ) {
			 	self::$instance = new self();
			    self::$instance->setup_constants();
			    self::$instance->includes();
			    self::$instance->setup_actions();
			    self::$instance->load_textdomain();
			    self::$instance->setting_options = First_Comment_Redirect_Settings_API::getInstance();
			 }

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'fcr' ), '1.0' );
		}

 		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			$this->version    = '1.0';

			// Setup some base path and URL information
			$this->file       = __FILE__; // Plugin Root File
			$this->plugin_dir = apply_filters( 'fcr_plugin_dir_path',  plugin_dir_path( $this->file ) ); // Plugin Folder Path
			$this->plugin_url = apply_filters( 'fcr_plugin_dir_url',   plugin_dir_url ( $this->file ) ); // Plugin Folder URL
			$this->basename   = apply_filters( 'fcr_plugin_basenname', plugin_basename( $this->file ) ); //path relative to plugin folder
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {
			require_once $this->plugin_dir . 'includes/admin/class-settings-api.php';
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @access private
		 * @since 1.0
		 * @uses add_filter() hook a function to specific filter action
		 */
		private function setup_actions() {
		    add_filter( 'comment_post_redirect', array( $this, 'redirect_after_first_comment' ), 10, 2 );
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			 load_plugin_textdomain( 'fcr', false, dirname( $this->basename ) . '/languages' );
		}

  		/**
  		 * Redirects the commenters to their desired page/links
  		 * 
  		 * @param string $url actual redirect url
  		 * @param object $comment comment object
  		 * @return string $url altered url
  		 */
		function redirect_after_first_comment ( $url, $comment ) {
			$comment_count = get_comments(
								array('author_email' => $comment->comment_author_email,'count' => true)
							);
			//if all comments are to be redirected
			if( $this->setting_options->fcr_options['redirect_all_comment'] == 1 ){
				if( $this->setting_options->fcr_options['redirect'] == 'custom_link') {
					$url = $this->setting_options->fcr_options['custom_link'];
					wp_redirect( $url );
					exit;
				}else{
					$url = get_permalink( $this->setting_options->fcr_options['page'] );
				}
			} else {
				//Check if this is the first Comment (based on the Commenter email)
				if ( $comment_count == 1 ) {
					//Redirect URL for first time Commenters.
					if( $this->setting_options->fcr_options['redirect'] == 'custom_link') {
						$url = $this->setting_options->fcr_options['custom_link'];
						wp_redirect( $url );
						exit;
					}else{
						$url = get_permalink( $this->setting_options->fcr_options['page'] );
					}
				}
			}
			return $url;
		}
  	}
}

/**
 * The main function responsible for returning the one true First_Comment_Redirect
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @since 1.0
 * @return object The one true First_Comment_Redirect Instance
 */
function FCR() {
	return First_Comment_Redirect::getInstance();
}

// Get FCR Running
FCR();
?>
