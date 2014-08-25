<?php
class OpenPeek_LittleVisuals extends OpenPeek_Kimonolabs {
	
	protected function map_fields( $content ) {

		$clean = array();

		foreach ( $content as $key => $image ) {

			$current           = array();
			$current['name']   = '';
			$current['source'] = $image['image']['href'];
			$current['link']   = $image['image']['src'];
			$current['tags']   = explode( "\n", $image['tags'] );

			array_push( $clean, $current );

		}

		return $clean;

	}

}