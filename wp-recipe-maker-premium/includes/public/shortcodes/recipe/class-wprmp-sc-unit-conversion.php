<?php
/**
 * Handle the Premium unit conversion shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium unit conversion shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Unit_Conversion {
	public static function init() {
		add_filter( 'wprm_recipe_unit_conversion_shortcode', array( __CLASS__, 'shortcode' ), 10, 3 );
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
		if ( ! $recipe || ! $recipe->ingredients() || ! WPRM_Addons::is_active( 'unit-conversion' ) || ! WPRM_Settings::get( 'unit_conversion_enabled' ) ) {
			return '';
		}

		$ingredients = $recipe->ingredients_without_groups();
		$unit_systems = array(
			1 => true, // Default unit system.
		);

		// Show unit conversion for demo recipe.
		if ( 'demo' === $recipe->id() ) {
			$unit_systems[2] = true;
		};

		// Check if there are values for any other unit system.
		if ( $ingredients ) {
			$replace_fraction_symbols = WPRM_Settings::get( 'automatic_amount_fraction_symbols' );

			foreach ( $ingredients as $index => $ingredient ) {
				// Maybe replace fractions in amount.
				if ( $replace_fraction_symbols && isset( $ingredients[ $index ]['amount'] ) ) {
					$ingredients[ $index ]['amount'] = WPRM_Recipe_Parser::replace_any_fractions_with_symbol( $ingredients[ $index ]['amount'] );
				}

				if ( isset( $ingredient['converted'] ) ) {
					foreach ( $ingredient['converted'] as $system => $values ) {
						if ( $values['amount'] || $values['unit'] ) {
							$unit_systems[ $system ] = true;

							// Maybe replace fractions in amount.
							if ( $replace_fraction_symbols ) {
								$ingredients[ $index ]['converted'][ $system ]['amount'] = WPRM_Recipe_Parser::replace_any_fractions_with_symbol( $values['amount'] );
							}
						}
					}
				}
			}
		}

		if ( count( $unit_systems ) > 1 ) {
			$style = '';
			$classes = array(
				'wprm-unit-conversion-container',
				'wprm-unit-conversion-container-' . esc_attr( $recipe->id() ),
				'wprm-unit-conversion-container-' . $atts['style'],
			);

			$unit_conversion_output = '';
			$recipe_unit_system = intval( $recipe->unit_system() );

			if ( 'links' === $atts['style'] || 'buttons' === $atts['style'] ) {
				$unit_system_links = array();
				foreach ( $unit_systems as $unit_system => $value ) {
					// Button style.
					$button_style = '';
					if ( 'buttons' === $atts['style'] ) {
						$button_style .= 'background-color: ' . $atts['button_accent'] . ';';
						$button_style .= 'color: ' . $atts['button_background'] . ';';

						if ( 1 !== $unit_system ) {
							$border = is_rtl() ? 'border-right' : 'border-left';
							$button_style .= $border . ': 1px solid ' . $atts['button_accent'] . ';';
						}
					}

					$active = 1 === $unit_system ? ' wprmpuc-active' : '';
					$unit_system_label = 2 === $recipe_unit_system ? WPRM_Settings::get( 'unit_conversion_system_' . ( 3 - $unit_system ) ) : WPRM_Settings::get( 'unit_conversion_system_' . $unit_system );

					if ( 'links' === $atts['style'] ) {
						$unit_system_links[] = '<a href="#" role="button" class="wprm-unit-conversion' . $active . '" data-system="' . esc_attr( $unit_system ) . '" data-recipe="' . esc_attr( $recipe->id() ) . '" style="' . $button_style .'" aria-label="' . __( 'Change unit system to', 'wp-recipe-maker' ) . ' ' . $unit_system_label . '">' . $unit_system_label . '</a>';
					} else {
						$unit_system_links[] = '<button href="#" class="wprm-unit-conversion' . $active . '" data-system="' . esc_attr( $unit_system ) . '" data-recipe="' . esc_attr( $recipe->id() ) . '" style="' . $button_style .'" aria-label="' . __( 'Change unit system to', 'wp-recipe-maker' ) . ' ' . $unit_system_label . '">' . $unit_system_label . '</button>';
					}
				}

				$classes[] = 'wprm-block-text-' . $atts['text_style'];

				// Custom Style for buttons.
				if ( 'buttons' === $atts['style'] ) {
					$style .= 'background-color: ' . $atts['button_background'] . ';';
					$style .= 'border-color: ' . $atts['button_accent'] . ';';
					$style .= 'color: ' . $atts['button_accent'] . ';';
					$style .= 'border-radius: ' . $atts['button_radius'] . ';';
				}
				
				$separator = '';
				if ( 'links' === $atts['style'] ) {
					$separator = $atts['separator'];
				}

				$unit_conversion_output = implode( $separator, $unit_system_links );
			} else {
				$unit_system_options = '';
				foreach ( $unit_systems as $unit_system => $value ) {
					$selected = 1 === $unit_system ? ' selected="selected"' : '';
					$unit_system_label = 2 === $recipe_unit_system ? WPRM_Settings::get( 'unit_conversion_system_' . ( 3 - $unit_system ) ) : WPRM_Settings::get( 'unit_conversion_system_' . $unit_system );
					$unit_system_options .= '<option value="' . esc_attr( $unit_system ) . '"' . $selected . '>' . $unit_system_label . '</option>';
				}

				$unit_conversion_output = '<select class="wprm-unit-conversion-dropdown" data-recipe="' . esc_attr( $recipe->id() ) . '" aria-label="' . __( 'Adjust recipe unit system', 'wp-recipe-maker' ) . '">' . $unit_system_options . '</select>';
			}
			

			// Output.
			$output = '<div class="' . implode( ' ', $classes ) . '" style="' . $style . '">' . $unit_conversion_output . '</div>';

			WPRM_Assets::add_js_data( 'wprmpuc_recipe_' . $recipe->id(), array(
				'ingredients' => $ingredients,
			));
		}

		return $output;
	}
}

WPRMP_SC_Unit_Conversion::init();