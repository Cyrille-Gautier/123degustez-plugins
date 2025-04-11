<?php
/**
 * Handle the Premium add to shopping list shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      8.3.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium add to shopping list shortcode.
 *
 * @since      8.3.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Add_To_Shopping_List {
	public static function init() {
		add_filter( 'wprm_recipe_add_to_shopping_list_shortcode', array( __CLASS__, 'shortcode' ), 10, 3 );
	}

	/**
	 * Add to collection shortcode.
	 *
	 * @since	8.3.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function shortcode( $output, $atts, $recipe ) {
		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe || ! $recipe->id() ) {
			return '';
		}

		// Make sure link to collections feature has been set.
		$shopping_list_link = WPRM_Settings::get( 'quick_access_shopping_list_link' );
		if ( ! WPRM_Addons::is_active( 'recipe-collections' ) || ! $shopping_list_link ) {
			return '';
		}

		// Check if user needs to be logged in.
		if ( ! is_user_logged_in() && 'logged_in' === WPRM_Settings::get( 'quick_access_shopping_list_access' ) && 'hide' === WPRM_Settings::get( 'recipe_collections_add_button_not_logged_in' ) ) {
			return;
		}

		$in_collection = $recipe->in_collection( 'temp' );

		// Get optional icon.
		$icon = '';
		if ( $atts['icon'] ) {
			$icon = WPRM_Icon::get( $atts['icon'], $atts['icon_color'] );

			if ( $icon ) {
				$icon = '<span class="wprm-recipe-icon wprm-recipe-add-to-shopping-list-icon wprm-recipe-not-in-shopping-list">' . $icon . '</span> ';
			}
		}
		$icon_added = '';
		if ( $atts['icon_added'] ) {
			$icon_added = WPRM_Icon::get( $atts['icon_added'], $atts['icon_color'] );

			if ( $icon_added ) {
				$icon_added = '<span class="wprm-recipe-icon wprm-recipe-add-to-shopping-list-icon wprm-recipe-in-shopping-list">' . $icon_added . '</span> ';
			}
		}

		// Output.
		$classes = array(
			'wprm-recipe-add-to-shopping-list',
			'wprm-recipe-link',
			'wprm-block-text-' . $atts['text_style'],
		);

		// Disabled class if button won't work.
		if ( ! is_user_logged_in() && 'logged_in' === WPRM_Settings::get( 'quick_access_shopping_list_access' ) && 'disabled' === WPRM_Settings::get( 'recipe_collections_add_button_not_logged_in' ) ) {
			$classes[] = 'wprm-recipe-link-disabled';
		}

		$style = 'color: ' . $atts['text_color'] . ';';
		if ( 'text' !== $atts['style'] ) {
			$classes[] = 'wprm-recipe-add-to-shopping-list-' . $atts['style'];
			$classes[] = 'wprm-recipe-link-' . $atts['style'];
			$classes[] = 'wprm-color-accent';

			$style .= 'background-color: ' . $atts['button_color'] . ';';
			$style .= 'border-color: ' . $atts['border_color'] . ';';
			$style .= 'border-radius: ' . $atts['border_radius'] . ';';
			$style .= 'padding: ' . $atts['vertical_padding'] . ' ' . $atts['horizontal_padding'] . ';';
		}

		// Backwards compatibility.
		if ( 'legacy' === WPRM_Settings::get( 'recipe_template_mode' ) ) {
			$style = '';
		}

		$output = '';
		if ( ! $in_collection ) {
			$collections_data = json_encode( WPRMPRC_Manager::get_collections_data_for_recipe( $recipe ) );

			$output .= '<a href="' . esc_url( $shopping_list_link ) . '" style="' . $style . '" class="wprm-recipe-not-in-shopping-list ' . implode( ' ', $classes ) . '" data-recipe-id="' . esc_attr( $recipe->id() ) . '" data-recipe="' . esc_attr( $collections_data ) . '">' . $icon . __( $atts['text'], 'wp-recipe-maker' ) . '</a>';
			$style .= 'display: none;';
		}
		$output .= '<a href="' . esc_url( $shopping_list_link ) . '" style="' . $style . '" class="wprm-recipe-in-shopping-list ' . implode( ' ', $classes ) . '" data-recipe-id="' . esc_attr( $recipe->id() ) . '" data-text-added="">' . $icon_added . __( $atts['text_added'], 'wp-recipe-maker' ) . '</a>';

		return $output;
	}
}

WPRMP_SC_Add_To_Shopping_List::init();