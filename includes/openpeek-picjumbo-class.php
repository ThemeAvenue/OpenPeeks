<?php
class OpenPeek_Picjumbo extends OpenPeek_Kimonolabs {
	
	protected function map_fields( $content ) {

		$clean = array();

		foreach ( $content as $key => $image ) {

			$current                = array();
			$current['name']        = $image['name'];
			$current['source']      = $image['link'];
			$current['preview_url'] = $image['img'];
			$current['link']        = str_replace( '-1300x866', '', $image['img'] );

			array_push( $clean, $current );

		}

		return $clean;

	}

}