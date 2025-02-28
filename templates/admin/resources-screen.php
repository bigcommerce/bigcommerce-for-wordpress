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
<div class="open-source-resources" data-js="open-source-resources-wrapper">
	<script id="open-source-resources" data-js="open-source-resources-json"><?php echo 'var open_source_resources_json = ' . wp_json_encode( $resources ); ?></script>
	<header class="open-source-resources-header bg-geometric-bg">

		<div class="open-source-resources-header--inner open-source-resources-tabs__max-width">
			<h1 class="open-source-resources-heading"><?php echo esc_html( $page_title ); ?></h1>

			<section class="open-source-resources-tabs">
				<div class="open-source-resources-tabs__header" data-js="open-source-resources-tabs-header">
					<ul class="open-source-resources-tabs__list open-source-resources-tabs__max-width" data-js="open-source-resources-tabs-list" role="tablist">
					</ul>
				</div>
			</section>
		</div>
	</header>

	<section class="open-source-resources-content" data-js="open-source-resources-tab-content">
	</section>
</div>
