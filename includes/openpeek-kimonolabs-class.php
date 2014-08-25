<?php
class OpenPeek_Kimonolabs extends OpenPeek {

	protected $app_id  = null;
	protected $api_key = null;

	public function __construct( $args = array() ) {

		$this->app_id  = isset( $args['app_id'] ) ? $args['app_id'] : false;
		$this->api_key = isset( $args['api_key'] ) ? $args['api_key'] : false;
		$this->route   = 'https://www.kimonolabs.com/api/';

		if ( false === $this->app_id || false === $this->api_key )
			return false;

	}

	/**
	 * Get full URL to query.
	 *
	 * Get the full query URL including application ID
	 * and API key.
	 *
	 * @since  0.1.0
	 * @return string URL to query
	 */
	protected function get_endpoint_url() {

		$args = http_build_query( array( 'apikey' => $this->api_key ) );
		$base = $this->route . $this->app_id;
		$url  = "$base?$args";

		return $url;
	}

	public function get_results() {

		$url      = $this->get_endpoint_url();
		$response = file_get_contents( $url );
		$body     = json_decode( $response, TRUE );

		/* Check if the content was correctly decoded */
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return false;
		}

		if ( isset( $body['results']['collection1'] ) )
			return $this->map_fields( $body['results']['collection1'] );

		else
			return array();

	}

}