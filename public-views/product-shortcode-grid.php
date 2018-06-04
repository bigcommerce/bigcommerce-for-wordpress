<?php
/**
 * Product Group Template.
 *
 * @var string[] $cards
 * @var string   $pagination
 * @var bool     $wrap Whether to wrap the output in a div
 * @var int      $columns The number of columns to use for the grid
 */

?>

<?php if ( $wrap ) { ?>
<div class="bc-load-items bc-shortcode-product-grid-wrapper">
	<?php if ( ! empty( $pagination ) ) { ?>
		<div class="bc-load-items__loader"></div>
	<?php } ?>
	<div class="bc-product-grid bc-product-grid--<?php echo intval( $columns ); ?>col bc-load-items-container <?php echo ( ! empty( $pagination ) ? esc_attr( 'bc-load-items-container--has-pages' ) : '' ); ?>">
		<?php } ?>

		<?php
		foreach ( $cards as $card ) {
			echo $card;
		}

		echo $pagination;

		?>

		<?php if ( $wrap ) { ?>

	</div>
</div>
<?php } ?>
