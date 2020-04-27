<?php
/**
 * Address List Template
 *
 * @var array $addresses
 * @var string $new_address
 * @version 1.0.0
 */

?>

<!-- class="bc-account-addresses__list" is required -->
<ul class="bc-account-addresses__list">
	<?php foreach ( $addresses as $address ) { ?>
		<!-- class="bc-account-addresses__item" is required -->
		<li class="bc-account-addresses__item" data-js="bc-account-address-entry">

			<?php echo $address[ 'formatted' ]; ?>
			<?php echo $address[ 'actions' ]; ?>

		</li>
	<?php } ?>

	<!-- class="bc-account-addresses__item" is required -->
	<li class="bc-account-addresses__item bc-account-addresses__add-new" data-js="bc-account-addresses__add-new">
		<?php echo $new_address; ?>
	</li>
</ul>
