<?php
/**
 * Product Title
 *
 * @package Bigcommerce
 *
 * @var Product $product
 * @var string  $title           The product title
 * @var string  $permalink       The link to the single product page.
 * @var string  $condition       The rendered product condition
 * @var string  $inventory       The rendered product inventory level
 * @var bool    $use_permalink   Wrap the title in an anchor tag linked to the product single page.
 * @var string  $link_attributes Sanitized attributes save to add to the permalink anchor tag
 * @version 1.0.0
 */

use \BigCommerce\Post_Types\Product\Product;

?>
<h3 class="bc-product__title">
	<?php
	if ( $use_permalink ) {
		$permalink = amp_get_permalink( $product->post_id() );
	?>
	<a href="<?php echo esc_url( $permalink ); ?>" class="bc-product__title-link" <?php echo wp_kses( $link_attributes, 'bigcommerce/amp' ); ?>>
		<?php } ?>

		<?php echo wp_kses( $title, 'bigcommerce/amp' ); ?>
		<?php echo wp_kses( $condition, 'bigcommerce/amp' ); ?>
		<?php echo wp_kses( $inventory, 'bigcommerce/amp' ); ?>

		<?php if ( $use_permalink ) { ?>
	</a>
<?php } ?>
</h3>
