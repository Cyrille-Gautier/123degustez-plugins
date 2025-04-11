<?php
/**
 * Advanced list style for the recipe template.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.10.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Advanced list style for the recipe template.
 *
 * @since      5.10.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_List_Style {

	private static $uids = array();

	/**
	 * Register actions and filters.
	 *
	 * @since	5.10.0
	 */
	public static function init() {
		add_filter( 'wprm_recipe_equipment_shortcode', array( __CLASS__, 'shortcode' ), 10, 2 );
		add_filter( 'wprm_recipe_ingredients_shortcode', array( __CLASS__, 'shortcode' ), 10, 2 );
		add_filter( 'wprm_recipe_instructions_shortcode', array( __CLASS__, 'shortcode' ), 10, 2 );
	}

	/**
	 * Filter shortcode output to add advanced styling.
	 *
	 * @since	5.10.0
	 */
	public static function shortcode( $output, $atts ) {
		if ( 'advanced' === $atts['list_style'] ) {
			do {
				$uid = rand( 0, 9999 );
			} while ( in_array( $uid, self::$uids ) );
			self::$uids[] = $uid;

			// Add UID class.
			$output = str_ireplace( '<ul class="', '<ul class="wprm-advanced-list wprm-advanced-list-' . $uid . ' ', $output );

			// Continue numbering or not.
			if ( ! (bool) $atts['list_style_continue_numbers'] ) {
				$output = str_ireplace( 'wprm-advanced-list ', 'wprm-advanced-list wprm-advanced-list-reset ', $output );
			}

			$style = '<style type="text/css">';
			$style .= 'ul.wprm-advanced-list-' . $uid . ' li:before {';
			$style .= 'background-color: ' . $atts['list_style_background'] . ';';
			$style .= 'color: ' . $atts['list_style_text'] . ';';
			$style .= 'width: ' . $atts['list_style_size'] . ';';
			$style .= 'height: ' . $atts['list_style_size'] . ';';
			$style .= 'font-size: ' . $atts['list_style_text_size'] . ';';
			$style .= 'line-height: ' . $atts['list_style_text_size'] . ';';
			$style .= '}';
			$style .= '</style>';

			$output = $style . $output;
		}		
		return $output;
	}
}

WPRMP_List_Style::init();
