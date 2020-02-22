<?php

namespace BigCommerce\Settings;


use BigCommerce\Api\v3\Api\SitesApi;
use BigCommerce\Api\v3\ApiException;

class Site_Update {

	/**
	 * @var SitesApi
	 */
	private $sites_api;
	private $channel;

	public function __construct( SitesApi $sites_api, $channel ) {
		$this->sites_api = $sites_api;
		$this->channel   = $channel;
	}

	/**
	 * @param $option
	 *
	 * @return mixed
	 *
	 * @filter pre_update_option_siteurl
	 */
	public function update_bc_channel_site_url( $option ) {
		if ( ! $this->channel ) {
			return $option;
		}

		try {
			$channel_site = $this->sites_api->getChannelSite( $this->channel );
		} catch ( ApiException $exception ) {
			return $option;
		}

		$site_data = $channel_site->getData();
		$site_data->setUrl( $option );

		try{
			$this->sites_api->putChannelSite( $this->channel, $site_data );
		} catch ( ApiException $exception ) {
			return $option;
		}

		return $option;
	}

	/**
	 * @param $url
	 *
	 * @return string
	 *
	 * @filter bigcommerce/settings/channel_site_url
	 */
	public function get_bc_channel_site_url( $url ) {
		try {
			$channel_site = $this->sites_api->getChannelSite( $this->channel );
		} catch ( ApiException $exception ) {
			return $url;
		}

		return $channel_site->getData()->getUrl();
	}
}