<?php
/**
 * @var Product $product
 * @var int     $attachment_id
 * @var string  $size
 * @var string  $image
 */

use BigCommerce\Post_Types\Product\Product;

?>


<div class="bc-product-card__featured-image">
	<?php if ( $product->on_sale() ) { ?>
		<span class="bc-product-flag--sale"><?php esc_html_e( 'SALE', 'bigcommerce' ); ?></span>
	<?php } ?>

	<?php
	echo $image;
	?>
</div>
