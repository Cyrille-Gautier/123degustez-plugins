<?php
/**
 * Main cart template
 */

$widget_settings = [
	'triggerType'  => $settings['trigger_type'],
	'openMiniCartOnAdd'  => $settings['open_minicart_on_add'],
	'closeOnClickOutside'  => $settings['close_on_click_outside'],
];

$classes = [
    'jet-blocks-cart',
    'jet-blocks-cart--' . $settings['layout_type'] . '-layout',
];

$class_string = implode( ' ', $classes );

?><div class="<?php echo esc_attr( $class_string ); ?>" data-settings="<?php echo htmlspecialchars( json_encode( $widget_settings ) ); ?>">
	<div class="jet-blocks-cart__heading"><?php
		include $this->__get_global_template( 'cart-link' );
	?></div>

	<?php if ( 'yes' === $settings['show_cart_list'] ) {
		include $this->__get_global_template( 'cart-list' );
	} ?>
</div>