<?php
/**
 * Handle the Premium nutrition label shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium nutrition label shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Nutrition_Label {
	public static function init() {
		add_filter( 'wprm_nutrition_label_shortcode', array( __CLASS__, 'shortcode' ), 10, 3 );
	}

	/**
	 * Nutrition label shortcode.
	 *
	 * @since	5.6.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function shortcode( $output, $atts, $recipe ) {
		$output = '';

		$align = in_array( $atts['align'], array( 'center', 'right' ) ) ? $atts['align'] : 'left';
		$output .= WPRM_Shortcode_Helper::get_section_header( $atts, 'nutrition' );

		// Output.
		$classes = array(
			'wprm-nutrition-label-container',
			'wprm-nutrition-label-container-' . $atts['style'],
		);

		if ( 'label' !== $atts['style'] ) {
			$classes[] = 'wprm-block-text-' . $atts['text_style'];
		}

		$output .= '<div class="' . implode( ' ', $classes ) . '" style="text-align: ' . $align . ';">';

		switch ( $atts['style'] ) {
			case 'simple':
			case 'grouped':
				$show_daily = (bool) $atts['daily'];
				$nutrition = $recipe->nutrition();
				$nutrition_output = array();

				$nutrition_fields = WPRM_Nutrition::get_fields();
				$nutrition_fields['serving_size']['unit'] = isset( $nutrition['serving_unit'] ) && $nutrition['serving_unit'] ? $nutrition['serving_unit'] : WPRM_Settings::get( 'nutrition_default_serving_unit' );

				foreach ( $nutrition_fields as $field => $options ) {
					if ( isset( $nutrition[ $field ] ) && false !== $nutrition[ $field ] && ( WPRM_Settings::get( 'nutrition_label_zero_values' ) || $nutrition[ $field ] ) ) {

						$percentage = false;
						if ( $show_daily && isset( $options['daily'] ) && $options['daily'] ) {
							$percentage = round( floatval( $nutrition[ $field ] ) / $options['daily'] * 100 );
						}

						$style = '';
						if ( 'grouped' === $atts['style'] ) {
							$style = 'style="flex-basis: ' . $atts['group_width'] . '"';
						}

						$field_output = '<span class="wprm-nutrition-label-text-nutrition-container wprm-nutrition-label-text-nutrition-container-' . esc_attr( $field ) . '"' . $style .'>';
						$field_output .= '<span class="wprm-nutrition-label-text-nutrition-label  wprm-block-text-' . $atts['label_style'] . '" style="color: ' . $atts['label_color'] . '">' . __( $options['label'] , 'wp-recipe-maker-premium' ) . $atts['label_separator'] . '</span>';
						$field_output .= '<span class="wprm-nutrition-label-text-nutrition-value" style="color: ' . $atts['value_color'] . '">' . $nutrition[ $field ] . '</span>';
						$field_output .= $atts['unit_separator'];
						$field_output .= '<span class="wprm-nutrition-label-text-nutrition-unit" style="color: ' . $atts['value_color'] . '">' . $options['unit'] . '</span>';

						if ( $percentage ) {
							$field_output .= '<span class="wprm-nutrition-label-text-nutrition-daily" style="color: ' . $atts['value_color'] . '"> (' . $percentage . '%)</span>';
						}

						$field_output .= '</span>';

						$nutrition_output[] = $field_output;
					}
				}

				if ( ! count( $nutrition_output ) ) {
					return '';
				}

				$nutrition_separator = '';
				if ( 'simple' === $atts['style'] ) {
					$nutrition_separator = '<span style="color: ' . $atts['label_color'] . '">' . $atts['nutrition_separator'] . '</span>';
				}
				
				$output .= implode( $nutrition_separator, $nutrition_output );
				break;
			default:
				$label = WPRMP_Nutrition::label( $recipe );
				if ( ! $label ) {
					return '';
				}

				if ( 'legacy' === WPRM_Settings::get( 'nutrition_label_style' ) ) {
					$style = 'style="';
					$style .= 'background-color: ' . $atts['label_background_color'] . ';';
					$style .= 'color: ' . $atts['label_text_color'] . ';';
					$style .= '"';

					$label = str_replace( 'class="wprm-nutrition-label"', 'class="wprm-nutrition-label" ' . $style, $label );
				}
				
				$output .= $label;
			}

		$output .= '</div>';

		return $output;
	}
}

WPRMP_SC_Nutrition_Label::init();