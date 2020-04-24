<?php
/**
 * View Cart Action
 *
 * @package BigCommerce
 *
 * @var array  $cart
 * @var string $href URL to the cart page
 * @version 1.0.0
 */

?>
<a href="<?php echo esc_url( $href ); ?>" class="bc-btn bc-cart-actions__view-button"><?php esc_html_e( 'View Cart', 'bigcommerce' ); ?></a>
