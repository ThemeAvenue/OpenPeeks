<?php
class OpenPeek_Kaboompics extends OpenPeek_Kimonolabs {
	
	protected function map_fields( $content ) {

		$clean = array();

		foreach ( $content as $key => $image ) {

			$current                = array();
			$current['name']        = $image['name']['text'];
			$current['source']      = $image['name']['href'];
			$current['preview_url'] = $image['img'];
			$current['link']        = $image['link'];
			$current['tags']        = explode( '#', trim( str_replace( 'TAGS:', '', $image['tags'] ) ) );

			array_push( $clean, $current );

		}

		return $clean;

	}

}