<?php

/**
 * Template for rendering messages
 *
 * @var string $content The contents of the message. May contain markup.
 * @var string $type The type of message, probably one of: notice, error, warning, success
 * @var string $key The key/error code that accompanies the message
 */

?>
<div class="bc-alert bc-alert--<?php echo sanitize_html_class( $type ); ?>" data-message-key="<?php echo esc_attr( $key ); ?>">
	<?php echo $content; ?>
</div>
