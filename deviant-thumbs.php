<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.6.2b
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
define('DTHUMBS_CAROUSEL', TRUE);	// Set to FALSE if you don't want to use the carousel at all.
define('DTHUMBS_WIDGET', TRUE);		// Set to FALSE if you don't want to use the widget.

abstract class deviantThumbs {
	public function init() {
		$wud = wp_upload_dir();
		define('DTHUMBS_CACHE_DIR', $wud['basedir'].'/deviant-thumbs');

		if ( !is_dir(DTHUMBS_CACHE_DIR) )
			@mkdir(DTHUMBS_CACHE_DIR);

		$inc = dirname(__FILE__) . '/inc';

		if ( DTHUMBS_INLINE )
			include_once "$inc/inline.php";

		if ( DTHUMBS_CAROUSEL )
			include_once "$inc/carousel/carousel.php";

		if ( DTHUMBS_WIDGET )
			include_once "$inc/widget.php";

		register_deactivation_hook(__FILE__, create_function('', 'deviantThumbs::clear_cache();'));
	}

	public function generate($query, $count, $rand, $cache, $before, $after) {
		$cache *= 3600;

		$file = DTHUMBS_CACHE_DIR . '/' . urlencode($query) . '.txt';

		if ( file_exists($file) && (time()-filemtime($file) <= $cache) )
			$thumbs = explode("\n", file_get_contents($file));
		else
			$thumbs = self::get_from_pipe($query, $count, $file);

		if ( $rand )
			shuffle($thumbs);

		$thumbs_nr = count($thumbs);
		for ( $i=0; $i<$count && $i<$thumbs_nr; $i++ )
			$output .= $before . $thumbs[$i] . $after . "\n";

		return $output;
	}

	public function clear_cache() {
		if ( !is_dir(DTHUMBS_CACHE_DIR) )
			return;

		$dir_handle = opendir(DTHUMBS_CACHE_DIR);

		while ( $file = readdir($dir_handle) )
			if ( $file != "." && $file != ".." )
				unlink(DTHUMBS_CACHE_DIR."/".$file);

		closedir($dir_handle);
		@rmdir(DTHUMBS_CACHE_DIR);
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

		file_put_contents($file, implode("\n", $thumbs));

		return $thumbs;
	}
}

// Init
deviantThumbs::init();

// Template tag
function deviant_thumbs($query, $count = 3, $rand = FALSE, $cache = 6, $before = '<li>', $after = '</li>') {
	echo deviantThumbs::generate($query, $count, $rand, $cache, $before, $after);
}

