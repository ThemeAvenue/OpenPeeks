<?php
/**
 * OpenPeek API.
 *
 * Requires PHP 5.3.0 at least.
 */
class OpenPeek {

	protected $app_id  = null;
	protected $api_key = null;

	public function __construct( $app_id = false, $api_key = false ) {

		$this->app_id  = $app_id;
		$this->api_key = $api_key;
		$this->route   = 'https://www.kimonolabs.com/api/';
		$this->cache   = __FILE__ . '/cache/photos_list.json';

		// CHECK CREDENTIALS
		
		$this->load_photo_providers();

	}

	protected function load_photo_providers() {

		$includes = realpath( dirname( __FILE__) );
		$files    = scandir( $includes );

		foreach ( $files as $file ) {

			if ( in_array( $file, array( '.', '..' ) ) )
				continue;

			$ext       = pathinfo( $file, PATHINFO_EXTENSION );
			$filename  = str_replace( ".$ext", '', $file );
			$breakdown = explode( '-', $filename );

			if ( in_array( 'class', $breakdown ) ) {

				$key = array_search( 'class', $breakdown );
				unset( $breakdown[$key] );

			}

			if ( is_array( $breakdown ) && count( $breakdown ) >= 2 && 'openpeek' == $breakdown[0] ) {

				$key = array_search( 'openpeek', $breakdown );
				unset( $breakdown[$key] );
				$breakdown = array_map( 'ucwords', $breakdown );

				/**
				 * Get class name.
				 *
				 * Get the provider class name based on the filename structure
				 * defined in the OpenPeeks GihHub repo.
				 *
				 * @link https://github.com/ThemeAvenue/OpenPeeks
				 * @var  string Class name
				 */
				$classname = implode( '_', $breakdown );

				if( class_exists( $classname ) )
					new $classname();

			}

		}

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

		return $body;

	}

	protected function get_library() {

		/* Get library from the cache */
		if( file_exists( $this->cache ) ) {

			$contents = file_get_contents( $this->cache );
			$contents = json_decode( $contents );

			/* Check if the content was correctly decoded */
			if ( JSON_ERROR_NONE !== json_last_error() ) {
				return $contents;
			}

		}

		/* Otherwise query all APIs */

		// CACHE RESULTS

	}

	protected function cache_library( $contents ) {

		$file     = $this->cache;
		$contents = json_encode( $contents );

		file_put_contents( $file, $contents );

	}

}