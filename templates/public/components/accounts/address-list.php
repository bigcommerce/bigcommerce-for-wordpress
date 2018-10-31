<?php
/**
 * Address List Template
 *
 * @var array $addresses
 * @var string $new_address
 */

?>

<section class="bc-account-addresses" data-js="bc-account-addresses">

	<ul class="bc-account-addresses__list">
		<?php foreach ( $addresses as $address ) { ?>
			<li class="bc-account-addresses__item" data-js="bc-account-address-entry">

				<?php echo $address[ 'formatted' ]; ?>
				<?php echo $address[ 'actions' ]; ?>

			</li>
		<?php } ?>

		<li class="bc-account-addresses__item bc-account-addresses__add-new" data-js="bc-account-addresses__add-new">
			<?php echo $new_address; ?>
		</li>
	</ul>
</section>
