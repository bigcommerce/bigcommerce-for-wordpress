<?php
/**
 * Cart Actions
 *
 * @package BigCommerce
 *
 * @var array $cart
 * @var array $actions
 * @version 1.0.0
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
