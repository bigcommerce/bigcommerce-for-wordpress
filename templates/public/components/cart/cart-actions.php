<?php
/**
 * Cart Actions
 *
 * @package BigCommerce
 *
 * @var array $cart
 * @var array $actions
 */

?>

<div class="bc-cart-actions">
	<form
			action="<?php echo esc_url( home_url( '/bigcommerce/checkout/' . $cart['cart_id'] ) ); ?>"
			method="post"
			enctype="multipart/form-data"
	>
		<?php foreach ( $actions as $action ) {
			echo $action;
		} ?>
	</form>
</div>
