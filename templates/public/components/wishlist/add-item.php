<?php

/**
 * @var string $heading     The heading for the list
 * @var array  $links       Links to add the product to lists. Each link is an associative array with the keys:
 *                              label - The name of the list
 *                              url   - The URL to add the product to the list
 * @var string $create_list The link and template to create a new list
 * @version 1.0.0
 */
?>

<button type="button" class="bc-btn bc-pdp-wish-list-toggle" data-js="bc-pdp-wish-list-toggle">
	<?php echo esc_html( $heading ); ?><i class="bc-icon icon-bc-chevron-down"></i>
</button>

<ul class="bc-pdp-wish-lists" data-js="bc-pdp-wish-lists">
	<?php foreach ( $links as $link ) { ?>
		<li class="bc-wish-lists-item"><a href="<?php echo esc_url( $link['url'] ); ?>" class="bc-wish-list-item-anchor"><?php echo esc_html( $link['label'] ); ?></a></li>
	<?php } ?>
	<li class="bc-wish-lists-item"><?php echo $create_list; ?></li>
</ul>
