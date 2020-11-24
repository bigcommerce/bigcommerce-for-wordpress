<?php
/**
 * Cart Items
 *
 * @package BigCommerce
 *
 * @var array $cart
 * @var string $fallback_image The fallback image to use for items that do not have one
 * @var string $image_size     The image size to use for product images
 * @version 1.0.0
 */

use BigCommerce\Taxonomies\Brand\Brand;

?>

<?php foreach ( $cart['items'] as $item ) { ?>
	<div class="bc-cart-item" data-js="<?php echo esc_attr( $item['id'] ); ?>">
		<div class="bc-cart-item-image">

			<?php if ( ! empty( $item['post_id'] ) ) { ?>
			<a
					href="<?php echo esc_url( get_the_permalink( $item['post_id'] ) ); ?>"
					class="bc-product__thumbnail-link"
			>
				<?php } ?>

				<?php
				echo( $item['thumbnail_id'] ? wp_get_attachment_image( $item['thumbnail_id'], $image_size ) : $fallback_image );
				?>

				<?php if ( ! empty( $item['post_id'] ) ) { ?>
			</a>
		<?php } ?>

			<!-- data-js="remove-cart-item" and class="bc-cart-item__remove-button" are required -->
			<button
					class="bc-link bc-cart-item__remove-button"
					data-js="remove-cart-item"
					data-cart_item_id="<?php echo esc_attr( $item['id'] ); ?>"
					type="button"
			>
				<?php esc_html_e( '(Remove)', 'bigcommerce' ); ?>
			</button>
		</div>
		<div class="bc-cart-item-meta">
			<h3 class="bc-cart-item__product-title">
				<?php if ( ! empty( $item['post_id'] ) ) { ?>
				<a
						href="<?php echo esc_url( get_the_permalink( $item['post_id'] ) ); ?>"
						class="bc-product__title-link"
				>
					<?php } ?>

					<?php echo esc_html( $item['name'] ); ?>
					<?php if ( $item['show_condition'] && $item['bigcommerce_condition'] ) { ?>
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

			<?php if ( ! empty( $item['options'] ) ) { ?>
				<div class="bc-cart-item__product-options">
					<?php foreach ( $item['options'] as $option ) { ?>
						<span class="bc-cart-item__product-option">
								<span class="bc-cart-item__product-option-label"><?php echo esc_html( sprintf( _x( '%s: ', 'product option label', 'bigcommerce' ), $option['label'] ) ); ?></span>
								<span class="bc-cart-item__product-option-value"><?php echo esc_html( sprintf( _x( '%s', 'product option value', 'bigcommerce' ), $option['value'] ) ); ?></span>
							</span>
					<?php } ?>
				</div>
			<?php } ?>
		</div>

		<div class="bc-cart-item-quantity">
			<?php
			$max = ( 0 >= $item['maximum_quantity'] ) ? '' : $item['maximum_quantity'];
			$min = ( 0 <= $item['minimum_quantity'] ) ? 1 : $item['minimum_quantity'];
			?>
			<label
					for="bc-cart-item__quantity"
					class="u-bc-screen-reader-text"
			><?php esc_html_e( 'Quantity', 'bigcommerce' ); ?></label>

			<!-- data-js="bc-cart-item__quantity" is required -->
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
		<!-- class="bc-cart-item-total-price" is required -->
		<?php $price_classes = $item['on_sale'] ? 'bc-cart-item-total-price bc-cart-item--on-sale' : 'bc-cart-item-total-price'; ?>
		<div class="<?php echo esc_attr( $price_classes ); ?>">
			<?php echo esc_html( $item['total_sale_price']['formatted'] ); ?>
		</div>
	</div>
<?php } ?>
