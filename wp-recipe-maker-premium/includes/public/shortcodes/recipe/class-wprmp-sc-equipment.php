<?php
/**
 * Handle the Premium recipe equipment shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 */

/**
 * Handle the Premium recipe equipment shortcode.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_SC_Equipment {
	public static function init() {
		add_filter( 'wprm_recipe_equipment_shortcode_checkbox', array( __CLASS__, 'checkbox' ) );
		add_filter( 'wprm_recipe_equipment_shortcode_link', array( __CLASS__, 'link' ), 10, 2 );
		add_filter( 'wprm_recipe_equipment_shortcode_display', array( __CLASS__, 'display' ), 10, 3 );
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
	 * Add equipment links.
	 *
	 * @since	5.6.0
	 * @param	mixed $output 	Current output.
	 * @param	array $equiment Equipment we're outputting.
	 */
	public static function link( $output, $equipment ) {
		if ( isset( $equipment['id'] ) && $equipment['id'] ) {
			// Easy Affiliate Links integration.
			if ( class_exists( 'EAFL_Link_Manager' ) ) {
				$eafl = get_term_meta( $equipment['id'], 'wprmp_equipment_eafl', true );

				if ( $eafl ) {
					$eafl_link = EAFL_Link_Manager::get_link( $eafl );

					if ( $eafl_link ) {
						return do_shortcode( '[eafl id="' .  $eafl . '"]' . $output . '[/eafl]' );
					}
				}
			}

			// Regular link.
			$link = get_term_meta( $equipment['id'], 'wprmp_equipment_link', true );
			$link_nofollow = get_term_meta( $equipment['id'], 'wprmp_equipment_link_nofollow', true );

			if ( $link ) {
				$link_output = WPRMP_Links::get( $link, $link_nofollow, $output, 'equipment' );

				if ( $link_output ) {
					return $link_output;
				}
			}
		}

		return $output;
	}

	/**
	 * Change the equipment display.
	 *
	 * @since	5.6.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function display( $output, $atts, $recipe ) {
		switch( $atts['display_style'] ) {
			case 'images':
				$output .= self::display_images( $atts, $recipe );
				break;
			case 'grid':
				$output .= self::display_grid( $atts, $recipe );
				break;
		}

		return $output;
	}

	/**
	 * Get the output for the images display.
	 *
	 * @since	8.0.0
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function display_images( $atts, $recipe ) {
		$output = '';

		$classes = array(
			'wprm-recipe-equipment',
			'wprm-recipe-equipment-images',
			'wprm-recipe-equipment-images-align-' . esc_attr( $atts['image_alignment'] ),
		);

		$output .= '<div class="' . implode( ' ', $classes ). '">';

		foreach ( $recipe->equipment() as $equipment ) {
			$output .= self::get_equipment_item_image_output( $equipment, $atts );
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Get the output for the images display.
	 *
	 * @since	8.0.0
	 * @param	array $atts   Options passed along with the shortcode.
	 * @param	mixed $recipe Recipe the shortcode is getting output for.
	 */
	public static function display_grid( $atts, $recipe ) {
		$output = '';


		$grid_columns = intval( $atts['grid_columns'] );
		$classes = array(
			'wprm-recipe-equipment',
			'wprm-recipe-equipment-grid',
			'wprm-recipe-equipment-grid-columns-' . $grid_columns,
		);

		$output .= '<div class="' . implode( ' ', $classes ). '">';

		$output .= '<div class="wprm-recipe-equipment-grid-row">';

		$item_nbr = 1;
		foreach ( $recipe->equipment() as $equipment ) {
			$output .= self::get_equipment_item_image_output( $equipment, $atts );

			if ( 0 === $item_nbr % $grid_columns && $item_nbr < count( $recipe->equipment() ) ) {
				$output .= '</div>';
				$output .= '<div class="wprm-recipe-equipment-grid-row">';
			}
			$item_nbr++;
		}

		$output .= '</div>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Get the output for an equipment item with image.
	 *
	 * @since	8.0.0
	 * @param	mixed $output Current output.
	 * @param	array $atts   Options passed along with the shortcode.
	 */
	public static function get_equipment_item_image_output( $equipment, $atts ) {
		$output = '';

		// Equipment Image.
		$image_id = intval( get_term_meta( $equipment['id'], 'wprmp_equipment_image_id', true ) );
		$class = $image_id ? 'wprm-recipe-equipment-item-has-image' : 'wprm-recipe-equipment-item-no-image';

		$output .= '<div class="wprm-recipe-equipment-item ' . $class . '">';
		
		$equipment_output = '';

		if ( $image_id ) {
			preg_match( '/^(\d+)x(\d+)$/i', $atts['image_size'], $match );
			if ( ! empty( $match ) ) {
				$size = array( intval( $match[1] ), intval( $match[2] ) );
			}

			$img = wp_get_attachment_image( $image_id, $size );

			// Disable equipment image pinning.
			if ( WPRM_Settings::get( 'pinterest_nopin_equipment_image' ) ) {
				$img = str_ireplace( '<img ', '<img data-pin-nopin="true" ', $img );
			}

			$image_output = '<div class="wprm-recipe-equipment-image">' . $img . '</div>';
			$equipment_output .= self::link( $image_output, $equipment );
		}

		// Equipment Affiliate HTML.
		$affiliate_html = get_term_meta( $equipment['id'], 'wprmp_equipment_affiliate_html', true );

		if ( $affiliate_html ) {
			$output .= '<div class="wprm-recipe-equipment-affiliate-html">' . do_shortcode( $affiliate_html ) . '</div>';
		}

		// Maybe add amount or notes.
		$name = self::link( $equipment['name'], $equipment );
		if ( isset( $equipment['amount'] ) && $equipment['amount'] ) {
			$name = $equipment['amount'] . ' ' . $name;
		}
		if ( isset( $equipment['notes'] ) && $equipment['notes'] ) {
			$name = $name . ' ' . $equipment['notes'];
		}

		// Equipment Name.
		$equipment_output .= '<div class="wprm-recipe-equipment-name">' . $name . '</div>';

		$output .= $equipment_output;
		$output .= '</div>';

		return $output;
	}
}

WPRMP_SC_Equipment::init();