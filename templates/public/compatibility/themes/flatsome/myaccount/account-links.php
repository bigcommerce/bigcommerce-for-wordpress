<?php if ( count( $links ) ) : ?>
	<?php foreach ( $links as $link ) : ?>
		<li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a></li>
	<?php endforeach; ?>
<?php endif; ?>