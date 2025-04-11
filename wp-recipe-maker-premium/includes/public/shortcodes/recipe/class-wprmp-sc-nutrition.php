<?php
/**
 * Handle the Premium nutrition shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium nutrition shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Nutrition {
	public static function init() {
		add_filter( 'wprm_nutrition_shortcode_nutrient', array( __CLASS__, 'nutrient' ), 10, 3 );
	}

	/**
	 * Nutrition label shortcode.
	 *
	 * @since	5.6.0
	 * @param	mixed $nutrient	Nutrient to output.
	 * @param	array $atts   	Options passed along with the shortcode.
	 * @param	mixed $recipe 	Recipe the shortcode is getting output for.
	 */
	public static function nutrient( $nutrient, $atts, $recipe ) {
		$show_daily = (bool) $atts['daily'];
		
		$nutrition = $recipe->nutrition();
		$nutrition_fields = WPRM_Nutrition::get_fields();
		$value = isset( $nutrition[ $atts['field'] ] ) ? $nutrition[ $atts['field'] ] : false;

		if ( $value !== false && ( WPRM_Settings::get( 'nutrition_label_zero_values' ) || $value ) ) {
			if ( $show_daily ) {
				$daily = isset( $nutrition_fields[ $atts['field'] ]['daily'] ) ? $nutrition_fields[ $atts['field'] ]['daily'] : false;

				if ( $daily ) {
					$nutrient['value'] = round( floatval( $value ) / $daily * 100 );
					$nutrient['unit'] = '%';
				}
			} else {
				$nutrient['value'] = $value;

				if ( 'serving_size' === $atts['field'] ) {
					$nutrient['unit'] = $nutrition_fields[ $atts['field'] ]['unit'] = isset( $nutrition['serving_unit'] ) && $nutrition['serving_unit'] ? $nutrition['serving_unit'] : $nutrition_fields[ $atts['field'] ]['unit'];
				} else {
					$nutrient['unit'] = $nutrition_fields[ $atts['field'] ]['unit'];
				}
			}
		}

		return $nutrient;
	}
}

WPRMP_SC_Nutrition::init();