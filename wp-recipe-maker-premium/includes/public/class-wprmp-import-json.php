<?php
/**
 * Handle the import of recipes from JSON.
 *
 * @link       http://bootstrapped.ventures
 * @since      5.2.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Handle the import of recipes from JSON.
 *
 * @since      5.2.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_Import_JSON {

	/**
	 *  Number of recipes to import at a time.
	 *
	 * @since    5.3.0
	 * @access   private
	 * @var      int $import_limit Number of recipes to import at a time.
	 */
	private static $import_limit = 3;

	/**
	 * Register actions and filters.
	 *
	 * @since    5.2.0
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu_page' ), 20 );
		add_action( 'wp_ajax_wprm_import_json', array( __CLASS__, 'ajax_import_json' ) );
	}

	/**
	 * Add the JSON import page.
	 *
	 * @since	5.2.0
	 */
	public static function add_submenu_page() {
		add_submenu_page( null, __( 'WPRM Import from JSON', 'wp-recipe-maker' ), __( 'WPRM Import from JSON', 'wp-recipe-maker' ), WPRM_Settings::get( 'features_import_access' ), 'wprm_import_json', array( __CLASS__, 'import_json_page_template' ) );
	}

	/**
	 * Get the template for the edit saved collection page.
	 *
	 * @since	5.2.0
	 */
	public static function import_json_page_template() {
		$importing = false;

		if ( isset( $_POST['wprm_import_json'] ) && wp_verify_nonce( $_POST['wprm_import_json'], 'wprm_import_json' ) ) { // Input var okay.
			$filename = $_FILES['json']['tmp_name'];
			if ( $filename ) {
				$json = false;

				$str = file_get_contents(
					$filename,
					false,
					stream_context_create( array(
						'http' => array(
							'ignore_errors' => true,
						),
					))
				);
				if ( $str ) {
					$json = json_decode( $str, true );
				}

				if ( ! $json || ! is_array( $json ) || ! count( $json ) ) {
					echo '<p>We were not able to read this file or find any recipes. Is it using the correct JSON format?</p>';
				} else {
					$importing = true;
					$import_type = isset( $_POST['wprm-import-type'] ) ? $_POST['wprm-import-type'] : 'create';

					delete_transient( 'wprm_import_recipes_json' );
					delete_transient( 'wprm_import_recipes_type' );
					$transient = json_encode( $json );
					set_transient( 'wprm_import_recipes_json', $transient, 60 * 60 * 24 );
					set_transient( 'wprm_import_recipes_type', $import_type, 60 * 60 * 24 );

					$recipes = count ( $json );
					$pages = ceil( $recipes / self::$import_limit );

					// Handle via AJAX.
					wp_localize_script( 'wprmp-admin', 'wprm_import_json', array(
						'pages' => $pages,
					));

					echo '<p>Importing ' . $recipes . ' recipes.</p>';
					echo '<div id="wprm-tools-progress-container"><div id="wprm-tools-progress-bar"></div></div>';
					echo '<p id="wprm-tools-finished">Import finished!. <a href="' . admin_url( 'admin.php?page=wprm_manage' ) . '">View on the manage page</a>.</p>';
					
					// foreach ( $json as $json_recipe ) {
					// 	self::import_json_recipe( $json_recipe );
					// }

					// echo '<p>Imported ' . count( $json ) . ' recipes. <a href="' . admin_url( 'admin.php?page=wprm_manage' ) . '">View on the manage page</a>.</p>';
				}
			} else {
				echo '<p>No file selected.</p>';
			}
		}
		
		if ( ! $importing ) {
			include WPRMP_DIR . 'templates/admin/import-json.php';
		}
	}

	/**
	 * Import recipes through AJAX.
	 *
	 * @since	5.3.0
	 */
	public static function ajax_import_json() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : false; // Input var okay.

			if ( false !== $page ) {
				$import_type = get_transient( 'wprm_import_recipes_type' );
				$transient = get_transient( 'wprm_import_recipes_json' );
				$json = json_decode( $transient, true );

				if ( $json && is_array( $json ) ) {
					$start = $page * self::$import_limit;
					$end = $start + self::$import_limit;

					for ( $i = $start; $i < $end; $i++ ) {
						if ( isset( $json[ $i ] ) ) {
							self::import_json_recipe( $json[ $i ], $import_type );
						}
					}

					wp_send_json_success();
				}
			}

			wp_send_json_error();
		}
		wp_die();
	}

	/**
	 * Import a single recipe from JSON.
	 *
	 * @since	5.2.0
	 * @param	mixed $json_recipe  Recipe to import from JSON.
	 * @param	mixed $import_type  Import type to use.
	 */
	public static function import_json_recipe( $json_recipe, $import_type = 'create' ) {
		$old_recipe_id = false;
		if ( isset ( $json_recipe['id'] ) && $json_recipe['id'] ) {
			$old_recipe_id = intval( $json_recipe['id'] );
		}

		// Check import type.
		$recipe_id = false;
		$create_new_otherwise = true;
		if ( 'create' !== $import_type ) {
			if ( 'edit' === substr( $import_type, 0, 4 ) ) {
				$create_new_otherwise = false;
			}

			// Find existing recipe.
			$import_type_parts = explode( '-', $import_type );
			if ( 'id' === $import_type_parts[1] ) {
				if ( $old_recipe_id && WPRM_POST_TYPE === get_post_type( $old_recipe_id ) ) {
					$recipe_id = $old_recipe_id;
				}
			} elseif ( 'slug' === $import_type_parts[1] ) {
				$json_recipe_slug = isset( $json_recipe['slug'] ) ? trim( $json_recipe['slug'] ) : false;

				if ( $json_recipe_slug ) {
					$existing_recipe = get_page_by_path( $json_recipe_slug, OBJECT, WPRM_POST_TYPE );

					if ( $existing_recipe ) {
						$recipe_id = $existing_recipe->ID;	
					}
				}
			}
		}

		// Maybe create new recipe.
		$created_new_recipe = false;
		if ( ! $recipe_id ) {
			if ( ! $create_new_otherwise ) {
				return;
			}

			// Create new recipe.
			$post = array(
				'post_type' => WPRM_POST_TYPE,
				'post_status' => 'draft',
			);

			// Try to reuse the ID if set.
			if ( $old_recipe_id ) {
				$post['import_id'] = $old_recipe_id;
			}

			$recipe_id = wp_insert_post( $post );
			$created_new_recipe = true;
		}

		// Import recipe images.
		if ( isset( $json_recipe['image_url'] ) && $json_recipe['image_url'] ) {
			$json_recipe['image_id'] = WPRM_Import_Helper::get_or_upload_attachment( $recipe_id, $json_recipe['image_url'] );
		}
		if ( isset( $json_recipe['pin_image_url'] ) && $json_recipe['pin_image_url'] ) {
			$json_recipe['pin_image_id'] = WPRM_Import_Helper::get_or_upload_attachment( $recipe_id, $json_recipe['pin_image_url'] );
		}

		// Import instruction images.
		if ( isset( $json_recipe['instructions_flat'] ) ) {
			foreach ( $json_recipe['instructions_flat'] as $index => $instruction ) {
				if ( isset( $instruction['image_url'] ) && $instruction['image_url'] ) {
					$json_recipe['instructions_flat'][ $index ]['image'] = WPRM_Import_Helper::get_or_upload_attachment( $recipe_id, $instruction['image_url'] );
				} 
			}
		}

		// Import custom field images.
		if ( isset( $json_recipe['custom_fields'] ) ) {
			foreach ( $json_recipe['custom_fields'] as $index => $custom_field ) {
				if ( is_array( $custom_field ) && isset( $custom_field['url'] ) && $custom_field['url'] ) {
					$json_recipe['custom_fields'][ $index ]['id'] = WPRM_Import_Helper::get_or_upload_attachment( $recipe_id, $custom_field['url'] );
				}
			}
		}

		// Sanitize and save recipe.
		$recipe = WPRM_Recipe_Sanitizer::sanitize( $json_recipe );
		WPRM_Recipe_Saver::update_recipe( $recipe_id, $recipe );

		// Maybe import parent post for recipe. Only when we created a new recipe.
		if ( $created_new_recipe && isset( $json_recipe['parent'] ) && $json_recipe['parent'] ) {
			$parent_post_id = self::import_json_parent( $json_recipe['parent'], $old_recipe_id, $recipe_id );

			if ( $parent_post_id ) {
				update_post_meta( $recipe_id, 'wprm_parent_post_id', $parent_post_id );
			}
		}
	}

	/**
	 * Import a parent post from JSON.
	 *
	 * @since	7.1.0
	 * @param	mixed $json_parent  	Parent post to import from JSON.
	 * @param	int   $old_recipe_id  	Old ID of the imported recipe.
	 * @param	int   $new_recipe_id  	New ID of the imported recipe.
	 */
	public static function import_json_parent( $json_parent, $old_recipe_id, $new_recipe_id ) {
		// Default to new draft post.
		$parent = array(
			'post_type' => 'post',
			'post_status' => 'draft',
		);

		// Maybe update recipe ID in post content.
		$content = isset( $json_parent['post_content'] ) ? $json_parent['post_content'] : '';
		if ( $old_recipe_id && $old_recipe_id !== $new_recipe_id ) {
			// Gutenberg.
			$gutenberg_matches = array();
			$gutenberg_patern = '/<!--\s+wp:(wp-recipe-maker\/recipe)(\s+(\{.*?\}))?\s+(\/)?-->.*?<!--\s+\/wp:wp-recipe-maker\/recipe\s+(\/)?-->/mis';
			preg_match_all( $gutenberg_patern, $content, $matches );

			if ( isset( $matches[3] ) ) {
				foreach ( $matches[3] as $index => $block_attributes_json ) {
					if ( ! empty( $block_attributes_json ) ) {
						$attributes = json_decode( $block_attributes_json, true );

						if ( ! is_null( $attributes ) ) {
							if ( isset( $attributes['id'] ) && $old_recipe_id === intval( $attributes['id'] ) ) {
								$content = str_ireplace( $matches[0][ $index ], '<!-- wp:wp-recipe-maker/recipe {"id":' . $new_recipe_id . ',"updated":' . time() . '} -->[wprm-recipe id="' . $new_recipe_id . '"]<!-- /wp:wp-recipe-maker/recipe -->', $content );
							}
						}
					}
				}
			}

			// Classic Editor.
			$content = WPRM_Fallback_Recipe::replace_fallback_with_shortcode( $content );

			$classic_pattern = '/\[wprm-recipe\s.*?id=\"?\'?(\d+)\"?\'?.*?\]/mi';
			preg_match_all( $classic_pattern, $content, $classic_matches );

			if ( isset( $classic_matches[1] ) ) {
				foreach ( $classic_matches[1] as $index => $id ) {
					if ( $old_recipe_id === intval( $id ) ) {
						$content = str_ireplace( $classic_matches[0][ $index ], '[wprm-recipe id="' . $new_recipe_id . '"]', $content );
					}
				}
			}
		}
		$parent['post_content'] = $content;

		// Regular post fields.
		if ( isset( $json_parent['ID'] ) ) { $parent['import_id'] = $json_parent['ID']; }
		if ( isset( $json_parent['post_date'] ) ) { $parent['post_date'] = $json_parent['post_date']; }
		if ( isset( $json_parent['post_name'] ) ) { $parent['post_name'] = $json_parent['post_name']; }
		if ( isset( $json_parent['post_title'] ) ) { $parent['post_title'] = $json_parent['post_title']; }
		if ( isset( $json_parent['post_excerpt'] ) ) { $parent['post_excerpt'] = $json_parent['post_excerpt']; }
		if ( isset( $json_parent['post_status'] ) ) { $parent['post_status'] = $json_parent['post_status']; }
		if ( isset( $json_parent['post_type'] ) ) { $parent['post_type'] = $json_parent['post_type']; }

		// Insert parent post.
		$parent_post_id = wp_insert_post( $parent );

		// Featured Image.
		if ( isset( $json_parent['image_url'] ) && $json_parent['image_url'] ) {
			$image_id = WPRM_Import_Helper::get_or_upload_attachment( $parent_post_id, $json_parent['image_url'] );
			set_post_thumbnail( $parent_post_id, $image_id );
		}

		// Taxonomies.
		if ( isset( $json_parent['tags'] ) ) {
			foreach ( $json_parent['tags'] as $taxonomy => $terms ) {
				wp_set_object_terms( $parent_post_id, $terms, $taxonomy, false );
			}
		}

		return $parent_post_id;
	}
}
WPRMP_Import_JSON::init();
