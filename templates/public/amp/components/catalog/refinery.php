<?php
/**
 * The template for rendering the search/sort/filter form
 *
 * @var string   $action  The form action URL
 * @var string   $search  The search box HTML
 * @var string   $sort    The sort box HTML
 * @var string[] $filters HTML for each of the filter selects
 * @version 1.0.0
 */

?>
<form id="bc-search-refinery" action="<?php echo esc_url( $action ); ?>" method="get" class="bc-form" target="_top">
	<?php echo wp_kses( $search, 'bigcommerce/amp' ); ?>
	<?php echo wp_kses( $sort, 'bigcommerce/amp' ); ?>
	<?php
	foreach ( $filters as $filter ) {
		echo wp_kses( $filter, 'bigcommerce/amp' );
	}
	?>
</form>
