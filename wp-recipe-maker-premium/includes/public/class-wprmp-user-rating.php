<?php
/**
 * Handle the user ratings.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.6.0
 *
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 */

/**
 * Handle the user ratings.
 *
 * @since      1.6.0
 * @package    WP_Recipe_Maker_Premium
 * @subpackage WP_Recipe_Maker_Premium/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRMP_User_Rating {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.6.0
	 */
	public static function init() {
		add_action( 'wp_ajax_wprm_user_rate_recipe', array( __CLASS__, 'ajax_user_rate_recipe' ) );
		add_action( 'wp_ajax_nopriv_wprm_user_rate_recipe', array( __CLASS__, 'ajax_user_rate_recipe' ) );
	}

	/**
	 * Get user ratings for a specific recipe.
	 *
	 * @since	2.2.0
	 * @param	int $recipe_id ID of the recipe.
	 */
	public static function get_ratings_for( $recipe_id ) {
		$recipe_id = intval( $recipe_id );

		$ratings = array();

		if ( $recipe_id ) {
			$user_ratings = WPRM_Rating_Database::get_ratings(array(
				'where' => 'recipe_id = ' . $recipe_id,
			));

			$ratings = $user_ratings['ratings'];
		}

		return $ratings;
	}

	/**
	 * Add or update rating for a specific recipe.
	 *
	 * @since	2.2.0
	 * @param	int $recipe_id ID of the recipe.
	 * @param	int $user_rating Rating to add for this recipe.
	 */
	public static function add_or_update_rating_for( $recipe_id, $user_rating ) {
		$recipe_id = intval( $recipe_id );

		if ( $recipe_id ) {
			$rating = array(
				'recipe_id' => $recipe_id,
				'user_id' => get_current_user_id(),
				'ip' => self::get_user_ip(),
				'rating' => $user_rating,
			);

			WPRM_Rating_Database::add_or_update_rating( $rating );

			// Maybe clear cache.
			if ( WPRM_Settings::get( 'user_ratings_clear_cache' ) ) {
				// Get optional parent post ID.
				$parent_post_id = false;

				$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );
				if ( $recipe ) {
					$parent_post_id = $recipe->parent_post_id();
				}

				// WP Rocket.
				if ( function_exists( 'rocket_clean_post' ) ) {
					rocket_clean_post( $recipe_id );

					if ( $parent_post_id ) {
						rocket_clean_post( $parent_post_id );
					}
				}
			}
		}
	}

	/**
	 * Get the rating the current user has given to a specific recipe.
	 *
	 * @since    1.6.0
	 * @param	 int $recipe_id The Recipe to get the rating for.
	 */
	public static function get_user_rating_for( $recipe_id ) {
		if ( isset ( $_COOKIE[ 'WPRM_User_Voted_' . $recipe_id ] ) ) {
			return intval( $_COOKIE[ 'WPRM_User_Voted_' . $recipe_id ] );
		}

		$rating = 0;

		$ip = self::get_user_ip();
		$user = get_current_user_id();

		$user_ratings = self::get_ratings_for( $recipe_id );

		foreach ( $user_ratings as $user_rating ) {
			if ( ! $user && 'unknown' !== $ip && $ip === $user_rating->ip ) {
				$rating = $user_rating->rating;
			} elseif ( $user && $user === $user_rating->user_id ) {
				$rating = $user_rating->rating;
			}
		}

		return $rating;
	}

	/**
	 * Set the user rating for a recipe.
	 *
	 * @since    1.6.0
	 */
	public static function ajax_user_rate_recipe() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$recipe_id = isset( $_POST['recipe_id'] ) ? intval( $_POST['recipe_id'] ) : 0; // Input var okay.
			$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0; // Input var okay.
			$rating = isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0; // Input var okay.
			$decimals = isset( $_POST['decimals'] ) ? intval( $_POST['decimals'] ) : 2; // Input var okay.

			$user_rated = false;
			if ( $recipe_id && $rating && self::is_user_allowed_to_vote() ) {
				self::add_or_update_rating_for( $recipe_id, $rating );
				
				// Track in analytics.
				WPRM_Analytics::register_action( $recipe_id, $post_id, 'user-rating', array(
					'rating' => $rating,
				) );

				// Set or update cookie for easy access.
				setcookie( 'WPRM_User_Voted_' . $recipe_id, $rating, time() + 60 * 60 * 24 * 30, '/' );
				$user_rated = true;
			}

			// Response.
			if ( $recipe_id ) {
				WPRM_Recipe_Manager::invalidate_recipe( $recipe_id ); // TODO. Still seeing cached rating?!
				$recipe = WPRM_Recipe_Manager::get_recipe( $recipe_id );

				if ( $recipe ) {
					$recipe_rating = $recipe->rating();

					if ( $recipe_rating ) {
						// Set user value to rating that was just given.
						if ( $user_rated ) {
							$recipe_rating['user'] = $rating;
						}

						// Send JSON response.
						wp_send_json( array(
							'formatted' => WPRM_Rating::get_formatted_rating( $recipe_rating, $decimals ),
						), 200 );
					}
				}
			}
		}

		wp_die();
	}

	/**
	 * Check if the current user is allowed to vote.
	 *
	 * @since    1.6.0
	 */
	public static function is_user_allowed_to_vote() {
		if ( 0 === get_current_user_id() && 'unknown' === self::get_user_ip() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the IP address of the current user.
	 * Source: http://stackoverflow.com/questions/6717926/function-to-get-user-ip-address
	 *
	 * @since    1.6.0
	 */
	public static function get_user_ip() {
		foreach ( array( 'REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED' ) as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( array_map( 'trim', explode( ',', $_SERVER[ $key ] ) ) as $ip ) { // Input var ok.
					if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
						return $ip;
					}
				}
			}
		}
		return 'unknown';
	}
}

WPRMP_User_Rating::init();
