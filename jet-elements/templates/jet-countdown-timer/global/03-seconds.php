<?php echo $this->blocks_separator(); // phpcs:ignore ?>
<div class="jet-countdown-timer__item item-seconds">
	<div class="jet-countdown-timer__item-value" data-value="seconds"><?php echo esc_html( $this->date_placeholder() ); ?></div>
	<?php $this->_html( 'label_sec', '<div class="jet-countdown-timer__item-label">%s</div>' ); // phpcs:ignore ?>
</div>
