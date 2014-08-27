<?php
class OPDB {

	/**
	 * Name of the "images" table.
	 * 
	 * @var string
	 */
	public $images = 'images';

	/**
	 * Name of the "tags" table.
	 * 
	 * @var string
	 */
	public $tags = 'tags';

	/**
	 * Name of the "relationships" table.
	 * 
	 * @var string
	 */
	public $relationship = 'relationship';

	/**
	 * The images library.
	 *
	 * This variable contains the entire images library
	 * gathered from the database after the class is
	 * instanciated.
	 * 
	 * @var array
	 */
	public $library = array();

	/**
	 * List of all available tags.
	 * 
	 * @var array
	 */
	public $tags_library = array();

	public function __construct() {

		/**
		 * Conect to the database.
		 */
		$this->opdb = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		
		if ( $this->opdb->connect_errno ) {
			echo "Failed to connect to MySQL: (" . $this->opdb->connect_errno . ") " . $this->opdb->connect_error;
		}

		/**
		 * Get the library
		 */
		$this->library = $this->get_images( 'all' );

	}

	/**
	 * Add image to the library.
	 *
	 * Takes an image with all its parameters are add it
	 * to the database if it doesn't exist. If the record already exists,
	 * it will be updated if $update is set to true.
	 * 
	 * @param array  $image  Array containing all of the image parameters
	 * @param string $site   Name of the site the image comes from
	 * @param bool   $update Wether or not to update an existing record
	 */
	public function add_image( $image, $site, $update = false ) {

		/**
		 * Check if the parameter is in the correct format.
		 */
		if ( !is_array( $image ) || empty( $image ) )
			return false;

		extract( $image );

		/**
		 * Escape all variables
		 */
		$name   = $this->opdb->real_escape_string( $name );
		$site   = $this->opdb->real_escape_string( $site );
		$source = filter_var( $source, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED );
		$link   = filter_var( $link, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED );

		if ( is_array( $tags ) ) {

			foreach ( $tags as $key => $tag ) {

				$tags[$key] = $this->opdb->real_escape_string( $tag );

			}

		}

		/**
		 * Get the image hash
		 */
		$hash = md5( $link );

		/**
		 * Get library
		 */
		$library = $this->get_images( 'all', true );

		/* The image already exists in DB */
		if ( array_key_exists( $hash, $library ) ) {

			/* Let's update the image */
			if ( true === $update ) {

			}

		} else {

			/* Insert the image */
			$image_id = $this->insert_image( array( 'hash' => $hash, 'name' => $name, 'source' => $source, 'link' => $link, 'site' => $site ) );

			/* Insert tags */
			if ( is_array( $tags ) ) {

				foreach ( $tags as $key => $tag ) {

					$tag_id = $this->insert_tag( $tag );

					/* Create the relationship */
					$this->create_relationship( $image_id, $tag_id );

				}

			}

		}

	}

