<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.6
Description: Display clickable deviation thumbs from deviantART.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/projects/deviant-thumbs.html

Copyright (C) 2008 scribu.net (scribu AT gmail DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

define('DTHUMBS_INLINE', TRUE);		// Set to FALSE if you don't want to use inline thumbs.
define('DTHUMBS_CAROUSEL', TRUE);	// Set to FALSE if you don't want to use carousels at all.
define('DTHUMBS_WIDGET', TRUE);		// Set to FALSE if you don't want to use the widget.

define('DTHUMBS_CACHE_DIR', dirname(__FILE__) . '/cache');

class deviantThumbs {
	static function generate($query, $count, $rand, $cache, $before, $after) {
		$cache *= 3600;

		$file = DTHUMBS_CACHE_DIR . '/' . urlencode($query) . '.txt';

		if ( file_exists($file) && (time()-filemtime($file) <= $cache) )
			$thumbs = self::get_from_cache($file);
		else
			$thumbs = self::get_from_pipe($query, $count, $file);

		if ( $rand )
			shuffle($thumbs);

		$thumbs_nr = count($thumbs);
		for ( $i=0; $i<$count && $i<$thumbs_nr; $i++ )
			$output .= $before . $thumbs[$i] . $after . "\n";

		return $output;
	}

	private function get_from_pipe($query, $count, $file) {
		$pipeurl = 'http://pipes.yahoo.com/pipes/pipe.run?_id=627f77f83f199773c5ce8a150a1e5977&_render=php';
		$pipeurl .= '&query=' . urlencode($query);

		$data = unserialize(file_get_contents($pipeurl));

		// Extract thumb list
		$thumbs_nr = count($data['value']['items']);
		for ($i=0; $i<$thumbs_nr; $i++) {
			$tmp = $data['value']['items'][$i]['content'];
			$thumbs[] = str_replace(' rel="nofollow" target="_blank"', '', $tmp);
		}

		self::update_cache($thumbs, $file);

		return $thumbs;
	}

	private function update_cache($thumbs, $file) {
		$fp = @fopen($file, "w");

		if ( FALSE === $fp )
			return;

		$data = implode("\n", $thumbs);

		fwrite($fp, $data);
		fclose($fp);
	}

	private function get_from_cache($file) {
		return explode("\n", file_get_contents($file) );
	}
}

// Template tag
function deviant_thumbs($query, $count = 3, $rand = FALSE, $cache = 6, $before = '<li>', $after = '</li>') {
	echo deviantThumbs::generate($query, $count, $rand, $cache, $before, $after);
}

// Init
function deviant_thumbs_init() {
	if ( !is_dir(DTHUMBS_CACHE_DIR) )
		@mkdir(DTHUMBS_CACHE_DIR);

	$inc = dirname(__FILE__) . '/inc';

	if ( DTHUMBS_INLINE )
		include_once "$inc/inline.php";

	if ( DTHUMBS_CAROUSEL )
		include_once "$inc/carousel/carousel.php";

	if ( DTHUMBS_WIDGET ) {
		include_once "$inc/widget.php";
		$widget = new deviantThumbsWidget();
		register_activation_hook(__FILE__, array(&$widget, 'install'));
	}
}

deviant_thumbs_init();

