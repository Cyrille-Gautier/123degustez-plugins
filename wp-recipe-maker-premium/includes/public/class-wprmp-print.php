<?php
/**
 * Handle Premium features for recipe printing.
 *
 * @link       http://bootstrapped.ventures
 * @since      6.1.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Handle Premium features for recipe printing.
 *
 * @since      6.1.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_Print {

	/**
	 * Register actions and filters.
	 *
	 * @since    6.1.0
	 */
	public static function init() {
		add_filter( 'wprm_print_output', array( __CLASS__, 'output' ), 2, 2 );
		add_filter( 'wprm_print_header_images', array( __CLASS__, 'print_header_images' ), 9, 2 );
	}

	/**
	 * Get output for the print page.
	 *
	 * @since    6.1.0
	 * @param	array $output 	Current output for the print page.
	 * @param	array $args	 	Arguments for the print page.
	 */
	public static function output( $output, $args ) {
		// Load premium styling.
		$output['assets'][] = array(
			'type' => 'css',
			'url' => WPRMP_URL . 'dist/public-' . strtolower( WPRMP_BUNDLE ) . '.css',
		);
		$output['assets'][] = array(
			'type' => 'css',
			'url' => WPRMP_URL . 'dist/print.css',
		);
		$output['assets'][] = array(
			'type' => 'js',
			'url' => WPRMP_URL . 'dist/print.js',
		);
		$output['assets'][] = array(
			'type' => 'custom',
			'html' => self::print_accent_color_styling(),
		);

		if ( 'recipe' === $output['type'] ) {
			// Needed for adjustable servings.
			$settings = array(
				'recipe_template_mode' => WPRM_Settings::get( 'recipe_template_mode' ),
				'features_adjustable_servings' => true,
				'adjustable_servings_round_to_decimals' => WPRM_Settings::get( 'adjustable_servings_round_to_decimals' ),
				'fractions_enabled' => WPRM_Settings::get( 'fractions_enabled' ),
				'fractions_use_mixed' => WPRM_Settings::get( 'fractions_use_mixed' ),
				'fractions_use_symbols' => WPRM_Settings::get( 'fractions_use_symbols' ),
				'fractions_max_denominator' => WPRM_Settings::get( 'fractions_max_denominator' ),
				'decimal_separator' => WPRM_Settings::get( 'decimal_separator' ),
			);

			$output['assets'][] = array(
				'type' => 'custom',
				'html' => '<script>wprmp_public = { settings : ' . json_encode( $settings ) . ' };</script>',
			);

			// Needed for Unit Conversion.
			$recipe = $output['recipe'];

			if ( $recipe ) {
				$ingredients = $recipe->ingredients_without_groups();

				foreach ( (array) $ingredients as $key => $value ) {
					if ( ! is_scalar( $value ) ) continue;
					$ingredients[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
				}
				$output['assets'][] = array(
					'type' => 'custom',
					'html' => '<script>var wprmpuc_recipe_' . $recipe->id() . ' = ' . wp_json_encode( array( 'ingredients' => $ingredients ) ) . ';</script>',
				);
			}

			// Nutrition toggle header.
			$output['header'] .= self::get_nutrition_toggle_header( $recipe );

			// Adjustable servings header.
			$servings = $recipe ? $recipe->servings() : false;

			if ( $servings && WPRM_Settings::get( 'print_adjustable_servings' ) ) {
				$output['header'] .= '<div id="wprm-print-servings-container">';
				$output['header'] .= '<span class="wprm-print-servings-decrement wprm-print-servings-change">–</span><input id="wprm-print-servings" type="text" value="' . esc_attr( $servings ) . '" min="1"><span class="wprm-print-servings-increment wprm-print-servings-change">+</span>';
				$output['header'] .= '&nbsp;<span id="wprm-print-servings-unit">' . __( 'servings', 'wp-recipe-maker-premium' ) . '</span>';
				$output['header'] .= '</div>';
			}

			// Unit Conversion Header.
			if ( $recipe && WPRM_Settings::get( 'print_unit_conversion' ) && WPRM_Addons::is_active( 'unit-conversion' ) ) {
				$ingredients = $recipe->ingredients_without_groups();

				$unit_systems = array(
					1 => true, // Default unit system.
				);

				foreach ( $ingredients as $ingredient ) {
					if ( isset( $ingredient['converted'] ) ) {
						foreach ( $ingredient['converted'] as $system => $values ) {
							if ( $values['amount'] || $values['unit'] ) {
								$unit_systems[ $system ] = true;
							}
						}
					}
				}

				// Add UC header:
				$recipe_unit_system = intval( $recipe->unit_system() );
				$output['header'] .= self::get_unit_conversion_header( $unit_systems, $recipe_unit_system );
			}
		}

		if ( 'recipes' === $output['type'] ) {
			// Needed for adjustable servings.
			$output['assets'][] = array(
				'type' => 'custom',
				'html' => '<script>wprmp_public = { settings : { recipe_template_mode: "' . WPRM_Settings::get( 'recipe_template_mode' ) . '", features_adjustable_servings : true, adjustable_servings_round_to_decimals: ' . WPRM_Settings::get( 'adjustable_servings_round_to_decimals' ) . ' } };</script>',
			);

			// Need serving information for each recipe.
			$output['assets'][] = array(
				'type' => 'custom',
				'html' => '<script>var wprmp_print_recipes = ' . wp_json_encode( $output['recipes'] ) . ';</script>',
			);

			// Nutrition toggle header.
			$output['header'] .= self::get_nutrition_toggle_header();

			// Loop over all recipes to check for unit system.
			$unit_systems = array(
				1 => true, // Default unit system.
			);

			foreach ( $output['recipes'] as $recipe_output ) {
				$recipe_id = isset( $recipe_output['recipe_id'] ) ? $recipe_output['recipe_id'] : $recipe_output['id'];
				$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );

				if ( $recipe ) {
					$ingredients = $recipe->ingredients_without_groups();
	
					foreach ( (array) $ingredients as $key => $value ) {
						// Check for second unit system.
						if ( isset( $value['converted'] ) ) {
							foreach ( $value['converted'] as $system => $values ) {
								if ( $values['amount'] || $values['unit'] ) {
									$unit_systems[ $system ] = true;
								}
							}
						}
						
						// Format data for JS.
						if ( ! is_scalar( $value ) ) continue;
						$ingredients[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
					}
					$output['assets'][] = array(
						'type' => 'custom',
						'html' => '<script>var wprmpuc_recipe_' . $recipe_output['id'] . ' = ' . wp_json_encode( array( 'ingredients' => $ingredients ) ) . ';</script>',
					);
				}
			}

			// Will add if there are multiple unit systems.
			$output['header'] .= self::get_unit_conversion_header( $unit_systems );
		}

		return $output;
	}

	/**
	 * Get header for nutrition toggle.
	 *
	 * @since    8.1.0
	 */
	private static function get_nutrition_toggle_header( $recipe = false ) {
		$header = '';

		// Recipe nutrition toggle.
		$has_nutrition = false;
		$nutrition = $recipe ? $recipe->nutrition() : array();

		foreach ( $nutrition as $field => $value ) {
			if ( $value ) {
				$has_nutrition = true;
				break;
			}
		}

		if ( false === $recipe || $has_nutrition ) {
			$checked = WPRM_Settings::get( 'print_show_nutrition' ) ? ' checked="checked"' : '';

			$header .= '<div class="wprm-print-toggle-container">';
			$header .= '<input type="checkbox" id="wprm-print-toggle-recipe-nutrition" class="wprm-print-toggle" value="1" ' . $checked . '/><label for="wprm-print-toggle-recipe-nutrition">' . __( 'Nutrition Label', 'wp-recipe-maker' ) . '</label>';
			$header .= '</div>';
		}

		return $header;
	}

	/**
	 * Get header for unit conversion.
	 *
	 * @since    7.2.0
	 */
	private static function get_unit_conversion_header( $unit_systems, $recipe_unit_system = 1 ) {
		$output = '';

		if ( count( $unit_systems ) > 1 && WPRM_Settings::get( 'print_unit_conversion' ) && WPRM_Addons::is_active( 'unit-conversion' ) ) {
			$output .= '<div id="wprm-print-unit-conversion-container">';

			foreach ( $unit_systems as $unit_system => $value ) {
				$active = 1 === $unit_system ? ' wprmpuc-active' : '';
				$unit_system_label = 2 === $recipe_unit_system ? WPRM_Settings::get( 'unit_conversion_system_' . ( 3 - $unit_system ) ) : WPRM_Settings::get( 'unit_conversion_system_' . $unit_system );
				$output .= '<a href="#" role="button" class="wprm-unit-conversion' . $active . '" data-system="' . esc_attr( $unit_system ) . '" aria-label="' . __( 'Change unit system to', 'wp-recipe-maker' ) . ' ' . $unit_system_label . '">' . $unit_system_label . '</a>';
			}

			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Get custom styling for the print accent color.
	 *
	 * @since    6.1.0
	 */
	public static function print_accent_color_styling() {
		$output = '';
		$color = WPRM_Settings::get( 'print_accent_color' );
		$color_default = WPRM_Settings::get_default( 'print_accent_color' );

		if ( $color !== $color_default ) {
			$output .= '<style>';
			$output .= ' #wprm-print-servings-container #wprm-print-servings { border-color: ' . $color . ' !important; }';
			$output .= ' #wprm-print-servings-container .wprm-print-servings-change { border-color: ' . $color . ' !important; background-color: ' . $color . ' !important; }';
			$output .= ' #wprm-print-unit-conversion-container a.wprm-unit-conversion { border-color: ' . $color . ' !important; }';
			$output .= ' #wprm-print-unit-conversion-container a.wprm-unit-conversion.wprmpuc-active { background-color: ' . $color . ' !important; }';
			$output .= '</style>';
		}

		return $output;
	}
}

WPRMP_Print::init();