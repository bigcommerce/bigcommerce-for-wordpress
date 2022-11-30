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
					<input type="hidden" name="cartId" value="<?php echo sprintf( 'CLIENT_ID(%s)', Cart::CART_COOKIE ) ?>" data-amp-replace="CLIENT_ID" />
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

				{{ #options }}
				<div class="bc-cart-item__product-options">
					<span class="bc-cart-item__product-option">
						<span class="bc-cart-item__product-option-label">{{ name }}</span>
						<span class="bc-cart-item__product-option-value">{{ value }}</span>
					</span>
				</div>
				{{ /options }}
			</div>

			<div class="bc-cart-item-quantity">
				<form
					id="item-{{ id }}-quantity"
					action-xhr="<?php echo esc_url( rest_url( sprintf( '/%s/carts/_cart_id_/items/', $proxy_base ) ) ); ?>{{ id }}?qty=true'"
					on="submit:AMP.setState({savingItem: true});submit-success:product-list.refresh,subtotal.refresh,AMP.setState({savingItem: false})"
					method="post"
				>
					<input type="hidden" name="product_id" value="{{ product_id }}" />
					<input type="hidden" name="variant_id" value="{{ variant_id }}" />
					<input type="hidden" name="cartId" value="CLIENT_ID(<?php echo Cart::CART_COOKIE; ?>)" data-amp-replace="CLIENT_ID" />
					<input
						type="number"

						name="quantity"
						class="bc-cart-item__quantity-input"
						data-js="bc-cart-item__quantity"
						value="{{ quantity }}"
						on="change:item-{{ id }}-quantity.submit"
						min="1"
					/>
				</form>
			</div>

			<div class="bc-cart-item-total-price">
				{{ price }}
			</div>
		</div>
	</template>
</amp-list>
