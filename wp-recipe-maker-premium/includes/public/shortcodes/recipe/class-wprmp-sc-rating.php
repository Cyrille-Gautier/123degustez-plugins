<?php
/**
 * Handle the Premium rating shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium rating shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Rating {
	private static $uid = 0;

	public static function init() {
		add_filter( 'wprm_recipe_rating_shortcode_stars', array( __CLASS__, 'stars' ), 10, 8 );
	}

	/**
	 * Stars shortcode.
	 *
	 * @since	5.6.0
	 * @param	mixed 	$output		Current output.
	 * @param   mixed 	$recipe   	Recipe to display the rating for.
	 * @param   array 	$rating   	Rating to display.
	 * @param   mixed	$icon 	   	Icon to use for the rating.
	 * @param   boolean $voteable 	Wether the user is allowed to vote.
	 * @param   mixed	$color 	   	Color for the stars.
	 * @param   mixed	$atts		Options passed along with the shortcode.
	 */
	public static function stars( $output, $recipe, $rating, $voteable, $icon, $color, $atts ) {
		if ( WPRM_Settings::get( 'features_user_ratings' ) ) {
			$rating_value = $rating['average'];

			// UID for these stars.
			$id = 'wprm-recipe-user-rating-' . self::$uid;
			self::$uid++;

			// Only output when there is an actual rating or users can rate.
			if ( ! $voteable && ! $rating_value ) {
				return false;
			}

			$output = '';

			// Output style for star color.
			$output .= '<style>';
			$output .= '#' . $id . ' .wprm-rating-star.wprm-rating-star-full svg * { fill: ' . $color . '; }';
			$output .= '#' . $id . ' .wprm-rating-star.wprm-rating-star-33 svg * { fill: url(#' . $id . '-33); }';
			$output .= '#' . $id . ' .wprm-rating-star.wprm-rating-star-50 svg * { fill: url(#' . $id . '-50); }';
			$output .= '#' . $id . ' .wprm-rating-star.wprm-rating-star-66 svg * { fill: url(#' . $id . '-66); }';
			$output .= 'linearGradient#' . $id . '-33 stop { stop-color: ' . $color . '; }';
			$output .= 'linearGradient#' . $id . '-50 stop { stop-color: ' . $color . '; }';
			$output .= 'linearGradient#' . $id . '-66 stop { stop-color: ' . $color . '; }';
			$output .= '</style>';

			// Definitions for quarter and half stars.
			$output .= '<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="display:block;width:0px;height:0px">';
			if ( is_rtl() ) {
				$output .= '<defs><linearGradient id="' . $id .'-33"><stop offset="0%" stop-opacity="0" /><stop offset="66%" stop-opacity="0" /><stop offset="66%" stop-opacity="1" /><stop offset="100%" stop-opacity="1" /></linearGradient></defs>';
				$output .= '<defs><linearGradient id="' . $id .'-50"><stop offset="0%" stop-opacity="0" /><stop offset="50%" stop-opacity="0" /><stop offset="50%" stop-opacity="1" /><stop offset="100%" stop-opacity="1" /></linearGradient></defs>';
				$output .= '<defs><linearGradient id="' . $id .'-66"><stop offset="0%" stop-opacity="0" /><stop offset="33%" stop-opacity="0" /><stop offset="33%" stop-opacity="1" /><stop offset="100%" stop-opacity="1" /></linearGradient></defs>';
			} else {
				$output .= '<defs><linearGradient id="' . $id .'-33"><stop offset="0%" stop-opacity="1" /><stop offset="33%" stop-opacity="1" /><stop offset="33%" stop-opacity="0" /><stop offset="100%" stop-opacity="0" /></linearGradient></defs>';
				$output .= '<defs><linearGradient id="' . $id .'-50"><stop offset="0%" stop-opacity="1" /><stop offset="50%" stop-opacity="1" /><stop offset="50%" stop-opacity="0" /><stop offset="100%" stop-opacity="0" /></linearGradient></defs>';
				$output .= '<defs><linearGradient id="' . $id .'-66"><stop offset="0%" stop-opacity="1" /><stop offset="66%" stop-opacity="1" /><stop offset="66%" stop-opacity="0" /><stop offset="100%" stop-opacity="0" /></linearGradient></defs>';
			}
			$output .= '</svg>';

			// Get correct class.
			$classes = array(
				'wprm-recipe-rating',
				'wprm-user-rating',
			);

			if ( 'stars-details' === $atts['display'] ) {
				$classes[] = 'wprm-recipe-rating-' . $atts['style'];
			}

			$functions = '';
			if ( $voteable && WPRMP_User_Rating::is_user_allowed_to_vote() ) {
				$decimals = isset( $atts['average_decimals'] ) ? $atts['average_decimals'] : 2;
				$user_rating = isset( $rating['user'] ) ? $rating['user'] : 0;

				$classes[] = 'wprm-user-rating-allowed';
				$data = ' data-recipe="' . $recipe->id() . '" data-average="' . $rating['average'] . '" data-count="' . $rating['count'] . '" data-total="' . $rating['total'] . '" data-user="' . $user_rating . '" data-decimals="' . $decimals .'"';
				
				$functions .= ' onmouseenter="window.WPRecipeMaker.userRating.enter(this)"';
				$functions .= ' onfocus="window.WPRecipeMaker.userRating.enter(this)"';
				$functions .= ' onmouseleave="window.WPRecipeMaker.userRating.leave(this)"';
				$functions .= ' onblur="window.WPRecipeMaker.userRating.leave(this)"';
				$functions .= ' onclick="window.WPRecipeMaker.userRating.click(this, event)"';
				$functions .= ' onkeypress="window.WPRecipeMaker.userRating.click(this, event)"';
			} else {
				$data = '';
			}

			// Output stars.
			$output .= '<div id="' . $id . '" class="' . implode( ' ', $classes ) . '"' . $data . '>';

			$stars = array(
				1 => __( 'Rate this recipe 1 out of 5 stars', 'wp-recipe-maker' ),
				2 => __( 'Rate this recipe 2 out of 5 stars', 'wp-recipe-maker' ),
				3 => __( 'Rate this recipe 3 out of 5 stars', 'wp-recipe-maker' ),
				4 => __( 'Rate this recipe 4 out of 5 stars', 'wp-recipe-maker' ),
				5 => __( 'Rate this recipe 5 out of 5 stars', 'wp-recipe-maker' ),
			);

			foreach ( $stars as $i => $label ) {
				$star_classes = array(
					'wprm-rating-star',
					'wprm-rating-star-' . $i,
				);

				// Get star class.
				if ( $i <= $rating_value ) {
					$star_classes[] = 'wprm-rating-star-full';
				} else {
					$difference = $rating_value - $i + 1;
					if ( 0 < $difference && $difference <= 0.33 ) {
						$star_classes[] = 'wprm-rating-star-33';
					} elseif ( 0 < $difference && $difference <= 0.5 ) {
						$star_classes[] = 'wprm-rating-star-50';
					} elseif( 0 < $difference && $difference <= 0.66 ) {
						$star_classes[] = 'wprm-rating-star-66';
					} elseif( 0 < $difference && $difference <= 1 ) {
						$star_classes[] = 'wprm-rating-star-full';
					} else {
						$star_classes[] = 'wprm-rating-star-empty';
					}
				}

				$accessibility = '';
				if ( $voteable && WPRMP_User_Rating::is_user_allowed_to_vote() ) {
					$accessibility = ' role="button" tabindex="0" aria-label="' . $label . '"';
				}

				$output .= '<span class="' . implode( ' ', $star_classes ) . '" data-rating="' . $i . '" data-color="' . $color . '"' . $accessibility . $functions . '>';
				$output .= apply_filters( 'wprm_recipe_rating_star_icon', WPRM_Icon::get( $icon, $color ) );
				$output .= '</span>';
			}
		}

		return $output;
	}
}

WPRMP_SC_Rating::init();