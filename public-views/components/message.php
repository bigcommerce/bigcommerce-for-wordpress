<?php

/**
 * Template for rendering messages
 *
 * @var string $content The contents of the message. May contain markup.
 * @var string $type The type of message, probably one of: notice, error, warning, success
 */

?>
<div class="bc-alert bc-alert--<?php echo sanitize_html_class( $type ); ?>">
	<?php echo $content; ?>
</div>
