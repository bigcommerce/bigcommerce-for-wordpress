<?php
/**
 * Checkout
 *
 * @package BigCommerce
 *
 * @var string $embedded_checkout_url
 * @var string $styles
 *
 */
?>

<div id="embedded-checkout"></div>

<script>
	/*
	// NOTE: Below is a set of sample styling values for testing purpose.
	const styles = {
		body: {
			backgroundColor: '#444444',
		},

		text: {
			color: '#ffffff',
		},

		secondaryText: {
			color: '#bbbbbb',
		},

		heading: {
			color: '#eeeeee',
		},

		secondaryHeading: {
			color: '#ffffff',
		},

		link: {
			color: '#ff957f',

			hover: {
				color: '#fab8a2',
			},
		},

		label: {
			color: '#ffffff',

			error: {
				color: '#ff957f',
			},
		},

		button: {
			backgroundColor: '#ff957f',
			borderColor: '#ff957f',
			color: '#333333',

			active: {
				backgroundColor: '#e5735c',
				borderColor: '#e5735c',
				color: '#222222',
			},

			hover: {
				backgroundColor: '#f2836d',
				borderColor: '#f2836d',
				color: '#222222',
			},

			disabled: {
				backgroundColor: '#757575',
				borderColor: '#444444',
				color: '#757575',
			},
		},

		secondaryButton: {
			backgroundColor: '#444444',
			borderColor: '#757575',
			color: '#ffffff',

			active: {
				backgroundColor: '#222222',
				borderColor: '#666666',
				color: '#ffffff',
			},

			hover: {
				backgroundColor: '#333333',
				borderColor: '#757575',
				color: '#ffffff',
			},

			disabled: {
				backgroundColor: '#757575',
				borderColor: '#444444',
				color: '#757575',
			},
		},

		input: {
			backgroundColor: '#404040',
			borderColor: '#757575',
			boxShadow: 'inset 0 1px 1px #404040',
			color: '#ffffff',

			error: {
				borderColor: '#ff957f',
				color: '#ff957f',
			},

			placeholder: {
				color: '#999999',
			},
		},

		checkbox: {
			backgroundColor: '#404040',
			borderColor: '#757575',

			error: {
				color: '#ff957f',
			},
		},

		radio: {
			backgroundColor: '#404040',
			borderColor: '#757575',

			error: {
				color: '#ff957f',
			},
		},

		select: {
			backgroundColor: '#404040',
			borderColor: '#757575',
			color: '#ffffff',

			error: {
				color: '#ff957f',
			},
		},

		checklist: {
			backgroundColor: '#404040',
			borderColor: '#757575',
			color: '#ffffff',

			hover: {
				backgroundColor: '#505050',
			},

			checked: {
				backgroundColor: '#505050',
			},
		},

		discountBanner: {
			backgroundColor: '#e5e5e5',
			color: '#333333',
		},

		loadingBanner: {
			backgroundColor: '#333333',
			color: '#ffffff',
		},

		orderSummary: {
			backgroundColor: '#444444',
			borderColor: '#666666',
		},

		step: {
			borderColor: '#757575',

			icon: {
				backgroundColor: '#ff957f',
				color: '#333333',
			},
		},
	};
	*/

	document.addEventListener('DOMContentLoaded', function() {
		checkoutKit.embedCheckout({
			containerId: 'embedded-checkout',
			styles: JSON.parse(<?php echo wp_json_encode($styles) ?>),
			url: '<?php echo $embedded_checkout_url ?>',
		});
	});
</script>
