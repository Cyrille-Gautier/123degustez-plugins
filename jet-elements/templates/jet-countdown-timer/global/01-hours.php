<?php echo $this->blocks_separator(); // phpcs:ignore  ?>
<div class="jet-countdown-timer__item item-hours">
	<div class="jet-countdown-timer__item-value" data-value="hours"><?php echo esc_html( $this->date_placeholder() ); ?></div>
	<?php $this->_html( 'label_hours', '<div class="jet-countdown-timer__item-label">%s</div>' ); // phpcs:ignore  ?>
</div>
