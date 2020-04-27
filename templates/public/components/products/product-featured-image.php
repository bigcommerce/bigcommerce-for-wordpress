<?php
/**
 * @var Product $product
 * @var int     $attachment_id
 * @var string  $size
 * @var string  $image
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>

<?php if ( $product->on_sale() ) { ?>
	<span class="bc-product-flag--sale"><?php esc_html_e( 'SALE', 'bigcommerce' ); ?></span>
<?php } ?>

<?php
echo $image;
?>
