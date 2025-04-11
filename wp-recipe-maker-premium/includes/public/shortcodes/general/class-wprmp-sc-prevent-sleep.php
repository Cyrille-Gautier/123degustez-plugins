<?php
/**
 * Handle the Premium prevent sleep shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      7.0.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/general
 */

/**
 * Handle the Premium prevent sleep shortcode.
 *
 * @since      7.0.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/general
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Prevent_Sleep {
	public static function init() {
		add_filter( 'wprm_prevent_sleep_shortcode', array( __CLASS__, 'shortcode' ), 10, 2 );
	}

	/**
	 * Add the call to action.
	 *
	 * @since	5.6.0	 
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 */
	public static function shortcode( $output, $atts ) {
		$output = '';

		$uid = rand();

		// Hide by default (unless in Template Editor).
		$style = ' style="display:none;"';
		if ( $atts['is_template_editor_preview'] ) {
			$style = '';
		}

		$output = '<div class="wprm-prevent-sleep wprm-toggle-switch-container"' . $style . '>';

		$output .= WPRM_Shortcode_Helper::get_toggle_switch( $atts, array(
			'uid' => $uid,
			'class' => 'wprm-prevent-sleep-checkbox',
		) );

		if ( $atts['label'] ) {
			$output .= '<label for="wprm-prevent-sleep-checkbox-' . $uid . '" class="wprm-prevent-sleep-label wprm-block-text-' . $atts['label_style'] . '">' . $atts['label'] . '</label>';
		}
		if ( $atts['description'] ) {
			$output .= '<span class="wprm-prevent-sleep-description wprm-block-text-' . $atts['description_style'] . '">' . $atts['description'] . '</span>';
		}

		$output .= '</div>';

		return $output;
	}
}

WPRMP_SC_Prevent_Sleep::init();