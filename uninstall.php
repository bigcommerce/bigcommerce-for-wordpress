<?php

namespace BigCommerce\Uninstall;

use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\User_Profile_Settings;
use BigCommerce\Customizer;
use BigCommerce\Import\Processors\Channel_Initializer;
use BigCommerce\Import\Processors\Listing_Fetcher;
use BigCommerce\Import\Processors\Product_Data_Fetcher;
use BigCommerce\Import\Processors\Term_Import;
use BigCommerce\Import\Processors\Term_Purge;
use BigCommerce\Import\Runner\Lock;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Merchant;
use BigCommerce\Nav_Menu\Nav_Items_Meta_Box;
use BigCommerce\Pages;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Schema;
use BigCommerce\Settings;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;
use BigCommerce\Webhooks\Product_Update_Webhook;
use BigCommerce\Webhooks\Webhook;
use BigCommerce\Webhooks\Webhook_Versioning;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

function uninstall() {
	set_pages_to_draft();
	delete_products();
	delete_terms();
	delete_tables();
	delete_options();
	delete_user_meta_data();
	flush_rewrites();
}

function set_pages_to_draft() {
	$pages = [
		Pages\Account_Page::NAME,
		Pages\Address_Page::NAME,
		Pages\Cart_Page::NAME,
		Pages\Check_Balance_Page::NAME,
		Pages\Checkout_Page::NAME,
		Pages\Gift_Certificate_Page::NAME,
		Pages\Login_Page::NAME,
		Pages\Orders_Page::NAME,
		Pages\Registration_Page::NAME,
		Pages\Shipping_Returns_Page::NAME,
		Pages\Wishlist_Page::NAME,
	];
	foreach ( $pages as $option ) {
		$page_id = \get_option( $option );
		if ( $page_id ) {
			$post              = \get_post( $page_id );
			$post->post_status = 'draft';
			$post->post_name   .= '-uninstalled';
			\wp_update_post( $post );
		}
		\delete_option( $option );
	}
}

function delete_products() {
	/** @var \wpdb $wpdb */
	global $wpdb;
	$product_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type=%s", Product::NAME ) );
	foreach ( $product_ids as $product ) {
		delete_images( $product );
		\wp_delete_post( $product );
	}
}

function delete_images( $post_id ) {
	$image_ids = \get_posts( [
		'post_type'   => 'attachment',
		'post_parent' => $post_id,
		'meta_query'  => [
			[
				'key'     => 'bigcommerce_id',
				'compare' => '>',
				'value'   => 0,
			],
		],
		'fields'      => 'ids',
	] );
	foreach ( $image_ids as $image ) {
		\wp_delete_attachment( $image, true );
	}
}

function get_taxonomy_list() {
	return [
		Availability::NAME,
		Brand::NAME,
		Condition::NAME,
		Flag::NAME,
		Product_Category::NAME,
		Product_Type::NAME,
	];
}

function delete_terms() {
	foreach ( get_taxonomy_list() as $tax ) {
		$terms = \get_terms( [
			'taxonomy'   => $tax,
			'hide_empty' => false,
			'fields'     => 'ids',
		] );
		foreach ( $terms as $term_id ) {
			\wp_delete_term( $term_id, $tax );
		}
		\clean_taxonomy_cache( $tax );
	}
}

function delete_tables() {
	/** @var \wpdb $wpdb */
	global $wpdb;
	$tables = [
		Schema\Reviews_Table::NAME,
	];

	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$table}" );
	}
}

