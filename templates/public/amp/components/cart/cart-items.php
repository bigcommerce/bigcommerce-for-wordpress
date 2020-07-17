<?php
/**
 * Cart Items
 *
 * @package BigCommerce
 *
 * @var string $fallback_image The fallback image to use for items that do not have an image.
 * @var string $proxy_base     The proxy base path for REST JSON requests.
 * @version 1.0.0
 */

use BigCommerce\Cart\Cart;

?>
<amp-list
	layout="fixed-height"
	height="600"
	id="product-list"
	src="<?php echo esc_url( rest_url( sprintf( '/%s/amp-cart?cart_id=CLIENT_ID(%s)', $proxy_base, Cart::CART_COOKIE ) ) ); ?>"
	class="bc-cart-body"
	reset-on-refresh
	>
	<div fallback>
		<div class="bc-cart__empty">
			<h2 class="bc-cart__title--empty"><?php esc_html_e( 'Your cart is empty.', 'bigcommerce' ); ?></h2>
			<a href="<?php echo esc_url( home_url() ); ?>" class="bc-cart__continue-shopping">
				<?php esc_html_e( 'Take a look around.', 'bigcommerce' ); ?>
			</a>
		</div>
	</div>
	<template type="amp-mustache">
		<div class="bc-cart-item" id="item-{{ id }}">
			<div class="bc-cart-item-image">
				<a [href]="'{{ permalink }}'" class="bc-product__thumbnail-link">
					{{ #thumbnail_src }}
						<amp-img
							srcset="{{ thumbnail_srcset }}"
							src="'{{ thumbnail_src }}'"
							layout="intrinsic"
							width="{{ thumbnail_width }}"
							height="{{ thumbnail_height }}"
							alt="{{ name }}">
						</amp-img>
					{{ /thumbnail_src }}
					{{ ^thumbnail_src }}
						<?php echo wp_kses( $fallback_image, 'bigcommerce/amp' ); ?>
					{{ /thumbnail_src }}
				</a>
				<form
					action-xhr="<?php echo esc_url( rest_url( sprintf( '/%s/carts/_cart_id_/items/', $proxy_base ) ) ); ?>{{ id }}?delete=1'"
					method="post"
					on="submit:AMP.setState({savingItem: true});submit-success:product-list.refresh,subtotal.refresh,AMP.setState({savingItem: false})"
					>
					<input type="hidden" name="cartId" value="CLIENT_ID(bigcommerce_cart_id)" data-amp-replace="CLIENT_ID" />
					<button
							class="bc-link bc-cart-item__remove-button"
							type="submit"
					>
						<?php esc_html_e( '(Remove)', 'bigcommerce' ); ?>
					</button>
				</form>
			</div>

			<div class="bc-cart-item-meta">
				<h3 class="bc-cart-item__product-title">
					<a [href]="'{{ permalink }}'" class="bc-product__title-link">
						{{{ name }}}
					</a>
				</h3>
				{{ #brand }}
					<span class="bc-cart-item__product-brand">{{ brand }}</span>
				{{ /brand }}
			</div>

			<div class="bc-cart-item-quantity">
				{{ quantity }}
			</div>

			<div class="bc-cart-item-total-price">
				{{ price }}
			</div>
		</div>
	</template>
</amp-list>