	/**
	 * Insert image in the database.
	 * 
	 * @param  array $values Values to insert
	 * @return integer       ID of the image inserted
	 */
	public function insert_image( $values ) {

		/**
		 * Sanitize values
		 */
		$name   = $this->opdb->real_escape_string( $values['name'] );
		$site   = $this->opdb->real_escape_string( $values['site'] );
		$source = filter_var( $values['source'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED );
		$link   = filter_var( $values['link'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED );

		/**
		 * Add date
		 */
		if ( !isset( $values['time'] ) || '' == $values['time'] )
			$values['time'] = date( 'Y-m-d H:i:s' );

		/**
		 * Add site
		 */
		if ( !isset( $values['site'] ) )
			$values['site'] = '';

		extract( $values );

		$request  = "INSERT INTO $this->images (hash, date_added, name, source, link, site) VALUES ('$hash', '$time', '$name', '$source', '$link', '$site')";
		$insert   = $this->opdb->query( $request );

		if ( false === $insert )
			return false;

		else
			return $this->opdb->insert_id;

	}

	/**
	 * Get image from the database.
	 * 
	 * @param  array $params ID of the image
	 * @return array         Image data
	 */
	public function get_image( $id ) {

		$id      = intval( $id );
		$image   = array();
		$results = $this->opdb->query( "SELECT * FROM $this->images WHERE image_id='$id' LIMIT 0,1" );

		while ( $row = $results->fetch_assoc() ) {
			$image = $row;
		}

		return $image;

	}

	/**
	 * Get image tags.
	 *
	 * Get the tags for a specific image.
	 * 
	 * @param  integer $image_id ID of the image
	 * @return array             List of tags
	 */
	public function get_image_tags( $image_id ) {

		/* Sanitize image ID */
		$image_id = intval( $image_id );

		/* Query DB */
		$query   = "SELECT * FROM $this->relationship WHERE image_id='$image_id'";
		$results = $this->opdb->query( $query );

		/* Get tags */
		$tag_ids = array();
		$tags    = array();

		while ( $row = $results->fetch_assoc() ) {
			$tag_ids[] = $row['tag_id'];
		}

		if ( empty( $tag_ids ) )
			return array();

		foreach ( $tag_ids as $id ) {

			$gettags = $this->opdb->query( "SELECT tag FROM $this->tags WHERE tag_id='$id'" );

			while ( $row = $gettags->fetch_assoc() ) {
				if ( '' != $row['tag'] )
					$tags[] = $row['tag'];
			}

		}

		return $tags;

	}

	/**
	 * Get images.
	 * 
	 * @return array Array of images data
	 */
	public function get_images( $params = array(), $key_hash = false ) {

		/* SQL request */
		$request = '';

		if ( is_array( $params ) ) {

			$defaults = array(
				'limit'   => 10,
				'offset'  => 0,
				'site'    => 'any',
				'orderby' => 'date_added',
				'order'   => 'ASC'
			);

			extract( array_merge( $defaults, $params ) );

			/* Check offset/limit logic */
			if ( $offset >= $limit )
				return 'The offset is bigger than the limit!';

			/* Default "where" argument to catch all */
			$where = '1';

			if ( 'any' != $site )
				$where = "site='$site'";

			$request = "SELECT * FROM $this->images WHERE $where ORDER BY $orderby $order LIMIT $offset,$limit";

		} else {

			if ( 'all' == $params ) {

				$request = "SELECT * FROM $this->images WHERE 1";

			}

		}

		if ( '' == $request )
			return array();

		$results = $this->opdb->query( $request );
		$library = array();

		while ( $row = $results->fetch_assoc() ) {

			$data = array(
				'name'   => $row['name'],
				'source' => $row['source'],
				'link'   => $row['link'],
			);

			if ( true === $key_hash ) {
				$library[$row['hash']] = $data;
			} else {
				$library[] = $data;
			}
		}

		return $library;

	}

	public function delete_image() {

	}

	public function insert_tag( $tag, $cache = false ) {

		if ( empty( $this->tags_library ) || false === $cache ) {

			$tags = $this->opdb->query( "SELECT * FROM $this->tags WHERE 1" );

			while ( $row = $tags->fetch_assoc() ) {
				$this->tags_library[$row['tag_id']] = $row['tag'];
			}

		}

		/* Return tag ID if already exists */
		if ( in_array( $tag, $this->tags_library ) )
			return array_search( $tag, $this->tags_library );
		
		/* Insert the new tag */
		$insert = $this->opdb->query( "INSERT INTO $this->tags ( tag ) VALUES ( '$tag' )" );

		if ( false === $insert )
			return false;

		else
			return $this->opdb->insert_id;

	}

	public function delete_tag() {

	}

	/**
	 * Create image / tag relationship.
	 * 
	 * @param  integer $image_id ID of the image
	 * @param  integer $tag_id   ID of the tag
	 * @return mixed             Result
	 */
	public function create_relationship( $image_id, $tag_id ) {

		$insert = $this->opdb->query( "INSERT INTO $this->relationship ( image_id, tag_id ) VALUES ( '$image_id', '$tag_id' )" );

		if ( false === $insert )
			return false;

		else
			return $this->opdb->insert_id;

	}

	public function delete_relationship() {

	}

	/**
	 * Search for an image.
	 *
	 * The search function takes a search term and
	 * looks into the database for images with a matching
	 * name or tags.
	 * 
	 * @param  string $term Search term
	 * @return array        Search results
	 */
	public function search( $term ) {

		/* Sanitize the term */
		$term = $this->opdb->real_escape_string( $term );

		/* Query DB */
		$query   = "SELECT * FROM $this->images WHERE name LIKE '%$term%'";
		$results = $this->opdb->query( $query );

		/* Store the response */
		$response = array();

		/* Organize results */
		while ( $row = $results->fetch_assoc() ) {
			$response[$row['image_id']] = $row;
		}

		/**
		 * Now let's search in the tags too
		 */
		$tags_query = $this->opdb->query( "SELECT tag_id FROM $this->tags WHERE tag LIKE '%$term%'" );

		/* Then get the relationships for each corresponding tag */
		while ( $row = $tags_query->fetch_assoc() ) {

			$tag_id    = $row['tag_id'];
			$rel_query = $this->opdb->query( "SELECT image_id FROM $this->relationship WHERE tag_id='$tag_id'" );

			while ( $subrow = $rel_query->fetch_assoc() ) {

				if ( !array_key_exists( $subrow['image_id'], $response ) ) {

					$response[$subrow['image_id']] = $this->get_image( $subrow['image_id'] );

				}

			}

		}

		return $response;

	}

}