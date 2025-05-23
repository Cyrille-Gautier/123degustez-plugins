<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Dynamic_Image' ) ) {

	class Jet_Engine_Render_Dynamic_Image extends Jet_Engine_Render_Base {

		private $source     = false;
		private $show_field = true;

		public $full_image_src = null;

		public function get_name() {
			return 'jet-listing-dynamic-image';
		}

		public function default_settings() {
			return array(
				'dynamic_image_source'        => 'post_thumbnail',
				'image_url_prefix'            => '',
				'dynamic_image_size'          => 'full',
				'dynamic_avatar_size'         => 50,
				'custom_image_alt'            => '',
				'lazy_load_image'             => wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ),
				'dynamic_image_source_custom' => '',
				'linked_image'                => true,
				'image_link_source'           => '_permalink',
				'link_url_prefix'             => '',
				'open_in_new'                 => false,
				'hide_if_empty'               => false,
				'object_context'              => 'default_object',
				'custom_image_styles'         => array(),
			);
		}

		/**
		 * Render image
		 *
		 * @return [type] [description]
		 */
		public function render_image( $settings ) {

			$source = isset( $settings['dynamic_image_source'] ) ? $settings['dynamic_image_source'] : 'post_thumbnail';
			$custom = isset( $settings['dynamic_image_source_custom'] ) ? $settings['dynamic_image_source_custom'] : false;

			if ( ! $source && ! $custom ) {
				return;
			}

			$object_context = isset( $settings['object_context'] ) ? $settings['object_context'] : false;
			$size           = $this->get_image_size( $settings );

			if ( $custom ) {
				$this->render_image_by_meta_field( $custom, $size, $settings );
				return;
			}

			if ( 'post_thumbnail' === $source ) {

					$post = jet_engine()->listings->data->get_object_by_context( $object_context );

					if ( ! $post ) {
						$post = jet_engine()->listings->data->get_current_object();
					}

					if ( ! $post || 'WP_Post' !== get_class( $post ) ) {
						return $this->process_fallback_image( $settings );
					}

					if ( ! has_post_thumbnail( $post->ID ) ) {
						return $this->process_fallback_image( $settings );
					}

					$thumbnail_id = get_post_thumbnail_id( $post );

					echo get_the_post_thumbnail( $post->ID, $size, array( 'alt' => $this->get_image_alt( $thumbnail_id, $settings ) ) );

					return;

			} elseif ( 'user_avatar' === $source ) {

				$user = jet_engine()->listings->data->get_object_by_context( $object_context );

				if ( ! $user ) {
					$user = jet_engine()->listings->data->get_current_object();
				}

				$size = ! empty( $settings['dynamic_avatar_size'] ) ? $settings['dynamic_avatar_size'] : array( 'size' => 50 );
				$size = ! empty( $size['size'] ) ? $size['size'] : 50;
				$alt  = $this->get_image_alt( null, $settings );
				$args = array(
					'loading' => filter_var( $settings['lazy_load_image'], FILTER_VALIDATE_BOOLEAN ) ? 'lazy' : 'eager',
				);

				if ( $user && 'WP_User' === get_class( $user ) ) {
					$this->full_image_src = get_avatar_url( $user->ID, array( 'size' => $size * 2 ) );
					echo str_replace( 'avatar ', 'jet-avatar ', get_avatar( $user->ID, $size, '', $alt, $args ) );
				} elseif ( $user && 'WP_User' !== get_class( $user ) && is_user_logged_in() ) {
					$user = wp_get_current_user();
					$this->full_image_src = get_avatar_url( $user->ID, array( 'size' => $size * 2 ) );
					echo str_replace( 'avatar ', 'jet-avatar ', get_avatar( $user->ID, $size, '', $alt, $args ) );
				} else {
					return $this->process_fallback_image( $settings );
				}

			} elseif ( 'options_page' === $source ) {

				$option = ! empty( $settings['dynamic_field_option'] ) ? $settings['dynamic_field_option'] : false;
				$image  = jet_engine()->listings->data->get_option( $option );

				if ( ! $image ) {
					return $this->process_fallback_image( $settings );
				} else {

					$image_data = Jet_Engine_Tools::get_attachment_image_data_array( $image, 'id' );
					$image      = $image_data['id'];

					echo wp_get_attachment_image( $image, $size, false, array( 'alt' => $this->get_image_alt( $image, $settings ) ) );
				}

			} else {
				$this->render_image_by_meta_field( $source, $size, $settings );
			}

		}

		public function get_caption( $settings ) {

			$caption = $settings['image_caption'] ?? '';

			if ( ! $caption ) {
				return '';
			}

			$attr = array(
				'class' => $this->get_image_caption_css_class(),
			);

			return sprintf( '<figcaption %1$s>%2$s</figcaption>', \Jet_Engine_Tools::get_attr_string( $attr ), $caption );
		}

		/**
		 * Process image fallback if set or hide widget
		 *
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function process_fallback_image( $settings = array() ) {

			$size = $this->get_image_size( $settings );

			if ( ! empty( $settings['hide_if_empty'] ) ) {
				$this->show_field = false;
			} elseif ( ! empty( $settings['fallback_image'] ) ) {

				$attachment_id = is_array( $settings['fallback_image'] ) ? $settings['fallback_image']['id'] : $settings['fallback_image'];

				if ( empty( $attachment_id ) ) {
					return;
				}

				echo wp_get_attachment_image( $attachment_id, $size, false, array( 'alt' => $this->get_image_alt( $attachment_id, $settings ) ) );

			}

		}

		public function render_image_by_meta_field( $field = null, $size = 'full', $settings = array() ) {

			$custom_output = apply_filters(
				'jet-engine/listings/dynamic-image/custom-image',
				false,
				$this->get_settings(),
				$size,
				$this
			);

			$image = apply_filters( 'jet-engine/listings/dynamic-image/image-data', false, $this->get_settings() );

			// If image output is not totally rewritten,
			// but we already get image tag with the data - use it as custom output
			if ( ! $custom_output && is_array( $image ) && ! empty( $image['image_tag'] ) ) {
				$custom_output = $image['image_tag'];
			}

			if ( $custom_output ) {
				echo $custom_output;
				return;
			}

			$object_context = isset( $settings['object_context'] ) ? $settings['object_context'] : false;

			if ( ! $image && jet_engine()->relations->legacy->is_relation_key( $field ) ) {
				$related_post = get_post_meta( get_the_ID(), $field, false );
				if ( ! empty( $related_post ) ) {
					$related_post = $related_post[0];
					if ( has_post_thumbnail( $related_post ) ) {
						$image = get_post_thumbnail_id( $related_post );
					}
				}
			} elseif ( ! $image ) {
				$image = jet_engine()->listings->data->get_meta_by_context( $field, $object_context );
			}

			if ( ! empty( $image ) ) {
				$image = maybe_unserialize( $image );
			}

			if ( is_array( $image ) && isset( $image['url'] ) ) {

				if ( $size && 'full' !== $size ) {
					$image = $image['id'];
				} else {
					$image = $image['url'];
				}

			} elseif ( is_array( $image ) ) {
				$image = array_values( $image );
				$image = $image[0];
			}

			if ( ! $image ) {
				return $this->process_fallback_image( $settings );
			}

			if ( ! empty( $settings['image_url_prefix'] ) ) {
				$image = $settings['image_url_prefix'] . $image;
			}

			if ( filter_var( $image, FILTER_VALIDATE_URL ) ) {
				$this->print_image_html_by_src( $image );
			} else {
				echo wp_get_attachment_image( $image, $size, false, array( 'alt' => $this->get_image_alt( $image, $settings ) ) );
			}

		}

		public function print_image_html_by_src( $src = null, $alt = null ) {

			$settings   = $this->get_settings();
			$image_data = Jet_Engine_Tools::get_attachment_image_data_array( $src, 'id' );

			// If the image is not external print the image html through wp_get_attachment_image().
			if ( ! empty( $image_data ) && ! empty( $image_data['id'] ) ) {
				echo wp_get_attachment_image(
					$image_data['id'],
					$this->get_image_size( $settings ),
					false,
					array( 'alt' => $this->get_image_alt( $image_data['id'], $settings ) )
				);

				return;
			}

			$attr = $this->modify_image_attrs( array(
				'src'      => $src,
				'class'    => $this->get_image_css_class(),
				'alt'      => $alt,
				'decoding' => 'async',
			) );

			$this->full_image_src = $src;

			printf( '<img %s>', Jet_Engine_Tools::get_attr_string( $attr ) );
		}

		public function get_image_css_class() {
			return apply_filters( 'jet-engine/listings/dynamic-image/css-class', 'jet-listing-dynamic-image__img' );
		}

		public function get_image_caption_css_class() {
			return apply_filters( 'jet-engine/listings/dynamic-image/caption-css-class', 'jet-listing-dynamic-image__caption' );
		}

		public function get_image_size( $settings = array() ) {
			$size = isset( $settings['dynamic_image_size'] ) ? $settings['dynamic_image_size'] : 'full';

			return apply_filters( 'jet-engine/listings/dynamic-image/size', $size, 'dynamic_image', $settings );
		}

		public function get_image_alt( $img_id = null, $settings = array() ) {

			if ( empty( $img_id ) ) {
				return ! empty( $settings['custom_image_alt'] ) ? $settings['custom_image_alt'] : null;
			}

			$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

			if ( ! $alt ) {
				$image = get_post( $img_id );

				if ( $image ) {
					$alt = $image->post_title;
				}

				if ( ! $alt ) {
					$alt = get_the_title();
				}
			}

			return $alt;
		}

		public function get_custom_image_alt( $settings = array() ) {
			return ! empty( $settings['custom_image_alt'] ) ? $settings['custom_image_alt'] : '';
		}

		public function get_image_url( $settings ) {

			$is_linked = $this->get( 'linked_image' );

			if ( ! $is_linked ) {
				return false;
			}

			$source = ! empty( $settings['image_link_source'] ) ? $settings['image_link_source'] : '_permalink';
			$custom = ! empty( $settings['image_link_source_custom'] ) ? $settings['image_link_source_custom'] : false;
			$object_context = isset( $settings['object_context'] ) ? $settings['object_context'] : false;

			$url = apply_filters(
				'jet-engine/listings/dynamic-image/custom-url',
				false,
				$settings
			);

			if ( false !== $url ) {
				return $url;
			}

			if ( $custom ) {
				$url = jet_engine()->listings->data->get_meta_by_context( $custom, $object_context );
			} elseif ( '_permalink' === $source ) {
				$url = jet_engine()->listings->data->get_current_object_permalink(
					jet_engine()->listings->data->get_object_by_context( $object_context )
				);
			} elseif ( '_file' === $source ) {
				$url = $this->full_image_src;
			} elseif ( 'options_page' === $source ) {
				$option = ! empty( $settings['image_link_option'] ) ? $settings['image_link_option'] : false;
				$url    = jet_engine()->listings->data->get_option( $option );
			} elseif ( $source ) {
				$url = jet_engine()->listings->data->get_meta_by_context( $source, $object_context );
			}

			if ( is_array( $url ) ) {
				$url = $url[0];
			}

			if ( ! empty( $settings['link_url_prefix'] ) ) {
				$url = $settings['link_url_prefix'] . $url;
			}

			return $url;

		}

		public function render() {

			$base_class = $this->get_name();
			$settings   = $this->get_settings();

			$classes = array(
				'jet-listing',
				$base_class,
			);

			if ( ! empty( $settings['className'] ) ) {
				$classes[] = esc_attr( $settings['className'] );
			}

			$image_html = $this->get_image_html( $settings );

			if ( ! $this->show_field ) {
				return;
			}

			$custom_image_styles = ! empty( $settings['custom_image_styles'] ) ? $settings['custom_image_styles'] : array();
			$custom_styles = $this->get_parsed_custom_styles( $custom_image_styles, true );

			$custom_styles_attr = ! empty( $custom_styles['width'] ) ? 'style="' . $custom_styles['width'] . '"' : '';

			printf( '<div class="%1$s" %2$s>', implode( ' ', $classes ), $custom_styles_attr );

				do_action( 'jet-engine/listing/dynamic-image/before-image', $this );

				$image_url = $this->get_image_url( $settings );

				if ( $image_url ) {

					$open_in_new = isset( $settings['open_in_new'] ) ? $settings['open_in_new'] : '';
					$rel_attr    = isset( $settings['rel_attr'] ) ? esc_attr( $settings['rel_attr'] ) : '';

					$link_attr = array(
						'href'  => $image_url,
						'class' => $base_class . '__link',
					);

					if ( $rel_attr ) {
						$link_attr['rel'] = $rel_attr;
					}

					if ( $open_in_new ) {
						$link_attr['target'] = '_blank';
					}

					$link_attr = apply_filters( 'jet-engine/listings/dynamic-image/link-attr', $link_attr, $settings );

					printf( '<a %s>', Jet_Engine_Tools::get_attr_string( $link_attr ) );
				}

				echo $image_html;

				if ( $image_url ) {
					echo '</a>';
				}

				do_action( 'jet-engine/listing/dynamic-image/after-image', $this );

			echo '</div>';

		}

		public function get_image_html( $settings ) {

			ob_start();

			$this->add_image_hooks();

			$this->render_image( $settings );

			$rendered_image = ob_get_clean();

			$this->remove_image_hooks();

			if ( empty( $rendered_image ) ) {
				return $rendered_image;
			}

			$add_caption = $settings['add_image_caption'] ?? false;
			$add_caption = filter_var( $add_caption, FILTER_VALIDATE_BOOLEAN ) && ! empty( $settings['image_caption'] );

			if ( ! $add_caption ) {
				return $rendered_image;
			}

			$caption = $this->get_caption( $settings );

			if ( empty( $caption ) ) {
				return $rendered_image;
			}

			$figure = '';

			$caption_position = $settings['image_caption_position'] ?? 'after';

			if ( $caption_position === 'before' ) {
				$figure = sprintf( '<figure class="jet-listing-dynamic-image__figure">%s%s</figure>', $caption, $rendered_image );
			} else {
				$figure = sprintf( '<figure class="jet-listing-dynamic-image__figure">%s%s</figure>', $rendered_image, $caption );
			}

			return $figure;
		}

		public function add_image_hooks() {
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'modify_image_attrs' ) );
			add_filter( 'wp_get_attachment_image',            array( $this, 'store_full_image_src' ), 10, 2 );
		}

		public function remove_image_hooks() {
			remove_filter( 'wp_get_attachment_image_attributes', array( $this, 'modify_image_attrs' ) );
			remove_filter( 'wp_get_attachment_image',            array( $this, 'store_full_image_src' ), 10 );
		}

		/**
		 * Parse custom styles array to string.
		 *
		 * @param  array $styles Custom styles array.
		 * @return string
		 */
		public function get_parsed_custom_styles( $styles = array(), $as_array = false ) {

			$parsed_styles = array();
			$aspect_ratio = false;

			if ( ! empty( $styles['aspect_ratio'] ) ) {

				$scale = ! empty( $styles['custom_scale'] ) && in_array(
					$styles['custom_scale'], array( 'cover', 'contain' )
				) ? $styles['custom_scale'] : 'cover';
				$aspect_ratio = str_replace( ':', ' / ', $styles['aspect_ratio'] );

				$parsed_styles['object-fit'] = 'object-fit: ' . $scale;
				$parsed_styles['aspect-ratio'] = 'aspect-ratio: ' . esc_attr( $aspect_ratio );
			}

			if ( ! empty( $styles['width'] ) || false !== $aspect_ratio ) {
				$width = ! empty( $styles['width'] ) ? $styles['width'] : '100%';
				$parsed_styles['width'] = 'width: ' . esc_attr( $width );
			}

			if ( ! empty( $styles['height'] ) || false !== $aspect_ratio ) {
				$height = ! empty( $styles['height'] ) ? $styles['height'] : 'auto';
				$parsed_styles['height'] = 'height: ' . esc_attr( $height );
			}

			if ( $as_array ) {
				return $parsed_styles;
			} else {
				return implode( '; ', $parsed_styles );
			}
		}

		/**
		 * Modify image attributes for core WP function and inner render function.
		 *
		 * @param  array $attr Attrributes array.
		 * @return array
		 */
		public function modify_image_attrs( $attr ) {

			$settings = $this->get_settings();

			// Add CSS Class
			$attr['class'] = $this->get_image_css_class() . ' ' . esc_attr( $attr['class'] );

			// Add Custom Alt
			if ( ! empty( $settings['custom_image_alt'] ) ) {
				$attr['alt'] = esc_attr( $settings['custom_image_alt'] );
			}

			// Add Custom Styles
			if (
				! empty( $settings['custom_image_styles'] )
				&& is_array( $settings['custom_image_styles'] )
			) {
				$attr['style'] = $this->get_parsed_custom_styles( $settings['custom_image_styles'] );
			}

			// Modify the `loading` attr
			if ( isset( $settings['lazy_load_image'] ) ) {
				$lazy_load = filter_var( $settings['lazy_load_image'], FILTER_VALIDATE_BOOLEAN );

				$attr['loading'] = $lazy_load ? 'lazy' : 'eager';
			}

			return $attr;
		}

		public function store_full_image_src( $html, $attachment_id ) {

			$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );

			if ( $image_src && isset( $image_src[0] ) ) {
				$this->full_image_src = $image_src[0];
			}

			return $html;
		}

	}

}
