<?php
/**
 * AMP Product Single Template
 * Override this template in your own theme by creating a file at
 * [your-theme]/bigcommerce/amp/single-bigcommerce_product.php
 * 
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$post              = get_post();
$amp_post_template = new AMP_Post_Template( $post );

$amp_post_template->load_parts( array( 'html-start' ) );
$amp_post_template->load_parts( array( 'header' ) );
?>

	<article class="amp-wp-article">
		<div class="amp-wp-article-content">
			<?php echo apply_filters( 'bigcommerce/template/product/single', '', $post->ID ); // WPCS: XSS ok. Already escaped data. ?>
		</div>
	</article>

<?php
$amp_post_template->load_parts( array( 'footer' ) );
$amp_post_template->load_parts( array( 'html-end' ) );
