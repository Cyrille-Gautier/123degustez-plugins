<?php
/**
 * Handle the Premium private notes shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      7.7.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium private notes  shortcode.
 *
 * @since      7.7.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Private_Notes {
	public static function init() {
		add_filter( 'wprm_private_notes_shortcode', array( __CLASS__, 'shortcode' ), 10, 3 );
	}

	/**
	 * Private notes shortcode.
	 *
	 * @since	7.7.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function shortcode( $output, $atts, $recipe ) {
		$output = '';

		// Check if only open for logged in users.
		$show_call_to_action = false;
		if ( ! is_user_logged_in() && 'logged_in' === WPRM_Settings::get( 'private_notes_access' ) ) {
			if ( 'hide' === WPRM_Settings::get( 'private_notes_not_logged_in' ) ) {
				return '';
			} else {
				$show_call_to_action = true;
			}
		}

		$output .= WPRM_Shortcode_Helper::get_section_header( $atts, 'private-notes' );

		// Get current notes for logged in user.
		$notes = WPRMP_Private_Notes::get( $recipe->id() );

		// Output.
		$classes = array(
			'wprm-private-notes-container',
			'wprm-block-text-' . $atts['text_style'],
		);

		if ( $show_call_to_action ) {
			$classes[] = 'wprm-private-notes-container-disabled';
		}

		$output .= '<div class="' . implode( ' ', $classes ) . '" data-recipe="' . esc_attr( $recipe->id() ) . '">';

		if ( $show_call_to_action ) {
			$message = WPRM_Settings::get( 'private_notes_not_logged_in_message' );
			$output .= '<div class="wprm-private-notes-call-to-action">' . $message . '</div>';
		} else {
			$output .= '<div class="wprm-private-notes-placeholder"><a href="#" role="button">' . esc_html( $atts['placeholder'] ) . '</a></div>';
			$output .= '<div class="wprm-private-notes-user"></div>';
			$output .= '<textarea class="wprm-private-notes-input" aria-label="' . __( 'Your own private notes about this recipe', 'wp-recipe-maker-premium' ) .'">' . esc_html( $notes ) . '</textarea>';
		}

		$output .= '</div>';

		return $output;
	}
}

WPRMP_SC_Private_Notes::init();