<?php
/**
 * Template for a single order's details
 *
 * @var string   $title
 * @var int      $image_id
 * @var string   $image
 * @var int      $quantity_ordered
 * @var int      $quantity_shipped
 * @var string   $unit_price
 * @var string   $total_price
 * @var string   $sku
 * @var array[]  $options
 * @var string[] $bigcommerce_brand
 * @var string[] $bigcommerce_condition
 * @var string   $permalink
 * @version 1.0.0
 */

?>

<div class="bc-order-product-row">
	<?php if ( $image ) { ?>
			<div class="bc-order-product-row__image">
				<?php if ( $permalink ) { ?>
					<a href="<?php echo esc_url( $permalink ); ?>" class="bc-product__thumbnail-link">
				<?php } ?>

				<?php echo $image; ?>

				<?php if ( $permalink ) { ?>
					</a>
				<?php } ?>
			</div>
	<?php } ?>

	<div class="bc-order-product-row__body">
		<div class="bc-order-product-row__header">
			<h3 class="bc-order-product-row__title">
				<?php if ( $permalink ) { ?>
					<a href="<?php echo esc_url( $permalink ); ?>" class="bc-product__title-link">
				<?php } ?>

				<?php echo esc_html( $title ); ?>
				<?php if ( $bigcommerce_condition ) { ?>
					<?php foreach ( $bigcommerce_condition as $condition ) { ?>
						<span class="bc-product-flag--grey"><?php echo esc_html( $condition ); ?></span>
					<?php } ?>
				<?php } ?>

				<?php if ( $permalink ) { ?>
					</a>
				<?php } ?>
			</h3>
		</div>

		<?php if ( $bigcommerce_brand ) { ?>
			<div class="bc-order-product-row__brand">
				<?php echo implode( esc_html( _x( ', ', 'brand name separator', 'bigcommerce' ) ), $bigcommerce_brand ); ?>
			</div>
		<?php } ?>

		<?php if ( $unit_price ) { ?>
			<div class="bc-order-product-row__unit-price"><?php echo esc_html( $unit_price ); ?></div>
		<?php } ?>

		<?php if ( $options ) { ?>
			<ul class="bc-order-product-row__options-list">
				<?php foreach ( $options as $option ) { ?>
					<li class="bc-order-product-row__option">
						<span class="bc-order-product-row__option-label"><?php echo esc_html( sprintf( _x( '%s: ', 'product option label', 'bigcommerce' ), $option['label'] ) ); ?></span>
						<span class="bc-order-product-row__option-value"><?php echo esc_html( sprintf( _x( '%s', 'product option value', 'bigcommerce' ), $option['value'] ) ); ?></span>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>

	<div class="bc-order-product-row__quantity">
		<div><?php esc_html_e( 'Quantity', 'bigcommerce' ); ?></div>
		<?php echo esc_html( $quantity_ordered ); ?>
	</div>

	<div class="bc-order-product-row__total"><?php echo esc_html( $total_price ); ?></div>
</div>
