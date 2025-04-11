<?php
/**
 * Handle the Premium recipe tag shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      6.4.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium recipe tag shortcode.
 *
 * @since      6.4.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Tag {
	public static function init() {
		add_filter( 'wprm_recipe_tag_shortcode_term', array( __CLASS__, 'display' ), 10, 3 );
		add_filter( 'wprm_recipe_tag_shortcode_link', array( __CLASS__, 'link' ), 10, 2 );
	}

	/**
	 * Alter term output display style.
	 *
	 * @since	6.7.0
	 * @param	mixed $output 		Current output.
	 * @param	array $term	 		Term we're outputting.
	 * @param	array $atts	 		Shortcode attributes.
	 */
	public static function display( $output, $term, $atts ) {
		if ( 'images' === $atts['display_style'] || 'text_images' === $atts['display_style'] ) {
			$classes = array(
				'wprm-recipe-tag-term',
				'wprm-recipe-tag-' . $atts['display_style'],
				'wprm-recipe-tag-image-align-' . esc_attr( $atts['image_position'] ),
			);

			$term_id = is_object( $term ) && isset( $term->term_id ) ? intval( $term->term_id ) : 0;

			if ( $term_id ) {
				// Equipment Image.
				$image_id = intval( get_term_meta( $term_id, 'wprmp_term_image_id', true ) );
				$classes[] = $image_id ? 'wprm-recipe-tag-term-has-image' : 'wprm-recipe-tag-term-no-image';

				$image_output = '';
				if ( $image_id ) {
					$size = $atts['image_size'];
					preg_match( '/^(\d+)x(\d+)$/i', $atts['image_size'], $match );
					if ( ! empty( $match ) ) {
						$size = array( intval( $match[1] ), intval( $match[2] ) );
					}
	
					$img = wp_get_attachment_image( $image_id, $size );
	
					// Disable term image pinning.
					if ( WPRM_Settings::get( 'pinterest_nopin_term_image' ) ) {
						$img = str_ireplace( '<img ', '<img data-pin-nopin="true" ', $img );
					}
	
					$image_output = '<div class="wprm-recipe-tag-term-image">' . $img . '</div>';
				}

				$text_output = ! $image_output || 'text_images' === $atts['display_style'] ? '<div class="wprm-recipe-tag-term-text">' . $output . '</div>' : '';

				$output = '<div class="' . implode( ' ', $classes ). '">' . $image_output . $text_output . '</div>';
			}
		}

		return $output;
	}

	/**
	 * Add ingredient links.
	 *
	 * @since	6.4.0
	 * @param	mixed $output 		Current output.
	 * @param	array $term	 		Term we're outputting.
	 */
	public static function link( $output, $term ) {
		$term_id = is_object( $term ) && isset( $term->term_id ) ? intval( $term->term_id ) : 0;

		if ( $term_id ) {
			// Easy Affiliate Links integration.
			if ( class_exists( 'EAFL_Link_Manager' ) ) {
				$eafl = get_term_meta( $term_id, 'wprmp_term_eafl', true );

				if ( $eafl ) {
					$eafl_link = EAFL_Link_Manager::get_link( $eafl );

					if ( $eafl_link ) {
						return do_shortcode( '[eafl id="' .  $eafl . '"]' . $output . '[/eafl]' );
					}
				}
			}

			// Regular link.
			$link = get_term_meta( $term_id, 'wprmp_term_link', true );
			$link_nofollow = get_term_meta( $term_id, 'wprmp_term_link_nofollow', true );

			if ( $link ) {
				$link_output = WPRMP_Links::get( $link, $link_nofollow, $output, 'term' );

				if ( $link_output ) {
					return $link_output;
				}
			}
		}

		return $output;
	}
}

WPRMP_SC_Tag::init();