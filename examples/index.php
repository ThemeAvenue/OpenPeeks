<?php
require( '../config.php' );
require( '../includes/openpeek-class.php' );

/* Load OpenPeeks class */
$pics = new OpenPeek( '../cache/library.json' );

/* Add an API */
// $pics->add_api( 'kaboompics', array( 'app_id' => 'dumrmhu4', 'api_key' => '34f710899fb2424aeb213c881ff10109' ) );
$pics->add_api( 'picjumbo', array( 'app_id' => '52f46muq', 'api_key' => '34f710899fb2424aeb213c881ff10109' ) );
// $pics->add_api( 'little-visuals', array( 'app_id' => '8l9t0l3y', 'api_key' => '34f710899fb2424aeb213c881ff10109' ) );

/* Fetch the library */
$results = $pics->get_library();
// echo '<pre>';
// print_r( $results );
// echo '</pre>';
// exit;

/* Update the DB */
// foreach( $results as $key => $result ) {
// 	$opdb->add_image( $result, 'kaboompics' );
// }

/* Search for an image */
echo '<h2>Search for &laquo;corn&raquo;</h2>';
$search = $opdb->search( 'corn' );
echo '<pre>';
print_r( $search );
echo '</pre>';

/* Get image tags */
echo '<h2>Get tags for image <code>15</code></h2>';
$tags = $opdb->get_image_tags( 15 );
echo '<pre>';
print_r( $tags );
echo '</pre>';

/* Get the 10 latest images */
echo '<h2>Last 10 images</h2>';
$images = $opdb->get_images();
echo '<pre>';
print_r( $images );
echo '</pre>';