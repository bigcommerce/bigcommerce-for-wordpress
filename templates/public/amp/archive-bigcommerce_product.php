<?php
/**
 * Default Product Archive Template
 * Override this template in your own theme by creating a file at
 * [your-theme]/bigcommerce/archive-bigcommerce_product.php
 * 
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

amp_add_post_template_actions();
$post              = get_post();
$amp_post_template = new AMP_Post_Template( $post );

$amp_post_template->load_parts( array( 'html-start' ) );
$amp_post_template->load_parts( array( 'header' ) );
?>

<div class="amp-wp-article">
	<?php echo apply_filters( 'bigcommerce/template/product/archive', '' ); // WPCS: XSS ok. Already escaped data. ?>
</div>

<?php
$amp_post_template->load_parts( array( 'footer' ) );
$amp_post_template->load_parts( array( 'html-end' ) );
