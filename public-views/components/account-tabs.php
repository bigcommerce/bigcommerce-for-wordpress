<?php

/**
 * Template for rendering account page navigation
 *
 * @var array[] $links An array of associative arrays of links with 'url', 'label', and 'current' keys
 */

?>
<aside class="bc-account-tabs">
	<ul class="bc-account-tabs__list">
		<?php foreach ( $links as $link ) { ?>
			<li class="bc-account-tabs__list-item <?php echo esc_attr( $link[ 'current' ] ? 'bc-account-tabs__list-item--current' : '' ); ?>">
				<a class="bc-link bc-account-tabs__link" href="<?php echo esc_url( $link[ 'url' ] ); ?>" title="<?php echo esc_attr( $link[ 'label' ] ); ?>"><?php echo esc_html( $link[ 'label' ] ); ?></a>
			</li>
		<?php } ?>
	</ul>
</aside>