function delete_options() {
	$options = [
		Settings\Sections\Api_Credentials::OPTION_ACCESS_TOKEN,
		Settings\Sections\Api_Credentials::OPTION_CLIENT_ID,
		Settings\Sections\Api_Credentials::OPTION_CLIENT_SECRET,
		Settings\Sections\Api_Credentials::OPTION_STORE_URL,
		Settings\Sections\Channels::CHANNEL_ID,
		Settings\Sections\Channels::CHANNEL_NAME,
		Settings\Sections\New_Account_Section::STORE_INFO,
		Settings\Screens\Create_Account_Screen::SUBMITTED_DATA,
		Settings\Screens\Nav_Menu_Screen::COMPLETE_FLAG,
		Settings\Screens\Store_Type_Screen::NAME,
		Settings\Sections\Cart::OPTION_ENABLE_CART,
		Settings\Sections\Cart::OPTION_AJAX_CART,
		Settings\Sections\Cart::OPTION_CART_PAGE_ID,
		Settings\Sections\Import::OPTION_FREQUENCY,
		Settings\Sections\Import::OPTION_NEW_PRODUCTS,
		Settings\Sections\Import::ENABLE_WEBHOOKS,
		Settings\Sections\Account_Settings::SUPPORT_EMAIL,
		Settings\Sections\Analytics::SYNC_ANALYTICS,
		Settings\Sections\Analytics::FACEBOOK_PIXEL,
		Settings\Sections\Analytics::GOOGLE_ANALYTICS,
		Settings\Sections\Analytics::SEGMENT,
		Settings\Sections\Currency::CURRENCY_CODE,
		Settings\Sections\Currency::CURRENCY_SYMBOL,
		Settings\Sections\Currency::CURRENCY_SYMBOL_POSITION,
		Settings\Sections\Currency::DECIMAL_UNITS,
		Settings\Sections\Currency::INTEGER_UNITS,
		Settings\Sections\Currency::ENABLED_CURRENCIES,
		Settings\Sections\Gift_Certificates::OPTION_ENABLE,
		Settings\Sections\Troubleshooting_Diagnostics::LOG_ERRORS,
		Settings\Sections\Units::MASS,
		Settings\Sections\Units::LENGTH,
		'schema-' . Schema\Reviews_Table::class,
		'schema-' . Schema\User_Roles::class,
		'schema-' . Webhook_Versioning::class,
		Status::CURRENT_LOG,
		Status::PREVIOUS_LOG,
		Lock::OPTION,
		Settings\Import_Status::IMPORT_TOTAL_PRODUCTS,
		Listing_Fetcher::STATE_OPTION,
		Listing_Fetcher::PRODUCT_LISTING_MAP,
		Channel_Initializer::STATE_OPTION,
		Product_Data_Fetcher::STATE_OPTION,
		Product_Data_Fetcher::FILTERED_LISTING_MAP,
		Term_Purge::STATE_OPTION,
		Merchant\Onboarding_Api::ACCOUNT_ID,
		Merchant\Onboarding_Api::STORE_ID,
		Merchant\Onboarding_Api::AUTH_KEY,
		Customizer\Sections\Buttons::ADD_TO_CART,
		Customizer\Sections\Buttons::BUY_NOW,
		Customizer\Sections\Buttons::CHOOSE_OPTIONS,
		Customizer\Sections\Buttons::VIEW_PRODUCT,
		Customizer\Sections\Product_Archive::ARCHIVE_TITLE,
		Customizer\Sections\Product_Archive::ARCHIVE_SLUG,
		Customizer\Sections\Product_Archive::CATEGORY_SLUG,
		Customizer\Sections\Product_Archive::BRAND_SLUG,
		Customizer\Sections\Product_Archive::ARCHIVE_DESCRIPTION,
		Customizer\Sections\Product_Archive::SORT_OPTIONS,
		Customizer\Sections\Product_Archive::FILTER_OPTIONS,
		Customizer\Sections\Product_Archive::GRID_COLUMNS,
		Customizer\Sections\Product_Archive::PER_PAGE,
		Customizer\Sections\Product_Archive::QUICK_VIEW,
		Customizer\Sections\Product_Single::RELATED_COUNT,
		Customizer\Sections\Product_Single::DEFAULT_IMAGE,
		Customizer\Sections\Product_Single::PRICE_DISPLAY,
		Customizer\Sections\Product_Single::INVENTORY_DISPLAY,
		Customizer\Sections\Product_Single::GALLERY_SIZE,
		Customizer\Sections\Product_Single::ENABLE_ZOOM,
		Customizer\Sections\Cart::ENABLE_MINI_CART,
		'bigcommerce_flushed_rewrites',
		md5( 'bc_webhook_product_updatestore/product/*' ), // removed in version 2.1
		md5( 'bc_webhook_password_product_updatestore/product/*' ), // removed in version 2.1
		Webhook::WEBHOOKS_OPTION,
		Webhook::AUTH_KEY_OPTION,
		Settings\Sections\Cart::OPTION_AJAX_CART,
		Settings\Sections\Import::BATCH_SIZE,
		Term_Import::STATE_OPTION,
	];

	foreach ( $options as $option ) {
		\delete_option( $option );
	}
}

/**
 * Delete all customer related usermeta data
 */
function delete_user_meta_data() {
	global $wpdb;
	$customer_id_meta_key = $wpdb->get_blog_prefix() . Customer::CUSTOMER_ID_META;

	// all meta-keys => value to be deleted
	$meta_keys = [
		$customer_id_meta_key                => '',
		Nav_Items_Meta_Box::USER_INITIALIZED => '',
		User_Profile_Settings::SYNC_PASSWORD => '',
	];

	// Delete metadata
	foreach ( $meta_keys as $key => $value ) {
		\delete_metadata( 'user', null, $key, $value, true );
	}
}

function flush_rewrites() {
	\unregister_post_type( Product::NAME );
	foreach ( get_taxonomy_list() as $tax ) {
		\unregister_taxonomy( $tax );
	}
	\flush_rewrite_rules();
}

\BigCommerce\Uninstall\uninstall();
