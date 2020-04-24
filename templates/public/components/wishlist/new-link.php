<?php

/**
 * @var string $label         The button label
 * @var string $attributes    The rendered attributes for the link
 * @var string $form_template The rendered template for the wishlist form
 * @version 1.0.0
 */

?>
<a <?php echo $attributes; ?> class="bc-link bc-wish-list-btn--new"><?php echo esc_html( $label ); ?></a>
<?php echo $form_template; ?>
