<?php
/**
 * Header bar template part.
 *
 * @package AMP
 * 
 * @version 1.0.0
 */

?>
<header id="top" class="amp-wp-header">
	<div>
	<?php if ( has_nav_menu( 'amp-menu' ) ) { ?>
		<button on="tap:sidebar-menu.toggle" tabindex="0" class="hamburger"></button>
	<?php } ?>
		<a href="<?php echo esc_url( $this->get( 'home_url' ) ); ?>">
			<?php $site_icon_url = $this->get( 'site_icon_url' ); ?>
			<?php if ( $site_icon_url ) : ?>
				<amp-img src="<?php echo esc_url( $site_icon_url ); ?>" width="32" height="32" class="amp-wp-site-icon"></amp-img>
			<?php endif; ?>
			<span class="amp-site-title">
				<?php echo esc_html( wptexturize( $this->get( 'blog_name' ) ) ); ?>
			</span>
		</a>
	</div>
</header>
<?php
if ( has_nav_menu( 'amp-menu' ) ) {
	?>
	<amp-sidebar id="sidebar-menu" layout="nodisplay" side="left">
		<div role="button" aria-label="close sidebar" on="tap:sidebar-menu.toggle" tabindex="0" class="close-sidebar">✕</div>
		<?php echo wp_kses( $this->get( 'header_nav_menu' ), 'bigcommerce/amp' ); ?>
	</amp-sidebar>
<?php
}
