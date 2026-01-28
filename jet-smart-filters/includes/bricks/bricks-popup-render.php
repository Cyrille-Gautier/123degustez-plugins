<?php

namespace Jet_Smart_Filters\Bricks_Views;

use Bricks\Templates;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Manages JetSmartFilters styles rendering inside Bricks popups.
 *
 * Bricks popups are rendered twice (fake render + real render).
 * This class temporarily resets the filters usage flag during the fake render
 * so filter styles can be printed during the real popup render,
 * without breaking cases where filters were already rendered earlier on the page.
 */
class Bricks_Popup_Render {
	public $filters_were_used_before_popup = false;

	function __construct() {
		add_filter( 'pre_do_shortcode_tag', array( $this, 'reset_filters_flag_before_fake_popup_render' ), 10, 3 );
		add_action( 'bricks/frontend/before_render_data', array( $this, 'restore_filters_flag_before_real_popup_render' ), 10, 2 );
	}

	/**
	 * Reset filters usage flag before Bricks popup fake render.
	 *
	 * Stores the previous state to avoid overriding cases
	 * where filters were already rendered on the page.
	 */
	public function reset_filters_flag_before_fake_popup_render( $flag, $tag, $attr ) {
		$template_id = ! empty( $attr['id'] ) ? intval( $attr['id'] ) : false;

		if ( ! $template_id ) {
			return $flag;
		}

		$template_type = Templates::get_template_type( $template_id );

		if ( $tag === 'bricks_template' && $template_type === 'popup' ) {
			$this->filters_were_used_before_popup = ! jet_smart_filters()->filters_not_used;

			if ( jet_smart_filters()->filters_not_used ) {
				jet_smart_filters()->filters_not_used = false;
			}
		}

		return $flag;
	}

	/**
	 * Restore filters usage flag before the real popup render.
	 *
	 * Ensures styles are printed inside the popup only
	 * if filters were not rendered earlier on the page.
	 */
	public function restore_filters_flag_before_real_popup_render( $elements, $area ) {
		if ( $area === 'popup' && ! $this->filters_were_used_before_popup ) {
			if ( ! jet_smart_filters()->filters_not_used ) {
				jet_smart_filters()->filters_not_used = true;
			}
		}
	}
}