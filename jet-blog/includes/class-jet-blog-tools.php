<?php
/**
 * Cherry addons tools class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Blog_Tools' ) ) {

	/**
	 * Define Jet_Blog_Tools class
	 */
	class Jet_Blog_Tools {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   Jet_Blog_Tools
		 */
		private static $instance = null;

		/**
		 * Returns columns classes string
		 * @param  [type] $columns [description]
		 * @return [type]          [description]
		 */
		public function col_classes( $columns = array() ) {

			$columns = wp_parse_args( $columns, array(
				'desk' => 1,
				'tab'  => 1,
				'mob'  => 1,
			) );

			$classes = array();

			foreach ( $columns as $device => $cols ) {
				if ( ! empty( $cols ) ) {
					$classes[] = sprintf( 'col-%1$s-%2$s', $device, $cols );
				}
			}

			return implode( ' ' , $classes );
		}

		/**
		 * Returns disable columns gap nad rows gap classes string
		 *
		 * @param  string $use_cols_gap [description]
		 * @param  string $use_rows_gap [description]
		 * @return [type]               [description]
		 */
		public function gap_classes( $use_cols_gap = 'yes', $use_rows_gap = 'yes' ) {

			$result = array();

			foreach ( array( 'cols' => $use_cols_gap, 'rows' => $use_rows_gap ) as $element => $value ) {
				if ( 'yes' !== $value ) {
					$result[] = sprintf( 'disable-%s-gap', $element );
				}
			}

			return implode( ' ', $result );

		}

		/**
		 * Returns image size array in slug => name format
		 *
		 * @return  array
		 */
		public function get_image_sizes() {

			global $_wp_additional_image_sizes;

			$sizes  = get_intermediate_image_sizes();
			$result = array();

			foreach ( $sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
				} else {
					$result[ $size ] = sprintf(
						'%1$s (%2$sx%3$s)',
						ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
						$_wp_additional_image_sizes[ $size ]['width'],
						$_wp_additional_image_sizes[ $size ]['height']
					);
				}
			}

			return array_merge( array( 'full' => esc_html__( 'Full', 'jet-blog' ), ), $result );
		}

		/**
		 * Get categories list.
		 *
		 * @return array
		 */
		public function get_categories() {

			$categories = get_categories();

			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}

			return wp_list_pluck( $categories, 'name', 'term_id' );

		}

		/**
		 * Returns icons data list.
		 *
		 * @return array
		 */
		public function get_theme_icons_data() {

			$default = array(
				'icons'  => false,
				'format' => 'fa %s',
				'file'   => false,
			);

			/**
			 * Filter default icon data before useing
			 *
			 * @var array
			 */
			$icon_data = apply_filters( 'jet-blog/controls/icon/data', $default );
			$icon_data = array_merge( $default, $icon_data );

			return $icon_data;
		}

		/**
		 * Returns allowed order by fields for options
		 *
		 * @return array
		 */
		public function orderby_arr() {
			return array(
				'none'          => esc_html__( 'None', 'jet-blog' ),
				'ID'            => esc_html__( 'ID', 'jet-blog' ),
				'author'        => esc_html__( 'Author', 'jet-blog' ),
				'title'         => esc_html__( 'Title', 'jet-blog' ),
				'name'          => esc_html__( 'Name (slug)', 'jet-blog' ),
				'date'          => esc_html__( 'Date', 'jet-blog' ),
				'modified'      => esc_html__( 'Modified', 'jet-blog' ),
				'rand'          => esc_html__( 'Rand', 'jet-blog' ),
				'comment_count' => esc_html__( 'Comment Count', 'jet-blog' ),
				'menu_order'    => esc_html__( 'Menu Order', 'jet-blog' ),
			);
		}

		/**
		 * Returns allowed order fields for options
		 *
		 * @return array
		 */
		public function order_arr() {

			return array(
				'desc' => esc_html__( 'Descending', 'jet-blog' ),
				'asc'  => esc_html__( 'Ascending', 'jet-blog' ),
			);

		}

		/**
		 * Returns allowed order by fields for options
		 *
		 * @return array
		 */
		public function verrtical_align_attr() {
			return array(
				'baseline'    => esc_html__( 'Baseline', 'jet-blog' ),
				'top'         => esc_html__( 'Top', 'jet-blog' ),
				'middle'      => esc_html__( 'Middle', 'jet-blog' ),
				'bottom'      => esc_html__( 'Bottom', 'jet-blog' ),
				'sub'         => esc_html__( 'Sub', 'jet-blog' ),
				'super'       => esc_html__( 'Super', 'jet-blog' ),
				'text-top'    => esc_html__( 'Text Top', 'jet-blog' ),
				'text-bottom' => esc_html__( 'Text Bottom', 'jet-blog' ),
			);
		}

		/**
		 * Returns array with numbers in $index => $name format for numeric selects
		 *
		 * @param  integer $to Max numbers
		 * @return array
		 */
		public function get_select_range( $to = 10, $exclude = array(), $from_zero = false ) {

			$from  = ( true === $from_zero ) ? 0 : 1;
			$range = range( $from, $to );

			if ( ! empty( $exclude ) ) {
				$range = array_diff( $range, $exclude );
			}

			return array_combine( $range, $range );
		}

		/**
		 * Returns badge placeholder URL
		 *
		 * @return void
		 */
		public function get_badge_placeholder() {
			return jet_blog()->plugin_url( 'assets/images/placeholder-badge.svg' );
		}

		/**
		 * Rturns image tag or raw SVG
		 *
		 * @param  string $url  image URL.
		 * @param  array  $attr [description]
		 * @return string
		 */
		public function get_image_by_url( $url = null, $attr = array() ) {

			$url = esc_url( $url );

			if ( empty( $url ) ) {
				return;
			}

			$ext  = pathinfo( $url, PATHINFO_EXTENSION );
			$attr = array_merge( array( 'alt' => '' ), $attr );

			if ( 'svg' !== $ext ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			$base_url = network_site_url( '/' );
			$svg_path = str_replace( $base_url, ABSPATH, $url );
			$key      = md5( $svg_path );
			$svg      = get_transient( $key );

			if ( ! $svg ) {
				$svg = file_get_contents( $svg_path );
			}

			if ( ! $svg ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			set_transient( $key, $svg, DAY_IN_SECONDS );

			unset( $attr['alt'] );

			return sprintf( '<div%2$s>%1$s</div>', $svg, $this->get_attr_string( $attr ) ); ;
		}

		/**
		 * Return attributes string from attributes array.
		 *
		 * @param  array  $attr Attributes string.
		 * @return string
		 */
		public function get_attr_string( $attr = array() ) {

			if ( empty( $attr ) || ! is_array( $attr ) ) {
				return;
			}

			$result = '';

			foreach ( $attr as $key => $value ) {
				$result .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}

			return $result;
		}

		/**
		 * Get post types options list
		 *
		 * @return array
		 */
		public function get_post_types() {

			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			$deprecated = apply_filters(
				'jet-blog/post-types-list/deprecated',
				array( 'attachment', 'elementor_library' )
			);

			$result = array();

			if ( empty( $post_types ) ) {
				return $result;
			}

			foreach ( $post_types as $slug => $post_type ) {

				if ( in_array( $slug, $deprecated ) ) {
					continue;
				}

				$result[ $slug ] = $post_type->label;

			}

			return $result;

		}

		/**
		 * Get post taxonomies options list
		 *
		 * @return array
		 */
		public function get_post_taxonomies() {

			$post_types = $this->get_post_types();
			$result     = array();

			$deprecated = apply_filters(
				'jet-blog/post-taxonomies-list/deprecated',
				array( 'product_shipping_class' )
			);

			foreach ( $post_types as $type => $label ) {

				$taxonomies = get_object_taxonomies( $type, 'objects' );

				if ( ! empty( $taxonomies ) ) {
					foreach ( $taxonomies as $tax ) {

						if ( $tax->public && ! in_array( $tax->name, $deprecated ) ) {
							$result[ $tax->name ] = $tax->label;
						}
					}
				}

			}

			return $result;

		}

		/**
		 * Return availbale arrows list
		 * @return [type] [description]
		 */
		public static function get_available_prev_arrows_list() {
			return apply_filters(
				'jet-blog/carousel/available-arrows/prev',
				array(
					'fa fa-angle-left'          => __( 'Angle', 'jet-blog' ),
					'fa fa-chevron-left'        => __( 'Chevron', 'jet-blog' ),
					'fa fa-angle-double-left'   => __( 'Angle Double', 'jet-blog' ),
					'fa fa-arrow-left'          => __( 'Arrow', 'jet-blog' ),
					'fa fa-caret-left'          => __( 'Caret', 'jet-blog' ),
					'fa fa-long-arrow-left'     => __( 'Long Arrow', 'jet-blog' ),
					'fa fa-arrow-circle-left'   => __( 'Arrow Circle', 'jet-blog' ),
					'fa fa-chevron-circle-left' => __( 'Chevron Circle', 'jet-blog' ),
					'fa fa-caret-square-o-left' => __( 'Caret Square', 'jet-blog' ),
				)
			);
		}

		public static function get_svg_arrows( $arrow_type ) {

			$arrow = [];

			switch( $arrow_type ){
				case 'fa fa-angle-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.625 5.3999L16.3 7.0749L11.35 12.0249L16.3 16.9749L14.625 18.6499L7.99999 12.0249L14.625 5.3999Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.37501 18.6001L7.70001 16.9251L12.65 11.9751L7.70001 7.0251L9.37501 5.3501L16 11.9751L9.37501 18.6001Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-chevron-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.4 1.4499L18.4 3.4749L9.82502 12.0499L18.4 20.6249L16.4 22.6499L5.80002 12.0499L16.4 1.4499Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.59998 22.5501L5.59998 20.5251L14.175 11.9501L5.59998 3.3751L7.59998 1.3501L18.2 11.9501L7.59998 22.5501Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-angle-double-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.925 4.5748L6.39999 11.9998L11.925 19.4248L9.02499 19.4248L3.47499 11.9998L9.02499 4.5748L11.925 4.5748ZM19.3 4.57481L13.775 11.9998L19.3 19.4248L16.375 19.4248L10.85 11.9998L16.375 4.57481L19.3 4.57481Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.075 19.4252L17.6 12.0002L12.075 4.5752H14.975L20.525 12.0002L14.975 19.4252H12.075ZM4.70001 19.4252L10.225 12.0002L4.70001 4.5752H7.62501L13.15 12.0002L7.62501 19.4252H4.70001Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-arrow-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.7249L13.675 5.4249L8.25 10.8249L20.275 10.8249L20.275 13.1749L8.24999 13.1749L13.675 18.5999L12 20.2749L3.725 11.9999L12 3.7249Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 20.2751L10.325 18.5751L15.75 13.1751H3.72501V10.8251H15.75L10.325 5.4001L12 3.7251L20.275 12.0001L12 20.2751Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-caret-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.425 5.9749L14.425 18.0249L8.39999 11.9999L14.425 5.9749Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.57501 18.0251V5.9751L15.6 12.0001L9.57501 18.0251Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-long-arrow-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.72499 6.62519L8.42499 8.30019L5.92499 10.8002L21.425 10.8002L21.425 13.1502L5.89999 13.1502L8.42499 15.6502L6.74999 17.3252L1.39999 11.9752L6.72499 6.62519Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.275 17.3748L15.575 15.6998L18.075 13.1998H2.57501V10.8498H18.1L15.575 8.3498L17.25 6.6748L22.6 12.0248L17.275 17.3748Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-arrow-circle-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8.35L8.35 12L12 15.65L13.05 14.6L11.2 12.75L15.75 12.75L15.75 11.25L11.2 11.25L13.05 9.4L12 8.35ZM12 2C13.4167 2 14.7333 2.25417 15.95 2.7625C17.1667 3.27083 18.225 3.975 19.125 4.875C20.025 5.775 20.7292 6.83333 21.2375 8.05C21.7458 9.26667 22 10.5833 22 12C22 13.4 21.7458 14.7083 21.2375 15.925C20.7292 17.1417 20.025 18.2 19.125 19.1C18.225 20 17.1667 20.7083 15.95 21.225C14.7333 21.7417 13.4167 22 12 22C10.6 22 9.29167 21.7417 8.075 21.225C6.85833 20.7083 5.8 20 4.9 19.1C4 18.2 3.29167 17.1417 2.775 15.925C2.25833 14.7083 2 13.4 2 12C2 10.5833 2.25833 9.26666 2.775 8.05C3.29167 6.83333 4 5.775 4.9 4.875C5.8 3.975 6.85834 3.27083 8.075 2.7625C9.29167 2.25417 10.6 2 12 2Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15.65L15.65 12L12 8.35L10.95 9.4L12.8 11.25H8.25V12.75H12.8L10.95 14.6L12 15.65ZM12 22C10.5833 22 9.26667 21.7458 8.05 21.2375C6.83333 20.7292 5.775 20.025 4.875 19.125C3.975 18.225 3.27083 17.1667 2.7625 15.95C2.25417 14.7333 2 13.4167 2 12C2 10.6 2.25417 9.29167 2.7625 8.075C3.27083 6.85833 3.975 5.8 4.875 4.9C5.775 4 6.83333 3.29167 8.05 2.775C9.26667 2.25833 10.5833 2 12 2C13.4 2 14.7083 2.25833 15.925 2.775C17.1417 3.29167 18.2 4 19.1 4.9C20 5.8 20.7083 6.85833 21.225 8.075C21.7417 9.29167 22 10.6 22 12C22 13.4167 21.7417 14.7333 21.225 15.95C20.7083 17.1667 20 18.225 19.1 19.125C18.2 20.025 17.1417 20.7292 15.925 21.2375C14.7083 21.7458 13.4 22 12 22Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-chevron-circle-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.25 11.9751L13.075 16.8251L14.625 15.2251L11.375 11.9751L14.625 8.7251L13.075 7.1251L8.25 11.9751ZM1.35 12.0001C1.35 10.4834 1.62083 9.0751 2.1625 7.7751C2.70417 6.4751 3.45 5.3501 4.4 4.4001C5.35 3.4501 6.475 2.70426 7.775 2.1626C9.075 1.62093 10.4833 1.3501 12 1.3501C13.4833 1.3501 14.8708 1.62093 16.1625 2.1626C17.4542 2.70426 18.5792 3.4501 19.5375 4.4001C20.4958 5.3501 21.25 6.4751 21.8 7.7751C22.35 9.0751 22.625 10.4834 22.625 12.0001C22.625 13.4834 22.35 14.8709 21.8 16.1626C21.25 17.4543 20.4958 18.5793 19.5375 19.5376C18.5792 20.4959 17.4542 21.2501 16.1625 21.8001C14.8708 22.3501 13.475 22.6251 11.975 22.6251C10.475 22.6251 9.075 22.3501 7.775 21.8001C6.475 21.2501 5.35 20.4959 4.4 19.5376C3.45 18.5793 2.70417 17.4543 2.1625 16.1626C1.62083 14.8709 1.35 13.4834 1.35 12.0001Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.725 12L10.9 7.15L9.35001 8.75L12.6 12L9.35001 15.25L10.9 16.85L15.725 12ZM22.625 11.975C22.625 13.4917 22.3542 14.9 21.8125 16.2C21.2708 17.5 20.525 18.625 19.575 19.575C18.625 20.525 17.5 21.2708 16.2 21.8125C14.9 22.3542 13.4917 22.625 11.975 22.625C10.4917 22.625 9.10417 22.3542 7.81251 21.8125C6.52084 21.2708 5.39584 20.525 4.43751 19.575C3.47917 18.625 2.72501 17.5 2.17501 16.2C1.62501 14.9 1.35001 13.4917 1.35001 11.975C1.35001 10.4917 1.62501 9.10417 2.17501 7.8125C2.72501 6.52083 3.47917 5.39583 4.43751 4.4375C5.39584 3.47917 6.52084 2.725 7.81251 2.175C9.10417 1.625 10.5 1.35 12 1.35C13.5 1.35 14.9 1.625 16.2 2.175C17.5 2.725 18.625 3.47917 19.575 4.4375C20.525 5.39583 21.2708 6.52083 21.8125 7.8125C22.3542 9.10417 22.625 10.4917 22.625 11.975Z" fill="#0F172A"/></svg>';
					break;
				case 'fa fa-caret-square-o-left':
					$arrow['prev']  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 22C21.1046 22 22 21.1046 22 20L22 4C22 2.89543 21.1046 2 20 2L4 2C2.89543 2 2 2.89543 2 4L2 20C2 21.1046 2.89543 22 4 22L20 22ZM8.4 11.975L14.425 18L14.425 5.95L8.4 11.975Z" fill="#0F172A"/></svg>';
					$arrow['next'] = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 2C2.89543 2 2 2.89543 2 4V20C2 21.1046 2.89543 22 4 22H20C21.1046 22 22 21.1046 22 20V4C22 2.89543 21.1046 2 20 2H4ZM15.6 12.025L9.575 6V18.05L15.6 12.025Z" fill="#0F172A"/></svg>';
					break;
			}

			$arrow = apply_filters( 'jet-blog/carousel/nav-arrow', $arrow, $arrow_type );

			return $arrow;
		}

		/**
		 * Get terms from passed taxonomy
		 *
		 * @param  string $tax Taxonomy name.
		 * @return array.
		 */
		public function get_terms( $tax = 'category' ) {

			$terms   = get_terms( array( 'taxonomy' => $tax ) );
			$default = array( esc_html__( 'From All', 'jet-blog' ) );

			if ( empty( $terms ) ) {
				return $default;
			}

			$filtered_terms    = wp_list_pluck( $terms, 'name', 'term_id' );
			$filtered_terms[0] = $default[0];

			ksort( $filtered_terms );

			return $filtered_terms;
		}

		/**
		 * Returns carousel arrow
		 *
		 * @param  array $classes Arrow additional classes list.
		 * @return string
		 */
		public function get_carousel_arrow( $icon = '', $direction = 'next' ) {

			if ( '' === $icon ) {
				return;
			}

			$arrows = $this->get_svg_arrows( $icon );

			$format = apply_filters(
				'jet-blog/carousel/arrows-format',
				'<i class="jet-arrow-%2$s jet-blog-arrow">%1$s</i>'
			);

			return sprintf( $format, $arrows[$direction], $direction );
		}

		public function get_archive_control_desc() {

			$link = sprintf(
				'<a href="%1$s" target="_blank">%2$s</a>',
				esc_url( admin_url( 'options-reading.php#posts_per_page' ) ),
				esc_html__( 'here', 'jet-blog' )
			);

			return sprintf( esc_html__( 'Note: posts number per page will be inherited from Reading Settings. You can change it %s', 'jet-blog' ), $link );

		}

		/**
		 * Is script debug.
		 *
		 * @return bool
		 */
		public function is_script_debug() {
			return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		}

		/**
		 * Check if is valid timestamp
		 *
		 * @param  $timestamp
		 * @return boolean
		 */
		public function is_valid_timestamp( $timestamp ) {
			return ( ( string ) ( int ) $timestamp === $timestamp || ( int ) $timestamp === $timestamp )
			       && ( $timestamp <= PHP_INT_MAX )
			       && ( $timestamp >= ~PHP_INT_MAX );
		}

		/**
		 * Is FA5 migration.
		 *
		 * @return bool
		 */
		public function is_fa5_migration() {
			if ( defined( 'ELEMENTOR_VERSION' )
			     && version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' )
			     && Elementor\Icons_Manager::is_migration_allowed()
			) {
				return true;
			}

			return false;
		}

		public function validate_html_tag( $tag ) {
			$allowed_tags = array(
				'article',
				'aside',
				'div',
				'footer',
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6',
				'header',
				'main',
				'nav',
				'p',
				'section',
				'span',
			);

			return in_array( strtolower( $tag ), $allowed_tags ) ? $tag : 'div';
		}

		public function allowed_meta_callbacks() {
			$callbacks = apply_filters( 'jet-blog/base/meta-callbacks', array(
				''                        => esc_html__( 'Clean', 'jet-blog' ),
				'get_permalink'           => 'get_permalink',
				'get_the_title'           => 'get_the_title',
				'wp_get_attachment_url'   => 'wp_get_attachment_url',
				'wp_get_attachment_image' => 'wp_get_attachment_image',
				'date'                    => esc_html__( 'Format date', 'jet-blog' ),
				'date_i18n'               => esc_html__( 'Format date (localized)', 'jet-blog' ),
			) );

			return $callbacks;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return Jet_Blog_Tools
		 */
		public static function get_instance( $shortcodes = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;
		}

		/**
		 * Renders the author's avatar based on the provided settings.
		 *
		 * @param int $author_id The ID of the author.
		 * @param int $avatar_size_value The size of the avatar.
		 * @param string $avatar_source The source of the avatar ('default' or 'custom').
		 * @param string|false $source_meta_field The custom field key for the avatar or false if not applicable.
		 * @return string The HTML markup for the avatar.
		 */
		public function render_avatar( $author_id, $avatar_size_value, $avatar_source, $source_meta_field = false ) {
			$avatar = '';

			if ( 'custom' === $avatar_source ) {
				$meta_value = get_user_meta( $author_id, $source_meta_field, true );

				if ( is_numeric( $meta_value ) ) {
					$avatar = wp_get_attachment_image( $meta_value, array( $avatar_size_value, $avatar_size_value ) );
				} elseif ( filter_var( $meta_value, FILTER_VALIDATE_URL ) ) {
					$avatar = '<img src="' . esc_url( $meta_value ) . '" class="avatar avatar-thumbnail" width="' . esc_attr( $avatar_size_value ) . '" height="' . esc_attr( $avatar_size_value ) . '" />';
				} elseif ( is_array( $meta_value ) ) {
					if ( isset( $meta_value['id'] ) && is_numeric( $meta_value['id'] ) ) {
						$avatar = wp_get_attachment_image( $meta_value['id'], array( $avatar_size_value, $avatar_size_value ) );
					} else {
						foreach ( $meta_value as $value ) {
							if ( is_numeric( $value ) ) {
								$avatar = wp_get_attachment_image( $value, array( $avatar_size_value, $avatar_size_value ) );
								break;
							} elseif ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
								$avatar = '<img src="' . esc_url( $value ) . '" class="avatar avatar-thumbnail" width="' . esc_attr( $avatar_size_value ) . '" height="' . esc_attr( $avatar_size_value ) . '" />';
								break;
							}
						}
					}
				}
			} elseif ( 'default' === $avatar_source ) {
				$avatar = get_avatar( $author_id, $avatar_size_value );
			}

			return $avatar;
		}
	}

}

/**
 * Returns instance of Jet_Blog_Tools
 *
 * @return Jet_Blog_Tools
 */
function jet_blog_tools() {
	return Jet_Blog_Tools::get_instance();
}
