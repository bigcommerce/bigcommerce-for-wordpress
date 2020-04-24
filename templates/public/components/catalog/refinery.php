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
<form action="<?php echo esc_url( $action ); ?>" method="get" class="bc-form">
	<?php echo $search; ?>
	<?php echo $sort; ?>
	<?php foreach ( $filters as $filter ) {
		echo $filter;
	} ?>
</form>
