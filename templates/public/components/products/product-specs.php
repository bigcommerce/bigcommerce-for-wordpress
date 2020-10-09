<?php
/**
 * @var array $specs
 * @version 1.0.0
 */
?>
<?php if ( ! empty( $specs ) ) { ?>
	<section class="bc-single-product__specifications">
		<h4 class="bc-single-product__section-title"><?php echo esc_html__( 'Specifications', 'bigcommerce' ); ?></h4>
		<ul class="bc-product__spec-list">
			<?php foreach ( $specs as $key => $value ) { ?>
				<li class="bc-product__spec">
					<span class="bc-product__spec-title"><?php echo esc_html( $key ); ?></span>
					<span class="bc-product__spec-value"><?php echo wp_kses( $value, $allowed_html ); ?></span>
				</li>
			<?php } ?>
		</ul>
	</section>
<?php } ?>