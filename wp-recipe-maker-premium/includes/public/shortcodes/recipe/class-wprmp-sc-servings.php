<?php
/**
 * Handle the Premium servings shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium servings shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Servings {
	public static function init() {
		add_filter( 'wprm_recipe_servings_shortcode', array( __CLASS__, 'shortcode' ), 10, 3 );
		add_filter( 'wprm_recipe_adjustable_servings_shortcode', array( __CLASS__, 'adjustable_shortcode' ), 10, 3 );
	}

	/**
	 * Filter the servings shortcode.
	 *
	 * @since	5.6.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function shortcode( $output, $atts, $recipe ) {
		// Output.
		$classes = array(
			'wprm-recipe-servings',
			'wprm-recipe-details',
			'wprm-recipe-servings-' . $recipe->id(),
			'wprm-recipe-servings-adjustable-' . $atts['adjustable'],
			'wprm-block-text-' . $atts['text_style'],
		);

		// Style in Preview.
		$output = '';
		if ( $atts['is_template_editor_preview'] ) {
			switch ( $atts['adjustable'] ) {
				case 'text-buttons':
					$button_style = '';
					$button_style .= 'background-color: ' . $atts['button_background'] . ';';
					$button_style .= 'border-color: ' . $atts['button_background'] . ';';
					$button_style .= 'color: ' . $atts['button_accent'] . ';';
					$button_style .= 'border-radius: ' . $atts['button_radius'] . ';';

					$input_style = 'border-color: ' . $atts['button_background'] . ';';

					$classes[] = 'wprm-recipe-servings-text-buttons-container';

					$output .= '<span class="' . implode( ' ', $classes ) . '">';
					$output .= '<span class="wprm-recipe-servings-decrement wprm-recipe-servings-change" style="' . $button_style . '">–</span>';
					$output .= '<input type="text" value="' . esc_attr( $recipe->servings() ) . '" class="wprm-recipe-servings"  data-recipe="' . $recipe->id() . '" aria-label="' . esc_attr__( 'Adjust recipe servings', 'wp-recipe-maker' ) . '" style="' . $input_style . '">';
					$output .= '<span class="wprm-recipe-servings-increment wprm-recipe-servings-change" style="' . $button_style . '">+</span>';
					$output .= '</span>';
					break;
				case 'text':
					$output = '<input type="number" value="' . esc_attr( $recipe->servings() ) . '" class="' . implode( ' ', $classes ) . '" data-recipe="' . $recipe->id() . '" aria-label="' . esc_attr__( 'Adjust recipe servings', 'wp-recipe-maker' ) . '">';
					break;
				case 'tooltip':
					$output = '<a class="' . implode( ' ', $classes ) . '" data-recipe="' . $recipe->id() . '">' . $recipe->servings() . '</a>';
					break;
			}
		}

		// Optional initial servings.
		$initial_servings = self::get_initial_servings();

		// No output yet, so we're not previewing.
		if ( ! $output ) {
			$style_data = '';
			if ( 'text-buttons' === $atts['adjustable'] ) {
				$style_data = ' data-button-background="' . esc_attr( $atts['button_background'] ) .'" data-button-accent="' . esc_attr( $atts['button_accent'] ) .'" data-button-radius="' . esc_attr( $atts['button_radius'] ) .'"';
			}

			$output = '<span class="' . implode( ' ', $classes ) . '" data-initial-servings="' . esc_attr( $initial_servings ) . '" data-recipe="' . $recipe->id() . '" aria-label="' . esc_attr__( 'Adjust recipe servings', 'wp-recipe-maker' ) . '"' . $style_data .'>' . $recipe->servings() . '</span>';
		}

		if ( (bool) $atts['label_container'] ) {
			$unit = WPRM_SC_Servings_Unit::shortcode( $atts );
			if ( $unit ) {
				$output = '<span class="wprm-recipe-servings-with-unit">' . $output . ' ' . $unit . '</span>';
			}

			$output = WPRM_Shortcode_Helper::get_label_container( $atts, 'servings', $output );
		}

		return $output;
	}

	/**
	 * Filter the adjustable servings shortcode.
	 *
	 * @since	5.6.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function adjustable_shortcode( $output, $atts, $recipe ) {
		if ( ! $recipe || ( ! $recipe->servings() && ! WPRM_Settings::get( 'adjustable_servings_without_servings' ) ) ) {
			return '';
		}

		$recipe_servings = $recipe->servings() ? $recipe->servings() : 1;

		$classes = array(
			'wprm-recipe-adjustable-servings-container',
			'wprm-recipe-adjustable-servings-' . $recipe->id() . '-container',
			'wprm-toggle-container',
			'wprm-block-text-' . $atts['text_style'],
		);

		// Custom style.
		$style = '';
		$style .= 'background-color: ' . $atts['button_background'] . ';';
		$style .= 'border-color: ' . $atts['button_accent'] . ';';
		$style .= 'color: ' . $atts['button_accent'] . ';';
		$style .= 'border-radius: ' . $atts['button_radius'] . ';';

		// Buttons.
		$buttons = array();
		$serving_options = explode( ';', $atts['serving_options'] );

		foreach ( $serving_options as $option ) {
			$option = trim( $option );
			$value = WPRM_Recipe_Parser::parse_quantity( $option );

			if ( 0 < $value ) {
				$buttons[ '' . $value ] = $option; // Make sure key is string or floats get truncated.
			}
		}

		// Set default buttons if not at least 2 valid options set.
		if ( 2 > count( $buttons ) ) {
			$buttons = array(
				'1' => '1x',
				'2' => '2x',
				'3' => '3x',
			);
		}

		// Optional "any value".
		$any_value_label = trim( $atts['serving_options_any_value'] );
		if ( $any_value_label ) {
			$buttons['?'] = $any_value_label;
		}

		$buttons_output = '';

		$first_button = true;
		foreach ( $buttons as $multiplier => $button_text ) {
			// Button style.
			$button_style = '';
			$button_style .= 'background-color: ' . $atts['button_accent'] . ';';
			$button_style .= 'color: ' . $atts['button_background'] . ';';

			if ( ! $first_button ) {
				$border = is_rtl() ? 'border-right' : 'border-left';
				$button_style .= $border . ': 1px solid ' . $atts['button_accent'] . ';';
			} else {
				$first_button = false;
			}

			if ( '?' === $multiplier ) {
				$aria_label = __( 'Change servings to...', 'wp-recipe-maker' );
			} else {
				$aria_label = __( 'Adjust servings by', 'wp-recipe-maker' ) . ' ' . $button_text;
			}

			$active = 1 === $multiplier ? ' wprm-toggle-active' : '';
			$buttons_output .= '<button href="#" class="wprm-recipe-adjustable-servings wprm-toggle' . $active . '" data-multiplier="' . esc_attr( $multiplier ) . '" data-servings="' . esc_attr( $recipe_servings ) . '" data-recipe="' . esc_attr( $recipe->id() ) . '" style="' . $button_style .'" aria-label="' . $aria_label . '">' . $button_text . '</button>';
		}

		// Optional initial servings.
		$initial_servings = self::get_initial_servings();

		// Output.
		$output = '<div class="' . implode( ' ', $classes ) . '" data-initial-servings="' . esc_attr( $initial_servings ) . '" style="' . $style . '">' . $buttons_output . '</div>';

		return $output;
	}

	/**
	 * Get initial servings set in the URL.
	 *
	 * @since	8.1.0
	 */
	public static function get_initial_servings() {
		// Check if URL parameter is set.
		$initial_servings = '';
		if ( WPRM_Settings::get( 'adjustable_servings_url' ) ) {
			$param = trim( WPRM_Settings::get( 'adjustable_servings_url_param' ) );
			$param = $param ? $param : 'servings';

			if ( isset( $_GET[ $param ] ) ) {
				$param_servings = floatval( $_GET[ $param ] );
				
				if ( $param_servings ) {
					$initial_servings = $param_servings;
				}
			}
		}

		return $initial_servings;
	}
}

WPRMP_SC_Servings::init();