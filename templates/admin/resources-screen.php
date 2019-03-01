<?php

/**
 * Template for the admin Resources screen
 *
 * @var array $resources
 * @var string $page_title
 * @var string $page_header
 */
?>
<?php echo $page_header; ?>
<div class="bc-resources" data-js="bc-resources-wrapper">
	<script id="bigcommerce-resources" data-js="bigcommerce-resources-json"><?php echo 'var bigcommerce_resources_json = ' . wp_json_encode( $resources ); ?></script>
	<header class="bc-resources-header bg-geometric-bg">

		<div class="bc-resources-header--inner bc-resources-tabs__max-width">
			<h1 class="bc-resources-heading"><?php echo esc_html( $page_title ); ?></h1>

			<section class="bc-resources-tabs">
				<div class="bc-resources-tabs__header" data-js="bc-resources-tabs-header">
					<ul class="bc-resources-tabs__list bc-resources-tabs__max-width" data-js="bc-resources-tabs-list" role="tablist">
					</ul>
				</div>
			</section>
		</div>
	</header>

	<section class="bc-resources-content" data-js="bc-resources-tab-content">
	</section>
</div>
