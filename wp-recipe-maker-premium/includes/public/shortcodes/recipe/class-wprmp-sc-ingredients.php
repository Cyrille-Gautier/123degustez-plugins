<?php
/**
 * Handle the Premium recipe ingredients shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium recipe ingredients shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Ingredients {
	public static function init() {
		add_filter( 'wprm_recipe_ingredients_shortcode_checkbox', array( __CLASS__, 'checkbox' ) );
		add_filter( 'wprm_recipe_ingredients_shortcode_amount_unit', array( __CLASS__, 'amount_unit' ), 10, 3 );
		add_filter( 'wprm_recipe_ingredients_shortcode_link', array( __CLASS__, 'link' ), 10, 3 );
	}

	/**
	 * Add checkboxes.
	 *
	 * @since	5.6.0
	 * @param	mixed $output Current output.
	 */
	public static function checkbox( $output ) {
		return WPRMP_Checkboxes::checkbox( $output );
	}

	/**
	 * Add unit conversion.
	 *
	 * @since	5.6.0
	 * @param	mixed $amount_unit 	Current output.
	 * @param	array $atts 		Shortcode attributes.
	 * @param	array $ingredient 	Ingredient we're outputting.
	 */
	public static function amount_unit( $amount_unit, $atts, $ingredient ) {
		if ( 'both' === $atts['unit_conversion'] && WPRM_Addons::is_active( 'unit-conversion' ) && WPRM_Settings::get( 'unit_conversion_enabled' ) ) {

			// Surround first unit system with span.
			$amount_unit = '<span class="wprm-recipe-ingredient-unit-system wprm-recipe-ingredient-unit-system-1">' . trim( $amount_unit ) . '</span>';

			// Add second unit system.
			$second_system = '';
			if ( isset( $ingredient['converted'] ) && isset( $ingredient['converted'][2] ) ) {

				// Maybe replace fractions in amount.
				if ( WPRM_Settings::get( 'automatic_amount_fraction_symbols' ) ) {
					$ingredient['converted'][2]['amount'] = WPRM_Recipe_Parser::replace_any_fractions_with_symbol( $ingredient['converted'][2]['amount'] );
				}

				// Check if identical if we're not showing them.
				$skip_second_system = false;
				if ( ! $atts['unit_conversion_show_identical'] ) {
					if ( $ingredient['amount'] === $ingredient['converted'][2]['amount'] && $ingredient['unit'] === $ingredient['converted'][2]['unit'] ) {
						$skip_second_system = true;
					}
				}

				// Make sure amount value is not NaN.
				if ( 'NaN' === $ingredient['converted'][2]['amount'] ) {
					$skip_second_system = true;
				}

				// Add second unit system to output.
				if ( ! $skip_second_system ) {
					if ( $ingredient['converted'][2]['amount'] ) {
						$second_system .= '<span class="wprm-recipe-ingredient-amount">' . $ingredient['converted'][2]['amount'] . '</span> ';
					}
					if ( $ingredient['converted'][2]['unit'] ) {
						$second_system .= '<span class="wprm-recipe-ingredient-unit">' . $ingredient['converted'][2]['unit'] . '</span>';
					}
				}
			}

			if ( $second_system && 'parentheses' === $atts['unit_conversion_both_style'] ) {
				$second_system = '(' . $second_system . ')';
			}

			$amount_unit .= ' <span class="wprm-recipe-ingredient-unit-system wprm-recipe-ingredient-unit-system-2">' . $second_system . '</span> ';
		}

		return $amount_unit;
	}

	/**
	 * Add ingredient links.
	 *
	 * @since	5.6.0
	 * @param	mixed $output 		Current output.
	 * @param	array $ingredient 	Ingredient we're outputting.
	 * @param	mixed $recipe 		Recipe the shortcode is getting output for.
	 */
	public static function link( $output, $ingredient, $recipe ) {
		$link = array();
		
		if ( 'global' === $recipe->ingredient_links_type() ) {
			$link = WPRMP_Ingredient_Links::get_ingredient_link( $ingredient['id'] );
		} elseif ( isset( $ingredient['link'] ) ) {
			$link = $ingredient['link'];
		}

		// Easy Affiliate Links integration.
		if ( class_exists( 'EAFL_Link_Manager' ) ) {
			if ( isset( $link['eafl'] ) && $link['eafl'] ) {
				return do_shortcode( '[eafl id="' . $link['eafl'] .'"]' . $output . '[/eafl]' );
			}
		}

		if ( isset( $link['url'] ) && $link['url'] ) {
			$link_output = WPRMP_Links::get( $link['url'], $link['nofollow'], $output, 'ingredient' );

			if ( $link_output ) {
				return $link_output;
			}
		} else {
			return $output;
		}

		return $output;
	}
}

WPRMP_SC_Ingredients::init();