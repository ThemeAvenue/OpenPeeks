<?php
require( '../includes/openpeek-class.php' );
$pics = new OpenPeek( 'dumrmhu4', '34f710899fb2424aeb213c881ff10109' );
$results = $pics->get_results();
print_r( $results );
$pics->cache_results( $results );