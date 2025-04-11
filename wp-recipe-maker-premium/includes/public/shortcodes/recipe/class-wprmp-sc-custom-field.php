<?php
/**
 * Handle the Premium custom field shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium custom field shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Custom_Field {
	public static function init() {
		add_filter( 'wprm_recipe_custom_field_shortcode', array( __CLASS__, 'shortcode' ), 10, 3 );
	}

	/**
	 * Add to collection shortcode.
	 *
	 * @since	5.6.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function shortcode( $output, $atts, $recipe ) {
		$output = '';

		if ( ! WPRM_Addons::is_active( 'custom-fields' ) ) {
			return $output;
		}

		$value = WPRMPCF_Fields::get( $recipe, $atts['key'] );
		$custom_field_options = WPRMPCF_Manager::get_custom_field( $atts['key'] );
		if ( ! $recipe || false === $value || '' === $value || ! $custom_field_options ) {
			return '';
		}

		// Output.
		$classes = array(
			'wprm-recipe-custom-field',
			'wprm-recipe-custom-field-type-' . $custom_field_options['type'],
			'wprm-recipe-custom-field-key-' . $atts['key'],
			'wprm-block-text-' . $atts['text_style'],
		);

		switch( $custom_field_options['type'] ) {
			case 'email':
				$output = '<a href="mailto:' . esc_attr( $value ). '" class="' . implode( ' ', $classes ) . '">' . $value . '</a>';
				break;
			case 'link':
				$link_text = $value;
				$link_icon = '';
				$link_style = '';

				if ( $atts['link_text'] ) {
					$link_text = $atts['link_text'];

					// Get optional icon.
					if ( $atts['link_icon'] ) {
						$link_icon = WPRM_Icon::get( $atts['link_icon'], $atts['link_icon_color'] );

						if ( $link_icon ) {
							$link_icon = '<span class="wprm-recipe-icon wprm-recipe-link-icon">' . $link_icon . '</span> ';
						}
					}

					// Output.
					$classes = array(
						'wprm-link',
						'wprm-recipe-link',
						'wprm-block-text-' . $atts['link_text_style'],
					);

					$link_style = ' style="';
					$link_style .= 'color: ' . $atts['link_text_color'] . ';';
					if ( 'text' !== $atts['link_style'] ) {
						$classes[] = 'wprm-recipe-link-' . $atts['link_style'];
						$classes[] = 'wprm-color-accent';

						$link_style .= 'background-color: ' . $atts['link_button_color'] . ';';
						$link_style .= 'border-color: ' . $atts['link_border_color'] . ';';
						$link_style .= 'border-radius: ' . $atts['link_border_radius'] . ';';
						$link_style .= 'padding: ' . $atts['link_vertical_padding'] . ' ' . $atts['link_horizontal_padding'] . ';';
					}
					$link_style .= '"';
				}

				$nofollow = 'nofollow' === $atts['link_nofollow'] ? ' rel="nofollow"' : '';
				$output = '<a href="' . esc_attr( $value ). '" target="' . $atts['link_target']. '" class="' . implode( ' ', $classes ) . '"' . $nofollow . $link_style . '>' . $link_icon . $link_text . '</a>';
				break;
			case 'text':
				$output = '<span class="' . implode( ' ', $classes ) . '">' . $value . '</span>';
				break;
			case 'textarea':
				$output = '<div class="' . implode( ' ', $classes ) . '">' . do_shortcode( $value ) . '</div>';
				break;
			case 'image':
				if ( $value['id'] ) {
					$output = '<div class="' . implode( ' ', $classes ) . '">' . self::image( $value['id'], $atts['image_size'] ) . '</div>';
				} else {
					$output = '';
				}
				break;
		}

		$output = WPRM_Shortcode_Helper::get_label_container( $atts, 'custom-field', $output );

		// Optional header.
		$header = WPRM_Shortcode_Helper::get_section_header( $atts, 'text' );
		if ( $header ) {
			$output = '<div class="wprm-recipe-custom-field-container">' . $header . $output . '</div>';
		}

		return $output;
	}

	/**
	 * Output an custom field image.
	 *
	 * @since	5.2.0
	 * @param	mixed $id   ID of the image to output.
	 * @param	mixed $size Image size to use.
	 */
	private static function image( $id, $size ) {
		preg_match( '/^(\d+)x(\d+)$/i', $size, $match );
		if ( ! empty( $match ) ) {
			$size = array( intval( $match[1] ), intval( $match[2] ) );
		}

		$img = wp_get_attachment_image( $id, $size );

		// Prevent instruction image from getting stretched in Gutenberg preview.
		if ( isset( $GLOBALS['wp']->query_vars['rest_route'] ) && '/wp/v2/block-renderer/wp-recipe-maker/recipe' === $GLOBALS['wp']->query_vars['rest_route'] ) {
			$image_data = wp_get_attachment_image_src( $id, $size );
			if ( $image_data[1] ) {
				$style .= 'max-width: ' . $image_data[1] . 'px;';

				if ( false !== stripos( $img, ' style="' ) ) {
					$img = str_ireplace( ' style="', ' style="' . $style, $img );
				} else {
					$img = str_ireplace( '<img ', '<img style="' . $style . '" ', $img );
				}
			}
		}

		return $img;
	}
}

WPRMP_SC_Custom_Field::init();