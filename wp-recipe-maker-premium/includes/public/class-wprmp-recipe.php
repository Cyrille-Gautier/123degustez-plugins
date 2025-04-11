<?php
/**
 * Handle the Premium recipe fields.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Handle the Premium recipe fields.
 *
 * @since      5.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_Recipe {
	public static function init() {
		add_filter( 'wprm_recipe_manage_data', array( __CLASS__, 'manage_data' ), 10, 2 );
		add_filter( 'wprm_recipe_author', array( __CLASS__, 'author' ), 10, 2 );
		add_filter( 'wprm_recipe_custom_field', array( __CLASS__, 'custom_field' ), 10, 3 );
		add_filter( 'wprm_recipe_custom_fields', array( __CLASS__, 'custom_fields' ), 10, 2 );
		add_filter( 'wprm_recipe_pin_image_id', array( __CLASS__, 'pin_image_id' ), 10, 3 );
		add_filter( 'wprm_recipe_in_collection', array( __CLASS__, 'in_collection' ), 10, 3 );
		add_filter( 'wprm_recipe_rating', array( __CLASS__, 'rating' ), 10, 2 );
	}

	/**
	 * Change the recipe manage data.
	 *
	 * @since	5.6.0	 
	 * @param	mixed $data 	Current data.
	 * @param	array $recipe	Recipe we're getting the manage data for.
	 */
	public static function manage_data( $data, $recipe ) {
		if ( WPRM_Addons::is_active( 'unit-conversion' ) ) {
			$data['unit_conversion'] = __( 'Not enabled', 'wp-recipe-maker' );

			if ( WPRM_Settings::get( 'unit_conversion_enabled' ) ) {
				$ingredients = $recipe->ingredients_without_groups();
				$converted_ingredients = array();

				foreach ( $ingredients as $ingredient ) {
					if ( isset( $ingredient['converted'] ) ) {
						foreach ( $ingredient['converted'] as $system => $values ) {
							$converted = $values['amount'] ? $values['amount'] : '';
							$converted .= $values['unit'] ? ' ' . $values['unit'] : '';
							$converted = trim( $converted );

							if ( $converted ) {
								$converted .= ' ' . $ingredient['name'] . ' ' . $ingredient['notes'];
								$converted_ingredients[] = trim( $converted );
							}
						}
					}
				}

				$data['unit_conversion'] = $converted_ingredients;
			}
		}

		return $data;
	}

	/**
	 * Get the recipe author.
	 *
	 * @since	5.6.0	 
	 * @param	mixed $author 	Current author.
	 * @param	array $recipe	Recipe we're getting the author for.
	 */
	public static function author( $author, $recipe ) {
		if ( $author ) {
			switch ( $recipe->author_display() ) {
				case 'post_author':
					if ( WPRM_Settings::get( 'post_author_link' ) ) {
						$author_id = $recipe->post_author();

						if ( $author_id ) {
							if ( 'archive' === WPRM_Settings::get( 'post_author_link_use' ) ) {
								$link = get_author_posts_url( $author_id );
							} else {
								$author_data = get_userdata( $author_id );
								$link = $author_data->data->user_url;
							}

							if ( $link ) {
								$target = WPRM_Settings::get( 'post_author_link_new_tab' ) ? '_blank' : '_self';
								$author = '<a href="' . esc_attr( $link ) . '" target="' . $target . '">' . $author . '</a>';
							}
						}
					}
					break;
				case 'custom':
					$link = $recipe->custom_author_link();
	
					if ( $link ) {
						$author = '<a href="' . esc_attr( $link ) . '" target="_blank">' . $author . '</a>';
					}
					break;
				case 'same':
					$link = WPRM_Settings::get( 'recipe_author_same_link' );
	
					if ( $link ) {
						$target = WPRM_Settings::get( 'recipe_author_same_link_new_tab' ) ? '_blank' : '_self';
						$author = '<a href="' . esc_attr( $link ) . '" target="' . $target . '">' . $author . '</a>';
					}
					break;
			}
		}

		return $author;
	}

	/**
	 * Get the recipe custom field.
	 *
	 * @since	5.6.0	 
	 * @param	mixed $custom_field Current custom field.
	 * @param	array $field		Field to get.
	 * @param	array $recipe		Recipe to get the field for.
	 */
	public static function custom_field( $custom_field, $field, $recipe ) {
		if ( WPRM_Addons::is_active( 'custom-fields' ) ) {
			$custom_field = WPRMPCF_Fields::get( $recipe, $field );
		}

		return $custom_field;
	}

	/**
	 * Get the recipe custom fields.
	 *
	 * @since	5.6.0	 
	 * @param	mixed $custom_fields Current custom fields.
	 * @param	array $recipe		Recipe to get the fields for.
	 */
	public static function custom_fields( $custom_fields, $recipe ) {
		// Prevent StoreCustomizer compatibility problem.
		if ( is_string( $recipe ) ) {
			return $custom_fields;
		}

		if ( WPRM_Addons::is_active( 'custom-fields' ) ) {
			$custom_fields = WPRMPCF_Fields::get_all( $recipe );
		}

		return $custom_fields;
	}

	/**
	 * Get the recipe pin image ID.
	 *
	 * @since	5.6.0
	 * @param	mixed 	$pin_image_id 	Current pin image ID.
	 * @param   boolean $for_editing 	Wether or not we're retrieving the value for editing.
	 * @param	array 	$recipe			Recipe to get the ID for.
	 */
	public static function pin_image_id( $pin_image_id, $for_editing, $recipe ) {
		switch ( WPRM_Settings::get( 'pinterest_use_for_image' ) ) {
			case 'custom':
				$pin_image_id = $recipe->meta( 'wprm_pin_image_id', 0 );
				break;
			case 'custom_or_recipe_image':
				$custom_image_id = $recipe->meta( 'wprm_pin_image_id', 0 );
				if ( $custom_image_id ) {
					$pin_image_id = $custom_image_id;
				}
				break;
		}

		return max( array( intval( $pin_image_id ), 0 ) ); // Make sure to return 0 when set to -1.
	}

	/**
	 * Check if a recipe is in a collection.
	 *
	 * @since	5.6.0
	 * @param   boolean $in_collection 	Wether or not the recipe is in the collection.
	 * @param	mixed 	$collection_id 	Collection to check.
	 * @param	array 	$recipe			Recipe to check.
	 */
	public static function in_collection( $in_collection, $collection_id, $recipe ) {
		if ( WPRM_Addons::is_active( 'recipe-collections' ) ) {
			$collections = WPRMPRC_Manager::get_user_collections();

			if ( 'inbox' === $collection_id ) {
				$collection = $collections['inbox'];
			} elseif ( 'temp' === $collection_id ) {
				if ( isset( $collections['temp'] ) ) {
					$collection = $collections['temp'];
				} else {
					return false;
				}
			} else {
				// Not implemented yet. Needed?
			}

			if ( $collections && isset( $collection['items'] ) && $collection['items']['0-0'] ) {
				$recipe_id = $recipe->id();
				$filtered_items = array_filter( $collection['items']['0-0'], function( $item ) use ( $recipe_id ) {
					if ( isset( $item['recipeId'] ) ) {
						return $recipe_id === $item['recipeId'];	
					}
					return false;
				});

				if ( $filtered_items ) {
					$in_collection = true;
				}
			}
		}

		return $in_collection;
	}

	/**
	 * Get the recipe rating.
	 *
	 * @since	5.6.0
	 * @param   boolean $rating Current rating.
	 * @param	array 	$recipe	Recipe to get the rating for.
	 */
	public static function rating( $rating, $recipe ) {
		$rating['user'] = WPRMP_User_Rating::get_user_rating_for( $recipe->id() );
		return $rating;
	}
}

WPRMP_Recipe::init();