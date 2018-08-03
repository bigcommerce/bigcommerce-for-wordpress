<?php
/**
 * Cart
 *
 * @package BigCommerce
 *
 * @var array  $cart
 * @var string $fallback_image The fallback image to use for items that do not have one
 * @var string $image_size     The image size to use for product images
 *
 */

use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

?>

<section class="bc-cart" data-js="bc-cart" data-cart_id="<?php echo esc_attr( $cart['cart_id'] ); ?>">
	<div class="bc-cart-error">
		<p class="bc-cart-error__message" data-js="bc-cart-error-message"></p>
	</div>
	<header class="bc-cart-header">
		<div class="bc-cart-header__item"><?php esc_html_e( 'Item', 'bigcommerce' ); ?></div>
		<div class="bc-cart-header__qty"><?php esc_html_e( 'Qty', 'bigcommerce' ); ?></div>
		<div class="bc-cart-header__price"><?php esc_html_e( 'Price', 'bigcommerce' ); ?></div>
	</header>
	<div class="bc-cart-body">
		<?php foreach ( $cart['items'] as $item ) { ?>
			<div class="bc-cart-item" data-js="<?php echo esc_attr( $item['id'] ); ?>">
				<div class="bc-cart-item-image">

					<?php if ( ! empty( $item['post_id'] ) ) { ?>
						<a href="<?php echo esc_url( get_the_permalink( $item['post_id'] ) ); ?>" class="bc-product__thumbnail-link">
					<?php } ?>

						<?php
						echo ( has_post_thumbnail( $item[ 'post_id' ] ) ? get_the_post_thumbnail( $item['post_id'], $image_size ) : $fallback_image );
						?>

					<?php if ( ! empty( $item['post_id'] ) ) { ?>
						</a>
					<?php } ?>

					<button class="bc-link bc-cart-item__remove-button" data-js="remove-cart-item" data-cart_item_id="<?php echo esc_attr( $item['id'] ); ?>" type="button">
						<?php esc_html_e( '(Remove)', 'bigcommerce' ); ?>
					</button>
				</div>
				<div class="bc-cart-item-title">
					<h3 class="bc-cart-item__product-title">
						<?php if ( ! empty( $item['post_id'] ) ) { ?>
							<a href="<?php echo esc_url( get_the_permalink( $item['post_id'] ) ); ?>" class="bc-product__title-link">
						<?php } ?>

							<?php echo esc_html( $item['name'] ); ?>
							<?php if ( $item['bigcommerce_condition'] ) { ?>
								<span class="bc-product-flag--grey"><?php echo esc_html( $item['bigcommerce_condition'][0]['label'] ); ?></span>
							<?php } ?>

						<?php if ( ! empty( $item['post_id'] ) ) { ?>
							</a>
						<?php } ?>
					</h3>

					<?php if ( ! empty( $item[ Brand::NAME ] ) ) {
						$brands = implode( _x( ', ', 'list separator', 'bigcommerce' ), wp_list_pluck( $item[ Brand::NAME ], 'label' ) );
						?>
						<span class="bc-cart-item__product-brand"><?php echo esc_html( sprintf( _n( '%s', '%s', count( $item[ Brand::NAME ] ) ), $brands ) ); ?></span>
					<?php } ?>

					<?php foreach ( $item['options'] as $option ) { ?>
						<span class="bc-cart-item__product-option">
							<span class="bc-cart-item__product-option-label"><?php echo esc_html( sprintf( _x( '%s: ', 'product option label', 'bigcommerce' ), $option['label'] ) ); ?></span>
							<span class="bc-cart-item__product-option-value"><?php echo esc_html( sprintf( _x( '%s', 'product option value', 'bigcommerce' ), $option['value'] ) ); ?></span>
						</span>
					<?php } ?>
				</div>

				<div class="bc-cart-item-quantity">
					<?php
					$max = ( 0 >= $item['maximum_quantity'] ) ? '' : $item['maximum_quantity'];
					$min = ( 0 <= $item['minimum_quantity'] ) ? 1  : $item['minimum_quantity'];
					?>
					<label for="bc-cart-item__quantity" class="u-bc-screen-reader-text"><?php esc_html_e( 'Quantity', 'bigcommerce' ); ?></label>
					<input
							type="number"
							name="bc-cart-item__quantity"
							class="bc-cart-item__quantity-input"
							data-js="bc-cart-item__quantity" data-cart_item_id="<?php echo esc_attr( $item['id'] ); ?>"
							value="<?php echo intval( $item['quantity'] ); ?>"
							min="<?php echo esc_attr( $min ); ?>"
							max="<?php echo esc_attr( $max ); ?>"
					>
				</div>
				<?php $price_classes = $item['on_sale'] ? 'bc-cart-item-total-price bc-cart-item--on-sale' : 'bc-cart-item-total-price'; ?>
				<div class="<?php echo esc_attr( $price_classes ); ?>">
					<?php echo esc_html( $item['total_sale_price']['formatted'] ); ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<footer class="bc-cart-footer">
		<div class="bc-cart-subtotal">
			<span class="bc-cart-subtotal__label"><?php esc_html_e( 'Subtotal: ', 'bigcommerce' ); ?></span>
			<span class="bc-cart-subtotal__amount"><?php echo esc_html( $cart['base_amount']['formatted'] ); ?></span>

		</div>
		<div class="bc-cart-actions">
			<form action="<?php echo esc_url( home_url( '/bigcommerce/checkout/' . $cart['cart_id'] ) ); ?>" method="post" enctype="multipart/form-data">
				<button type="submit" class="bc-btn bc-cart-actions__checkout-button" data-js="proceed-to-checkout"><?php esc_html_e( 'Proceed to Checkout', 'bigcommerce' ); ?></button>
			</form>
		</div>
	</footer>
</section>
