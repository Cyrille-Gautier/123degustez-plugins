<?php
/**
 * Handle the export of recipes to JSON.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.2.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Handle the export of recipes to JSON.
 *
 * @since      5.2.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_Export_JSON {

	/**
	 * Export recipes to JSON.
	 *
	 * @since	5.2.0
	 * @param	int 	$recipe_ids IDs of the recipes to export.
	 * @param	mixed 	$type		Export type to use.
	 */
	public static function bulk_edit_export( $recipe_ids, $type = 'recipe' ) {
		$export = array();

		foreach ( $recipe_ids as $recipe_id ) {
			$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );

			if ( $recipe ) {
				$export[] = self::get_data_for_export( $recipe, $type );
			}
		}

		$json = json_encode( $export, JSON_PRETTY_PRINT );
		
		// Create file.
		$upload_dir = wp_upload_dir();
		$slug = 'wprm';
		$dir = trailingslashit( trailingslashit( $upload_dir['basedir'] ) . $slug );
		$url = $upload_dir['baseurl'] . '/' . $slug . '/';

		wp_mkdir_p( $dir );

		$filename = 'WPRM Recipe Export.json';
		$filepath = $dir . $filename;

		$f = fopen( $filepath, 'wb' );
		if ( ! $f ) {
			wp_die( 'Unable to create recipe export file. Check file permissions' );
		}

		fwrite( $f, $json );
		fclose( $f );

		return array(
			'result' => __( 'Your recipes have been exported to:', 'wp-recipe-maker' ) . '<br/><a href="' . esc_url( $url . $filename ) . '?t=' . time() . '" target="_blank">' . $url . $filename . '</a>',
		);
	}

	/**
	 * Get recipe data for export.
	 *
	 * @since	7.1.0
	 * @param	mixed $recipe Recipe to export.
	 * @param	mixed 	$type		Export type to use.
	 */
	public static function get_data_for_export( $recipe, $type ) {
		$data = $recipe->get_data();
		$data = self::clean_up_recipe_for_export( $data );

		if ( 'with_parent' === $type ) {
			$parent = $recipe->parent_post();

			if ( ! $parent ) {
				$data['parent'] = false;
			} else {
				// Post data.
				$data['parent']['ID'] = $parent->ID;
				$data['parent']['post_date'] = $parent->post_date;
				$data['parent']['post_name'] = $parent->post_name;
				$data['parent']['post_title'] = $parent->post_title;
				$data['parent']['post_content'] = $parent->post_content;
				$data['parent']['post_excerpt'] = $parent->post_excerpt;
				$data['parent']['post_status'] = $parent->post_status;
				$data['parent']['post_type'] = $parent->post_type;

				// Featured image.
				$parent_image_id = get_post_thumbnail_id( $parent->ID );

				if ( $parent_image_id ) {
					$thumb = wp_get_attachment_image_src( $parent_image_id, 'full' );
					$parent_image_url = $thumb && isset( $thumb[0] ) ? $thumb[0] : false;

					if ( $parent_image_url ) {
						$data['parent']['image_url'] = $parent_image_url;
					}
				}
				
				// Taxonomies.
				$data['parent']['tags'] = array();

				$taxonomies = get_taxonomies( '', 'names' );
				$terms = wp_get_object_terms( $parent->ID, $taxonomies );

				foreach ( $terms as $term ) {
					if ( ! array_key_exists( $term->taxonomy, $data['parent']['tags'] ) ) {
						$data['parent']['tags'][ $term->taxonomy ] = array();
					}

					$data['parent']['tags'][ $term->taxonomy ][] = $term->name;
				}
			}
		}

		return $data;
	}

	/**
	 * Clean up recipe data for export.
	 *
	 * @since	5.2.0
	 * @param	mixed $data Recipe data to export.
	 */
	public static function clean_up_recipe_for_export( $data ) {
		unset( $data[ 'image_id' ] );
		unset( $data[ 'pin_image_id' ] );
		unset( $data[ 'video_id' ] );
		unset( $data[ 'video_thumb_url' ] );
		unset( $data[ 'ingredients' ] ); // Use ingredients_flat for easier editing.
		unset( $data[ 'instructions' ] ); // Use instructions_flat for easier editing.

		foreach ( $data['tags'] as $tag => $terms ) {
			$term_names = array();

			foreach ( $terms as $term ) {
				$term_names[] = $term->name;
			}

			$data['tags'][ $tag ] = $term_names;
		}

		foreach ( $data['equipment'] as $index => $equipment ) {
			unset( $data['equipment'][ $index ]['id'] );
		}

		foreach ( $data['ingredients_flat'] as $index => $ingredient ) {
			unset( $data['ingredients_flat'][ $index ]['id'] );
		}

		foreach ( $data['instructions_flat'] as $index => $ingredient ) {
			unset( $data['instructions_flat'][ $index ]['image'] );
		}

		if ( isset( $data['custom_fields'] ) ) {
			foreach ( $data['custom_fields'] as $index => $custom_field ) {
				if ( is_array( $custom_field ) && isset( $custom_field['id'] ) ) {
					unset( $data['custom_fields'][ $index ]['id'] );
				}
			}
		}

		return $data;
	}
}
