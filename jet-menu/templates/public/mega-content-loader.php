<?php
$gradient_id = 'linear-gradient-' . uniqid();
?>

<svg class="jet-sub-mega-menu__loader" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="24px" height="25px" viewBox="0 0 128 128" xml:space="preserve">
	<g>
		<linearGradient id="<?php echo esc_attr( $gradient_id ); ?>">
			<stop offset="0%" stop-color="#3a3a3a" stop-opacity="0"/>
			<stop offset="100%" stop-color="#3a3a3a" stop-opacity="1"/>
		</linearGradient>
	<path d="M63.85 0A63.85 63.85 0 1 1 0 63.85 63.85 63.85 0 0 1 63.85 0zm.65 19.5a44 44 0 1 1-44 44 44 44 0 0 1 44-44z" fill="url(#<?php echo esc_attr( $gradient_id ); ?>)" fill-rule="evenodd"/>
	<animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="1080ms" repeatCount="indefinite"></animateTransform>
	</g>
</svg>
