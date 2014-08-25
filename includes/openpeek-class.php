<?php
/**
 * OpenPeek API.
 *
 * Requires PHP 5.3.0 at least.
 */
class OpenPeek {

	/**
	 * List of sites to get images from.
	 * 
	 * @var array
	 */
	public $providers = array();

	/**
	 * Default list of fields used.
	 * 
	 * @var array
	 */
	protected $fields = array(
		'name',
		'source',
		'link',
		'tags'
	);

	/**
	 * List of all the images gathered from all sites.
	 * 
	 * @var array
	 */
	protected $library = array();

	public function __construct( $cache = null, $cache_expiry = 86400 ) {

		/* Load Kimonolabs API */
		require_once( realpath( dirname( __FILE__) ) . '/openpeek-kimonolabs-class.php' );

		/* Set cache configuration */
		$this->cache        = is_null( $cache ) ? realpath( dirname( __FILE__) ) . '/cache/library.json' : $cache;
		$this->cache_expiry = $cache_expiry;

	}

	/**
	 * Add a new image provider.
	 * 
	 * @param string $provider The provider name
	 * @param array  $args     A list of arguments used in the provider API
	 */
	public function add_api( $provider, $args ) {
		array_push( $this->providers, array( 'provider' => $provider, 'args' => $args ) );
	}

	/**
	 * Provision the library by querying all APIs.
	 * 
	 * @return void
	 */
	protected function provision() {

		$includes = realpath( dirname( __FILE__) ) . '/';

		foreach ( $this->providers as $provider ) {

			$classname = str_replace( array( '-', '_' ), ' ', $provider['provider'] );
			$classname = ucwords( $classname );
			$classname = 'OpenPeek_' . str_replace( ' ', '', $classname );
			$class     = null;
			$name      = $provider['provider'];
			$filename  = "openpeek-$name-class.php";

			if ( !file_exists( $includes . $filename ) )
				return;

			/* Load the class */
			require_once( $includes . $filename );

			/* Dynamically instanciate */
			$class = new $classname( $provider['args'] );

			/* Get the pics */
			$results = $class->get_results();

			/* Add them to the library */
			$this->update_library( $results );

		}

	}

	/**
	 * Whitelist a new field.
	 * 
	 * @param  string $field Field name
	 */
	protected function map_field( $field ) {
		array_push( $this->fields, $field );
	}

	/**
	 * Get fields whitelist.
	 * 
	 * @return array List of mapped fields
	 */
	protected function get_mapped_fields() {
		return $this->fields;
	}

	protected function update_library( $content ) {

		if ( empty( $content ) )
			return false;

		$fields = $this->get_mapped_fields();

		foreach ( $content as $key => $image ) {

			foreach ( $image as $field => $value ) {

				if ( !in_array( $field, $fields ) )
					unset( $content[$key][$image][$field] );

			}

			array_push( $this->library, $content[$key] );

		}

	}

	public function get_library() {

		/* Get library from the cache */
		if ( file_exists( $this->cache ) && time()-filemtime( $this->cache ) <= $this->cache_expiry ) {

			$contents = file_get_contents( $this->cache );
			$contents = json_decode( $contents, true );

			/* Check if the content was correctly decoded */
			if ( is_array( $contents ) ) {
				return $contents;
			} else {
				return false;
			}

		} else {

			$this->provision();
			$this->cache_library( $this->library );
			return $this->library;

		}

	}

	public function cache_library( $contents ) {

		$file     = $this->cache;
		$contents = json_encode( $contents );

		file_put_contents( $file, $contents );

	}

}