<?php

use BigCommerce\Accounts\Wishlists\Wishlist;
use BigCommerce\Post_Types\Product\Product;

/**
 * Template for a single wishlist product row
 *
 * @var Product  $product
 * @var Wishlist $wishlist
 * @var string   $title
 * @var int      $image_id
 * @var string   $image
 * @var string   $price
 * @var string   $sku
 * @var string[] $bigcommerce_brand
 * @var string[] $bigcommerce_condition
 * @var string   $permalink
 * @var string   $delete URL to remove the product from the wishlist
 * @version 1.0.0
 */

?>

<?php if ( $image ) { ?>
	<div class="bc-wish-list-product-row__image">
		<?php echo $image; ?>
	</div>
<?php } ?>

<div class="bc-wish-list-product-row__body">
	<div class="bc-wish-list-product-row__header">
		<h3 class="bc-wish-list-product-row__title">
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

</div>

<div class="bc-wish-list-product-row__delete"><a href="<?php echo esc_url( $delete ); ?>" class="bc-link"><?php esc_html_e( 'Remove Item', 'bigcommerce' ); ?></a></div>

<div class="bc-wish-list-product-row__price"><?php echo esc_html( $price ); ?></div>

