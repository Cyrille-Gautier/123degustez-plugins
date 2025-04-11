<?php
/**
 * Responsible for loading the plugin assets.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Responsible for loading the plugin assets.
 *
 * @since      1.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_Assets {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.6.0
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin' ) );
		add_action( 'amp_post_template_css', array( __CLASS__, 'amp_style' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'block_assets' ) );

		add_action( 'wprm_load_assets', array( __CLASS__, 'load' ) );
	}

	/**
	 * Enqueue stylesheets and scripts.
	 *
	 * @since    1.6.0
	 */
	public static function enqueue() {
		$filename = 'public-' . strtolower( WPRMP_BUNDLE );
		wp_register_style( 'wprmp-public', WPRMP_URL . 'dist/' . $filename . '.css', array(), WPRMP_VERSION, 'all' );

		// Only include scripts when not AMP page.
		if ( ! function_exists( 'is_amp_endpoint' ) || ! is_amp_endpoint() ) {
			wp_register_script( 'wprmp-public', WPRMP_URL . 'dist/' . $filename . '.js', array(), WPRMP_VERSION, true );

			// Get Timer Icons.
			ob_start();
			include( WPRM_DIR . 'assets/icons/timer/pause.svg' );
			$pause = ob_get_contents();
			ob_end_clean();

			ob_start();
			include( WPRM_DIR . 'assets/icons/timer/play.svg' );
			$play = ob_get_contents();
			ob_end_clean();

			ob_start();
			include( WPRM_DIR . 'assets/icons/timer/close.svg' );
			$close = ob_get_contents();
			ob_end_clean();

			wp_localize_script( 'wprmp-public', 'wprmp_public', apply_filters( 'wprmp_localize_public', array(
				'endpoints' => array(
					'private_notes' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/private-notes' ), '/' ),
				),
				'settings' => array(
					'recipe_template_mode' => WPRM_Settings::get( 'recipe_template_mode' ),
					'features_adjustable_servings' => WPRM_Settings::get( 'features_adjustable_servings' ),
					'adjustable_servings_round_to_decimals' => WPRM_Settings::get( 'adjustable_servings_round_to_decimals' ),
					'fractions_enabled' => WPRM_Settings::get( 'fractions_enabled' ),
					'fractions_use_mixed' => WPRM_Settings::get( 'fractions_use_mixed' ),
					'fractions_use_symbols' => WPRM_Settings::get( 'fractions_use_symbols' ),
					'fractions_max_denominator' => WPRM_Settings::get( 'fractions_max_denominator' ),
					'decimal_separator' => WPRM_Settings::get( 'decimal_separator' ),
					'features_comment_ratings' => WPRM_Settings::get( 'features_comment_ratings' ),
					'features_user_ratings' => WPRM_Settings::get( 'features_user_ratings' ),
					'user_ratings_thank_you_message' => WPRM_Settings::get( 'user_ratings_thank_you_message' ),
					'user_ratings_force_comment' => WPRM_Settings::get( 'user_ratings_force_comment' ),
					'user_ratings_force_comment_scroll_to' => WPRM_Settings::get( 'user_ratings_force_comment_scroll_to' ),
					'servings_changer_display' => WPRM_Settings::get( 'servings_changer_display' ),
					'template_ingredient_list_style' => WPRM_Settings::get( 'template_ingredient_list_style' ),
					'template_instruction_list_style' => WPRM_Settings::get( 'template_instruction_list_style' ),
					'template_color_icon' => WPRM_Settings::get( 'template_color_icon' ),
				),
				'timer' => array(
					'sound_file' => WPRMP_URL . 'assets/sounds/alarm.mp3',
					'text' => array(
						'start_timer' => __( 'Click to Start Timer', 'wp-recipe-maker-premium' ),
					),
					'icons' => array(
						'pause' => $pause,
						'play' => $play,
						'close' => $close,
					),
				),
				'recipe_submission' => array(
					'max_file_size' => wp_max_upload_size(),
					'text' => array(
						'image_size' => __( 'The image file is too large', 'wp-recipe-maker-premium' ),
					),
				),
			) ) );
		}
	}

	/**
	 * Actually load assets.
	 *
	 * @since	5.5.0
	 */
	public static function load() {
		wp_enqueue_style( 'wprmp-public' );

		if ( ! function_exists( 'is_amp_endpoint' ) || ! is_amp_endpoint() ) {
			wp_enqueue_script( 'wprmp-public' );
		}
	}

	/**
	 * Enqueue Gutenberg block assets.
	 *
	 * @since    4.0.0
	 */
	public static function block_assets() {
		$filename = 'public-' . strtolower( WPRMP_BUNDLE );
		wp_enqueue_style( 'wprmp-public', WPRMP_URL . 'dist/' . $filename . '.css', array(), WPRMP_VERSION, 'all' );

		$filename = 'blocks-' . strtolower( WPRMP_BUNDLE );
		wp_enqueue_script( 'wprmp-blocks', WPRMP_URL . 'dist/' . $filename . '.js', array(), WPRMP_VERSION, true );
	}

	/**
	 * Enqueue admin stylesheets and scripts.
	 *
	 * @since    2.0.0
	 */
	public static function enqueue_admin() {
		if ( method_exists( 'WPRM_Assets', 'should_load_admin_assets' ) && ! WPRM_Assets::should_load_admin_assets() ) {
			return;
		}

		$filename = 'admin-' . strtolower( WPRMP_BUNDLE );
		wp_enqueue_style( 'wprmp-admin', WPRMP_URL . 'dist/' . $filename . '.css', array(), WPRMP_VERSION, 'all' );
		wp_enqueue_script( 'wprmp-admin', WPRMP_URL . 'dist/' . $filename . '.js', array( 'wprm-shared', 'jquery', 'jquery-ui-sortable' ), WPRMP_VERSION, true );

		$count_posts = wp_count_posts( WPRM_POST_TYPE );

		wp_localize_script( 'wprmp-admin', 'wprmp_admin', apply_filters( 'wprmp_localize_admin',
			array(
				'settings' => array(
					'nutrition_facts_calculation_round_to_decimals' => WPRM_Settings::get( 'nutrition_facts_calculation_round_to_decimals' ),
					'unit_conversion_round_to_decimals' => WPRM_Settings::get( 'unit_conversion_round_to_decimals' ),
					'fractions_enabled' => WPRM_Settings::get( 'fractions_enabled' ),
					'fractions_use_mixed' => WPRM_Settings::get( 'fractions_use_mixed' ),
					'fractions_use_symbols' => WPRM_Settings::get( 'fractions_use_symbols' ),
					'fractions_max_denominator' => WPRM_Settings::get( 'fractions_max_denominator' ),
					'decimal_separator' => WPRM_Settings::get( 'decimal_separator' ),
				),
				'manage' => array(
					'collections_url' => admin_url( 'admin.php?page=wprm_recipe_collections' ),
					'recipe_collections_link' => WPRM_Settings::get( 'recipe_collections_link' ),
					'recipe_submissions' => $count_posts->pending,
				),
				'endpoints' => array(
					'collections' => WPRM_Addons::is_active( 'elite' ) ? get_rest_url( null, 'wp/v2/' . WPRMPRC_POST_TYPE ) : '',
					'saved_collection' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/saved-collection' ), '/' ),
					'nutrient' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/nutrient' ), '/' ),
					'nutrition' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/nutrition' ), '/' ),
					'unit_conversion' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/unit-conversion' ), '/' ),
					'equipment_affiliate' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/equipment-affiliate' ), '/' ),
					'ingredient_links' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/ingredient-links' ), '/' ),
					'custom_fields' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/custom-fields' ), '/' ),
					'recipe_submission' => rtrim( get_rest_url( null, 'wp-recipe-maker/v1/recipe-submission' ), '/' ),
				),
			)
		) );
	}

	/**
	 * Enqueue template style on AMP pages.
	 *
	 * @since    2.0.1
	 */
	public static function amp_style() {
		// Get AMP specific CSS.
		ob_start();
		include( WPRMP_DIR . 'dist/amp.css' );
		$css = ob_get_contents();
		ob_end_clean();

		// Get rid of !important flags.
		$css = str_ireplace( ' !important', '', $css );
		$css = str_ireplace( '!important', '', $css );

		echo $css;
	}
}

WPRMP_Assets::init();
