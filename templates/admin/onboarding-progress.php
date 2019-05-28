<?php

/**
 * Displays progress through the onboarding workflow
 *
 * @var array[] $steps
 */
?>
<ol class="bc-onboarding-steps">
	<?php foreach ( $steps as $step ) { ?>
		<li class="bc-onboarding-step step-<?php echo $step['active']?'active':'inactive'; ?>">
			<span class="bc-onboarding-step-label"><?php echo esc_html( $step[ 'label' ] ); ?></span>
			<span class="bc-onboarding-step-dot"></span>
		</li>
	<?php } ?>
</ol>
