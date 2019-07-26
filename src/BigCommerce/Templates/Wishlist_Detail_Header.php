<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Wishlist;

class Wishlist_Detail_Header extends Controller {
	const WISHLIST = 'wishlist';
	const TITLE    = 'title';
	const SHARE    = 'share';
	const EDIT     = 'edit';
	const DELETE   = 'delete';
	const ACTIONS  = 'actions';

	protected $template = 'components/wishlist/detail-header.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::WISHLIST => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Wishlist $wishlist */
		$wishlist = $this->options[ self::WISHLIST ];

		return [
			self::WISHLIST => $wishlist,
			self::TITLE    => $wishlist->name(),
			self::SHARE    => $this->render_share_form( $wishlist ),
			self::EDIT     => $this->render_edit_form( $wishlist ),
			self::DELETE   => $this->render_delete_form( $wishlist ),
			self::ACTIONS  => $this->render_actions( $wishlist ),
		];
	}

	private function render_share_form( Wishlist $wishlist ) {
		if ( ! $wishlist->is_public() ) {
			return '';
		}

		$controller = Wishlist_Detail_Share::factory( [
			Wishlist_Detail_Share::WISHLIST => $wishlist,
		] );

		return $controller->render();
	}

	private function render_edit_form( Wishlist $wishlist ) {
		$controller = Wishlist_Edit::factory( [
			Wishlist_Edit::WISHLIST => $wishlist,
		] );

		return $controller->render();
	}

	private function render_delete_form( Wishlist $wishlist ) {
		$controller = Wishlist_Delete::factory( [
			Wishlist_Delete::WISHLIST => $wishlist,
		] );

		return $controller->render();
	}

	private function render_actions( Wishlist $wishlist ) {
		$actions = [
			'edit'   => $this->get_edit_wishlist_action( $wishlist ),
			'delete' => $this->get_delete_wishlist_action( $wishlist ),
		];

		/**
		 * Filter the action links that are displayed on a wishlist
		 * detail page
		 *
		 * @param string[] $actions  The rendered action links
		 * @param Wishlist $wishlist The wishlist being rendered
		 */
		return apply_filters( 'bigcommerce/wishlist/detail/actions', $actions, $wishlist );
	}

	private function get_edit_wishlist_action( Wishlist $wishlist ) {
		return sprintf( '<a href="#"
			class="bc-link"
			data-js="bc-wish-list-dialog-trigger"
			data-trigger="bc-edit-wish-list-form--%1$d"
			data-content="bc-edit-wish-list-form--%1$d"
		>%2$s</a>', $wishlist->list_id(), esc_html__( 'Edit', 'bigcommerce' ) );
	}

	private function get_delete_wishlist_action( Wishlist $wishlist ) {
		return sprintf( '<a href="#"
			class="bc-link"
			data-js="bc-wish-list-dialog-trigger"
			data-trigger="bc-delete-wish-list-form--%1$d"
			data-content="bc-delete-wish-list-form--%1$d"
		>%2$s</a>', $wishlist->list_id(), esc_html__( 'Delete', 'bigcommerce' ) );
	}

}
