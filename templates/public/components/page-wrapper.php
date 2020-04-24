<?php

/**
 * The content-wrapper.php template is responsible for rendering everything that goes between
 * get_header() and get_footer() in the theme.
 *
 * @var string $content The contents of the page to render inside of the wrapper
 * @version 1.0.0
 */
?>
<main id="main" class="site-main" role="main">
	<div class="entry-content">
		<?php echo $content; ?>
	</div>
</main><!-- .site-main -->