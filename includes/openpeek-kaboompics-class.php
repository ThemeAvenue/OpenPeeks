<?php
class OpenPeek_Kaboompics extends OpenPeek_Kimonolabs {
	
	protected function map_fields( $content ) {

		$clean = array();

		foreach ( $content as $key => $image ) {

			$current           = array();
			$current['name']   = $image['name']['text'];
			$current['source'] = $image['name']['href'];
			$current['link']   = $image['img'];
			$current['tags']   = explode( '#', trim( str_replace( 'TAGS:', '', $image['tags'] ) ) );

			array_push( $clean, $current );

		}

		return $clean;

	}

}